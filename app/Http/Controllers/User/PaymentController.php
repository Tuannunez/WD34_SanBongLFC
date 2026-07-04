<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // 1. Hiển thị trang thanh toán
    public function showPaymentPage($bookingId)
{
    // 1. Lấy thông tin đơn hàng
    $booking = DB::table('bookings')
    ->where('id', $bookingId)
    ->where('user_id', Auth::id())
    ->first(); // Thay đổi ở đây

    if (!$booking) {
        abort(404, 'Đơn đặt sân không tồn tại.');
    }

    // Lấy danh sách phương thức thanh toán độc lập
    $paymentMethods = DB::table('payment_methods')->where('status', 1)->get();

    $bankId = "MB"; 
    $accountNo = "0123456789"; 
    $template = "qr_only";
    $description = "THANH TOAN SAN BONG " . ($booking->code ?? $bookingId);
    $amount = $booking->total_price ?? $booking->price ?? 0;

    $qrCodeUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo=" . urlencode($description);

    // BỎ HẲN HÀM COMPACT() CŨ VÀ THAY BẰNG DẠNG MẢNG NÀY ĐỂ ÉP BUỘC TRUYỀN BIẾN:
    return view('user.payment.index', [
        'booking' => $booking,
        'paymentMethods' => $paymentMethods,
        'qrCodeUrl' => $qrCodeUrl
    ]);
}

    // 2. Xử lý khi người dùng chọn phương thức và bấm xác nhận
   // 2. Xử lý khi người dùng chọn phương thức và bấm xác nhận
    public function processPayment(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'payment_method_id' => 'required',
        ]);

        // Lấy thông tin đơn đặt sân và phương thức thanh toán
        $booking = DB::table('bookings')->where('id', $request->booking_id)->where('user_id', Auth::id())->first();
        $method = DB::table('payment_methods')->where('id', $request->payment_method_id)->first();

        if (!$booking || !$method) {
            return back()->with('error', 'Thông tin đơn hàng hoặc phương thức thanh toán không tồn tại.');
        }

        // 🔴 Đã sửa thành total_amount theo chuẩn database của bạn
        $amount = $booking->total_amount ?? 0;

        try {
            DB::beginTransaction();

            $methodCode = strtoupper(trim($method->code));

            // TH1: Khách chọn trả tiền mặt trực tiếp tại sân
            if ($methodCode === 'CASH' || $methodCode === 'TIEN_MAT' || $methodCode === 'TIENMAT') {
                
                DB::table('payments')->insert([
                    'booking_id' => $booking->id,
                    'payment_method_id' => $method->id,
                    'amount' => $amount,
                    'transaction_code' => 'CASH_' . strtoupper(uniqid()),
                    'status' => 'unpaid', // Khớp với enum('unpaid', 'paid'...) của bảng payments
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 🔴 Đã loại bỏ cột lỗi payment_status, chỉ update cột status hợp lệ
                DB::table('bookings')->where('id', $booking->id)->update([
                    'status' => 'confirmed', // Khớp với enum của bảng bookings
                    'updated_at' => now()
                ]);

                DB::commit();
                return redirect()->route('user.bookings.index')->with('success', 'Đặt sân thành công! Vui lòng thanh toán tiền mặt khi đến sân.');
            } 

            // TH2: Khách chọn Chuyển khoản qua mã QR Ngân hàng
            else if ($methodCode === 'BANK_TRANSFER' || $methodCode === 'BANK' || $methodCode === 'CHUYEN_KHOAN' || $methodCode === 'CHUYENKHOAN') {
                
                DB::table('payments')->insert([
                    'booking_id' => $booking->id,
                    'payment_method_id' => $method->id,
                    'amount' => $amount,
                    'transaction_code' => 'QR_' . time(),
                    'status' => 'unpaid', 
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 🔴 Đã loại bỏ cột lỗi payment_status, chỉ update cột status hợp lệ
                DB::table('bookings')->where('id', $booking->id)->update([
                    'status' => 'pending', // Đợi admin kiểm tra giao dịch banking
                    'updated_at' => now()
                ]);

                DB::commit();
                return redirect()->route('user.bookings.index')->with('success', 'Yêu cầu thanh toán đang được xử lý. Hệ thống sẽ xác nhận khi nhận được tiền.');
            }

            else {
                DB::rollBack();
                return back()->with('error', 'Mã phương thức thanh toán trong database không hợp lệ: ' . $method->code);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            // Nếu muốn test xem còn lỗi gì khác thì mở dòng dưới ra, nếu chạy mượt thì cứ đóng lại nhé
            // dd($e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage());
        }
    }
}