<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

final class BookingInvoiceController extends Controller
{
    public function __invoke(
        Request $request,
        Booking $booking,
    ): View {
        abort_unless(
            (int) $booking->user_id === (int) $request->user()->id,
            403,
            'Bạn không có quyền xem hóa đơn của đơn này.',
        );

        $this->loadInvoiceRelations($booking);

        return view(
            'user.bookings.invoice',
            compact('booking'),
        );
    }

    private function loadInvoiceRelations(Booking $booking): void
    {
        $relations = [
            'user',
            'bookingDetails.field',
            'bookingDetails.timeSlot',
            'bookingServices.service',
            'payments.paymentMethod',
        ];

        foreach ($relations as $relation) {
            try {
                $booking->loadMissing($relation);
            } catch (Throwable) {
                // Dự án có thể chưa khai báo một quan hệ tùy chọn.
                // View hóa đơn vẫn dùng dữ liệu dự phòng.
            }
        }
    }
}
    