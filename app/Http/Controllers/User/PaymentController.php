<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    // 1. Hiển thị trang chọn phương thức thanh toán
    public function showPaymentPage($booking_id)
    {
        $booking = DB::table('bookings')->where('id', $booking_id)->first();
        if (!$booking) abort(404);

        $paymentMethods = DB::table('payment_methods')->where('status', 1)->get();

        return view('user.payment.index', compact('booking', 'paymentMethods'));
    }

    // 2. Xử lý tạo liên kết thanh toán VNPay gửi đi
    public function processPayment(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $booking = DB::table('bookings')->where('id', $bookingId)->first();
        
        if (!$booking) {
            return back()->withErrors(['error' => 'Không tìm thấy đơn đặt sân.']);
        }

        // =========================================================================
        // KIỂM TRA THỜI GIAN GIỮ SÂN 5 PHÚT (ĐỒNG BỘ MÚI GIỜ) TRƯỚC KHI SANG VNPAY
        // =========================================================================
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

        $methodId = $request->input('payment_method_id');
        $method = DB::table('payment_methods')->where('id', $methodId)->first();

        if (!$method) {
            return back()->withErrors(['error' => 'Vui lòng chọn phương thức thanh toán hợp lệ.']);
        }

        // =========================================================================
        // PHÂN CHIA SỐ TIỀN THEO total_price CHUẨN TRONG DB CỦA BẠN
        // =========================================================================
        $totalPrice = $booking->total_price ?? $booking->total_amount ?? 0;
        $depositPrice = $booking->deposit_amount ?? ($totalPrice * 0.3);

        $methodCode = strtoupper($method->code ?? '');

        if ($methodCode !== 'BANK_TRANSFER' && $methodCode !== 'VNPAY_QR') {
            $amountToPay = $depositPrice;
            $vnp_OrderInfo = "Thanh toan coc 30 phan tram don dat san " . $booking->booking_code;
        } else {
            $amountToPay = $totalPrice;
            $vnp_OrderInfo = "Thanh toan 100 phan tram don dat san " . $booking->booking_code;
        }

        // TÍCH HỢP VNPAY (Sử dụng cấu hình của Nhật)
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

    // 3. Xử lý kết quả VNPay trả về sau khi khách thao tác xong
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
                // =========================================================================
                // CHẶN THANH TOÁN QUÁ HẠN: Kiểm tra nếu thời gian thanh toán quá 5 phút
                // =========================================================================
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
                    $vnpAmountPaid = $request->input('vnp_Amount') / 100;
                    
                    $totalPrice = $booking->total_price ?? $booking->total_amount ?? 0;
                    $depositPrice = $booking->deposit_amount ?? ($totalPrice * 0.3);

                    // =========================================================================
                    // PHÂN BIỆT THEO SỐ TIỀN THỰC TRẢ ĐỂ TRÁNH LỖI CỘT PAYMENT_STATUS TRONG DB
                    // =========================================================================
                    if (abs($vnpAmountPaid - $depositPrice) < 100) {
                        DB::table('bookings')->where('booking_code', $bookingCode)->update([
                            'status' => 'confirmed',
                            'is_deposit_paid' => true,
                            'updated_at' => now()
                        ]);

                        return redirect()->route('user.bookings.index')
                            ->with('success', 'Tuyệt vời! Bạn đã thanh toán thành công 30% tiền cọc. Đơn đặt sân đã được xác nhận!');
                    } else {
                        DB::table('bookings')->where('booking_code', $bookingCode)->update([
                            'status' => 'confirmed',
                            'is_deposit_paid' => true,
                            'updated_at' => now()
                        ]);

                        return redirect()->route('user.bookings.index')
                            ->with('success', 'Tuyệt vời! Bạn đã hoàn tất thanh toán 100% qua cổng VNPay. Đơn đặt sân đã được xác nhận!');
                    }
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
                ->with('error', 'Chữ ký phản hồi không hợp lệ (Lỗi bảo mật bảo mật).');
        }
    }
}