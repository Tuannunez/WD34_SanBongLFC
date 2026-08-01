<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\Bookings\BookingLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingCheckInController extends Controller
{
    public function store(
        Request $request,
        int $booking,
        BookingLifecycleService $lifecycle
    ): RedirectResponse {
        $result = $lifecycle->checkInByUser(
            bookingId: $booking,
            userId: (int) $request->user()->id,
        );

        return match ($result['result']) {
            BookingLifecycleService::RESULT_CHECKED_IN =>
                back()->with(
                    'success',
                    'Check-in thành công lúc '
                    .$result['checked_in_at']->format('H:i:s d/m/Y').'.'
                ),

            BookingLifecycleService::RESULT_ALREADY_CHECKED_IN =>
                back()->with(
                    'success',
                    'Đơn này đã được check-in trước đó.'
                ),

            BookingLifecycleService::RESULT_TOO_EARLY =>
                back()->withErrors([
                    'check_in' =>
                        'Chưa đến thời gian check-in. Bạn có thể check-in từ '
                        .$result['opens_at']->format('H:i d/m/Y').'.',
                ]),

            BookingLifecycleService::RESULT_NO_SHOW =>
                back()->withErrors([
                    'check_in' =>
                        'Bạn đã quá hạn check-in 15 phút. '
                        .'Đơn đã tự động hủy và tiền cọc không được hoàn lại.',
                ]),

            BookingLifecycleService::RESULT_NOT_PAID =>
                back()->withErrors([
                    'check_in' =>
                        'Đơn chưa được thanh toán nên không thể check-in.',
                ]),

            BookingLifecycleService::RESULT_MISSING_SCHEDULE =>
                back()->withErrors([
                    'check_in' =>
                        'Không xác định được ngày hoặc khung giờ của đơn.',
                ]),

            BookingLifecycleService::RESULT_NOT_FOUND =>
                abort(404),

            default =>
                back()->withErrors([
                    'check_in' =>
                        'Đơn không còn đủ điều kiện check-in.',
                ]),
        };
    }
}