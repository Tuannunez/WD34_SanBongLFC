<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Bookings\BookingLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class BookingCheckOutController extends Controller
{
    public function store(
        Request $request,
        int $booking,
        BookingLifecycleService $lifecycle,
    ): RedirectResponse {
        $result = $lifecycle->checkOutEarlyByUser(
            bookingId: $booking,
            userId: (int) $request->user()->id,
        );

        return match ($result['result']) {
            BookingLifecycleService::RESULT_CHECKED_OUT => back()->with(
                'success',
                'Check-out sớm thành công lúc '
                    .$result['checked_out_at']->format('H:i:s d/m/Y').'.',
            ),
            BookingLifecycleService::RESULT_ALREADY_CHECKED_OUT => back()->with(
                'success',
                'Đơn này đã được check-out trước đó.',
            ),
            BookingLifecycleService::RESULT_NOT_PAID => back()->withErrors([
                'check_out' => 'Đơn chưa có bằng chứng thanh toán hợp lệ nên không thể check-out.',
            ]),
            BookingLifecycleService::RESULT_MISSING_SCHEDULE => back()->withErrors([
                'check_out' => 'Không xác định được khung giờ của đơn. Vui lòng liên hệ hỗ trợ.',
            ]),
            BookingLifecycleService::RESULT_NOT_FOUND => abort(404),
            default => back()->withErrors([
                'check_out' => 'Chỉ có thể check-out sớm khi đơn đang ở trạng thái đã check-in. '
                    .'Sau giờ kết thúc, hệ thống sẽ tự check-out.',
            ]),
        };
    }
}
