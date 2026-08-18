<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PaymentController extends Controller
{
    private function ensurePaymentMethods()
    {
        $paymentMethods = DB::table('payment_methods')->where('status', 1)->get();

        if ($paymentMethods->isNotEmpty()) {
            return $paymentMethods;
        }

        $defaults = [
            [
                'name' => 'Thanh toán tại sân',
                'code' => 'PAY_AT_FIELD',
                'status' => 1,
            ],
            [
                'name' => 'Chuyển khoản / VNPay',
                'code' => 'VNPAY_QR',
                'status' => 1,
            ],
        ];

        foreach ($defaults as $method) {
            DB::table('payment_methods')->updateOrInsert(
                ['code' => $method['code']],
                [
                    'name' => $method['name'],
                    'code' => $method['code'],
                    'status' => $method['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return DB::table('payment_methods')->where('status', 1)->get();
    }

    public function showPaymentPage(int $booking_id)
    {
        $booking = DB::table('bookings')
            ->leftJoin('promotions', 'bookings.promotion_id', '=', 'promotions.id')
            ->where('bookings.id', $booking_id)
            ->select('bookings.*', 'promotions.code as promotion_code', 'promotions.name as promotion_name')
            ->first();
            
        if (!$booking) abort(404);

        $paymentMethods = $this->ensurePaymentMethods();
        $promotions = DB::table('promotions')->where('status', 1)->get();

        return view('user.payment.index', compact('booking', 'paymentMethods', 'promotions'));
    }

    public function processPayment(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $promotionId = $request->input('promotion_id');

        $booking = DB::table('bookings')
            ->where('id', $bookingId)
            ->first();
        
        if (!$booking) {
            return back()->withErrors(['error' => 'Không tìm thấy đơn đặt sân.']);
        }

        $paymentStartedAt = $booking->payment_started_at ?? null;
        if (empty($paymentStartedAt)) {
            $paymentStartedAt = now();
            DB::table('bookings')->where('id', $booking->id)->update([
                'payment_started_at' => $paymentStartedAt
            ]);
        }

        if (($booking->status ?? 'pending') === 'pending') {
            $createdAt = Carbon::parse($booking->created_at);
            $now = Carbon::now();

            if ($createdAt->diffInMinutes($now) >= 5) {
                DB::table('bookings')->where('id', $booking->id)->update([
                    'status' => 'cancelled',
                    'updated_at' => now()
                ]);
                return redirect()->route('user.bookings.index')
                    ->with('error', 'Đơn đặt sân đã quá hạn 5 phút giữ sân và đã bị hủy tự động. Vui lòng đặt lại lịch mới!');
            }
        }

        $originalTotal = (float)($booking->total_amount ?? $booking->final_amount ?? 0);
        if ($originalTotal <= 0) {
            $originalTotal = (float)($booking->price ?? 0) + (float)($booking->service_total ?? 0);
        }

        $discountAmount = 0;

        if (!empty($promotionId)) {
            $promo = DB::table('promotions')->where('id', $promotionId)->first();
            if ($promo) {
                $pTypeVal = $promo->discount_type ?? '';
                $pVal = (float)($promo->discount_value ?? 0);
                $pPercent = (float)($promo->discount_percent ?? 0);
                $pAmount = (float)($promo->discount_amount ?? 0);

                if ($pTypeVal === 'percent' || $pPercent > 0) {
                    $rate = $pPercent > 0 ? $pPercent : $pVal;
                    $discountAmount = $originalTotal * ($rate / 100);
                    if (!empty($promo->max_discount_amount)) {
                        $discountAmount = min($discountAmount, (float)$promo->max_discount_amount);
                    }
                } else {
                    $discountAmount = $pAmount > 0 ? $pAmount : $pVal;
                }
            }
        }

        $discountAmount = min($discountAmount, $originalTotal);
        $finalPrice = max(0, $originalTotal - $discountAmount);
        
        $isMonthly = (($booking->booking_type ?? 'single') === 'monthly');

        if ($isMonthly) {
            $depositPrice = (float)($booking->deposit_amount ?? $finalPrice);
        } else {
            $depositPrice = $finalPrice * 0.3; 
        }

        DB::table('bookings')->where('id', $booking->id)->update([
            'promotion_id' => !empty($promotionId) ? $promotionId : null,
            'discount_amount' => $discountAmount,
            'total_amount' => $finalPrice,
            'final_amount' => $finalPrice,
            'deposit_amount' => $depositPrice,
            'updated_at' => now()
        ]);

        $booking = DB::table('bookings')
            ->leftJoin('promotions', 'bookings.promotion_id', '=', 'promotions.id')
            ->where('bookings.id', $bookingId)
            ->select('bookings.*', 'promotions.code as promotion_code')
            ->first();

        $currentPaid = (float)($booking->paid_amount ?? 0);
        $remainingAmount = max(0, $finalPrice - $currentPaid);

        // QUAN TRỌNG: Nếu đã thanh toán cọc trước đó và còn thiếu tiền, bắt buộc thanh toán phần còn lại
        if ($currentPaid > 0 && $remainingAmount > 0) {
            $amountToPay = $remainingAmount;
            $vnp_OrderInfo = "Thanh toan so tien con thieu don " . $booking->booking_code;
            $paymentTypeToSave = 'remaining';
        } else {
            // Thanh toán lần đầu bình thường
            $paymentTypeToSave = 'deposit';
            if ($isMonthly) {
                $amountToPay = (float)($booking->deposit_amount ?? $booking->total_amount);
                $vnp_OrderInfo = "Thanh toan lich co dinh thang don " . $booking->booking_code;
                $paymentTypeToSave = ($booking->payment_type === 'full') ? 'full' : 'deposit';
            } else {
                $methodId = $request->input('payment_method_id');
                $method = DB::table('payment_methods')->where('id', $methodId)->first();

                if (!$method) {
                    return back()->withErrors(['error' => 'Vui lòng chọn phương thức thanh toán hợp lệ.']);
                }

                $currentDeposit = $booking->deposit_amount ?? ($finalPrice * 0.3);
                $methodCode = strtoupper($method->code ?? '');

                if ($methodCode !== 'BANK_TRANSFER' && $methodCode !== 'VNPAY_QR') {
                    $amountToPay = $currentDeposit;
                    $vnp_OrderInfo = "Thanh toan coc 30% don dat san " . $booking->booking_code;
                    $paymentTypeToSave = 'deposit';
                } else {
                    $amountToPay = $finalPrice;
                    $vnp_OrderInfo = "Thanh toan 100% don dat san " . $booking->booking_code;
                    $paymentTypeToSave = 'full';
                }
            }
        }

        DB::table('bookings')->where('id', $booking->id)->update([
            'payment_type' => $paymentTypeToSave,
            'updated_at' => now()
        ]);

        if (!empty($booking->promotion_code)) {
            $vnp_OrderInfo .= " ma KM " . $booking->promotion_code;
        }

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_TmnCode = "WFWAS3FC"; 
        $vnp_HashSecret = "KLUI7YPP5B9RNXCO2QIYLRKMFZI44CHX"; 
        $vnp_Returnurl = route('vnpay.return');

        $vnp_TxnRef = $booking->booking_code . '_' . time();
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $amountToPay * 100; // Truyền chính xác số tiền còn lại sang VNPay
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();
        if ($vnp_IpAddr === '::1' || empty($vnp_IpAddr)) {
            $vnp_IpAddr = '127.0.0.1';
        }
        // Dùng mốc thời gian cố định từ database để đồng hồ không bị reset khi khách quay lại
        $startTime = Carbon::parse($paymentStartedAt)->format('YmdHis');
        $expireTime = Carbon::parse($paymentStartedAt)->addMinutes(5)->format('YmdHis');

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime,
            "vnp_CurrCode" => "VND",
            "vnp_ExpireDate" => $expireTime,
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_SecureHash = $request->input('vnp_SecureHash');
        $inputData = array();
        foreach ($request->query() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashdata, "KLUI7YPP5B9RNXCO2QIYLRKMFZI44CHX");
        
        if ($secureHash === $vnp_SecureHash) {
            $txnRef = $request->input('vnp_TxnRef');
            $parts = explode('_', $txnRef);
            $bookingCode = $parts[0];

            $booking = DB::table('bookings')->where('booking_code', $bookingCode)->first();

            if ($booking) {
                if (($booking->status ?? 'pending') === 'pending') {
                    $createdAt = Carbon::parse($booking->created_at);
                    $now = Carbon::now();

                    if ($createdAt->diffInMinutes($now) >= 5) {
                        DB::table('bookings')->where('booking_code', $bookingCode)->update([
                            'status' => 'cancelled',
                            'updated_at' => now()
                        ]);

                        return redirect()->route('user.bookings.index')
                            ->with('error', 'Giao dịch thành công nhưng đã quá hạn giữ sân 5 phút! Đơn đặt của bạn đã bị hủy tự động.');
                    }
                }

                if ($request->input('vnp_ResponseCode') == '00') {
                    $totalAmountVal = (float)($booking->total_amount ?? $booking->final_amount ?? 0);
                    $currentPaid = (float)($booking->paid_amount ?? 0);
                    $paidAmountVnpay = (float)($request->input('vnp_Amount') / 100);

                    // Cộng dồn tiền khách đã trả thực tế
                    $newPaidAmount = $currentPaid + $paidAmountVnpay;
                    
                    if ($newPaidAmount > $totalAmountVal) {
                        $newPaidAmount = $totalAmountVal;
                    }

                    $updateData = [
                        'is_deposit_paid' => true,
                        'paid_amount' => $newPaidAmount,
                        'updated_at' => now()
                    ];

                    // Xử lý chuyển trạng thái đơn hàng chuẩn xác theo thực tế
                    if ($newPaidAmount >= $totalAmountVal) {
                        if ($currentPaid > 0) {
                            // Khách thanh toán nốt phần còn thiếu sau khi đã đá xong -> Hoàn thành đơn
                            $updateData['status'] = 'completed';
                        } else {
                            // Trường hợp khách thanh toán trả trước 100% ngay từ đầu
                            $updateData['status'] = 'confirmed';
                        }
                        $updateData['payment_status'] = 'paid';
                    } else {
                        // Mới thanh toán cọc ban đầu -> Giữ ở trạng thái Đã xác nhận để đi đá
                        $updateData['status'] = 'confirmed';
                        $updateData['payment_status'] = 'deposit_paid';
                    }

                    $filtered = [];
                    foreach ($updateData as $col => $val) {
                        if (Schema::hasColumn('bookings', $col)) {
                            $filtered[$col] = $val;
                        }
                    }

                    DB::table('bookings')->where('booking_code', $bookingCode)->update($filtered);

                    $msg = ($newPaidAmount >= $totalAmountVal && $currentPaid > 0)
                        ? 'Thanh toán số tiền còn lại thành công! Đơn hàng đã chuyển sang trạng thái Hoàn thành.'
                        : 'Tuyệt vời! Thanh toán thành công. Đơn đặt sân đã được xác nhận!';

                    return redirect()->route('user.bookings.index')->with('success', $msg);
                } else {
                    return redirect()->route('user.bookings.index')
                        ->with('error', 'Thanh toán không thành công hoặc giao dịch đã bị hủy.');
                }
            } else {
                return redirect()->route('user.bookings.index')
                    ->with('error', 'Không tìm thấy đơn đặt sân tương ứng.');
            }
        } else {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Chữ ký phản hồi không hợp lệ.');
        }
    }
}