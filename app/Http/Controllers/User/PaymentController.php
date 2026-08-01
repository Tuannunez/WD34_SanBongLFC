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

        return view('user.payment.index', compact('booking', 'paymentMethods'));
    }

    public function processPayment(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $booking = DB::table('bookings')
            ->leftJoin('promotions', 'bookings.promotion_id', '=', 'promotions.id')
            ->where('bookings.id', $bookingId)
            ->select('bookings.*', 'promotions.code as promotion_code', 'promotions.name as promotion_name')
            ->first();
        
        if (!$booking) {
            return back()->withErrors(['error' => 'Không tìm thấy đơn đặt sân.']);
        }

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

        $isMonthly = (($booking->booking_type ?? 'single') === 'monthly');

        if ($isMonthly) {
            $amountToPay = $booking->deposit_amount ?? $booking->total_amount;
            $vnp_OrderInfo = "Thanh toan lich co dinh thang don " . $booking->booking_code;
        } else {
            $methodId = $request->input('payment_method_id');
            $method = DB::table('payment_methods')->where('id', $methodId)->first();

            if (!$method) {
                return back()->withErrors(['error' => 'Vui lòng chọn phương thức thanh toán hợp lệ.']);
            }

            $totalPrice = $booking->total_price ?? $booking->total_amount ?? 0;
            $depositPrice = $booking->deposit_amount ?? ($totalPrice * 0.3);
            $methodCode = strtoupper($method->code ?? '');

            if ($methodCode !== 'BANK_TRANSFER' && $methodCode !== 'VNPAY_QR') {
                $amountToPay = $depositPrice;
                $vnp_OrderInfo = "Thanh toan coc 30% don dat san " . $booking->booking_code;
            } else {
                $amountToPay = $totalPrice;
                $vnp_OrderInfo = "Thanh toan 100% don dat san " . $booking->booking_code;
            }
        }

        if (!empty($booking->promotion_code) && (float) ($booking->discount_amount ?? 0) > 0) {
            $vnp_OrderInfo .= " ma KM " . $booking->promotion_code;
        }

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_TmnCode = "WFWAS3FC"; 
        $vnp_HashSecret = "KLUI7YPP5B9RNXCO2QIYLRKMFZI44CHX"; 
        $vnp_Returnurl = route('vnpay.return');

        $vnp_TxnRef = $booking->booking_code . '_' . time();
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $amountToPay * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();
        if ($vnp_IpAddr === '::1' || empty($vnp_IpAddr)) {
            $vnp_IpAddr = '127.0.0.1';
        }

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
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

                if ($request->input('vnp_ResponseCode') == '00') {
                    $updateData = [
                        'status' => 'confirmed',
                        'is_deposit_paid' => true,
                        'payment_status' => 'paid',
                        'updated_at' => now()
                    ];

                    $filtered = [];
                    foreach ($updateData as $col => $val) {
                        if (Schema::hasColumn('bookings', $col)) {
                            $filtered[$col] = $val;
                        }
                    }

                    DB::table('bookings')->where('booking_code', $bookingCode)->update($filtered);

                    return redirect()->route('user.bookings.index')
                        ->with('success', 'Tuyệt vời! Thanh toán qua cổng VNPay thành công. Đơn đặt sân đã được xác nhận!');
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