<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

final class BookingCheckInScannerController extends Controller
{
    /**
     * Hiển thị trang mô phỏng quét QR / nhập mã đơn.
     */
    public function index(Request $request): View
    {
        $booking = null;
        $checkIn = null;
        $lookupError = null;

        if ($request->filled('booking_code')) {
            $data = $request->validate([
                'booking_code' => ['required', 'string', 'max:100'],
            ], [
                'booking_code.required' => 'Vui lòng nhập mã đơn.',
                'booking_code.max' => 'Mã đơn không hợp lệ.',
            ]);

            $booking = $this->findBookingByCode($data['booking_code']);

            if ($booking === null) {
                $lookupError = 'Không tìm thấy đơn đặt sân với mã đã nhập.';
            } else {
                $checkIn = $this->evaluateCheckIn($booking);
            }
        }

        return view('admin.bookings.check-in.index', compact(
            'booking',
            'checkIn',
            'lookupError',
        ));
    }

    /**
     * Nhân viên xác nhận mã đơn và chuyển sân sang đang hoạt động.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'booking_code' => ['required', 'string', 'max:100'],
        ], [
            'booking_code.required' => 'Vui lòng nhập mã đơn.',
            'booking_code.max' => 'Mã đơn không hợp lệ.',
        ]);

        $bookingCode = trim((string) $data['booking_code']);

        /** @var Booking $booking */
        $booking = DB::transaction(function () use ($bookingCode): Booking {
            $codeColumn = $this->bookingCodeColumn();

            if ($codeColumn === null) {
                throw ValidationException::withMessages([
                    'booking_code' => 'Bảng bookings chưa có cột booking_code hoặc code.',
                ]);
            }

            /** @var Booking|null $booking */
            $booking = Booking::query()
                ->where($codeColumn, $bookingCode)
                ->lockForUpdate()
                ->first();

            if ($booking === null) {
                throw ValidationException::withMessages([
                    'booking_code' => 'Không tìm thấy đơn đặt sân với mã đã nhập.',
                ]);
            }

            $booking->load([
                'user',
                'bookingDetails.field',
                'bookingDetails.timeSlot',
            ]);

            $checkIn = $this->evaluateCheckIn($booking);

            if (! $checkIn['can_check_in']) {
                throw ValidationException::withMessages([
                    'booking_code' => $checkIn['message'],
                ]);
            }

            $fromStatus = (string) ($booking->usage_status ?? 'not_checked_in');
            $now = CarbonImmutable::now($this->timezone());

            $updates = [
                'status' => 'confirmed',
                'usage_status' => 'checked_in',
                'checked_in_at' => $now,
            ];

            if (Schema::hasColumn('bookings', 'status_source')) {
                $updates['status_source'] = 'admin_code_scan';
            }

            if (Schema::hasColumn('bookings', 'auto_status_updated_at')) {
                $updates['auto_status_updated_at'] = $now;
            }

            $booking->forceFill($updates)->save();

            $this->writeStatusHistory(
                booking: $booking,
                fromStatus: $fromStatus,
                occurredAt: $now,
            );

            return $booking->fresh([
                'user',
                'bookingDetails.field',
                'bookingDetails.timeSlot',
            ]);
        }, 3);

        return redirect()
            ->route('admin.bookings.check-in.index', [
                'booking_code' => $this->displayCode($booking),
            ])
            ->with(
                'success',
                'Xác nhận check-in thành công. Sân đã chuyển sang trạng thái đang hoạt động.',
            );
    }

    private function findBookingByCode(string $bookingCode): ?Booking
    {
        $codeColumn = $this->bookingCodeColumn();

        if ($codeColumn === null) {
            return null;
        }

        /** @var Booking|null $booking */
        $booking = Booking::query()
            ->with([
                'user',
                'bookingDetails.field',
                'bookingDetails.timeSlot',
            ])
            ->where($codeColumn, trim($bookingCode))
            ->first();

        return $booking;
    }

    /**
     * @return array{
     *     can_check_in: bool,
     *     message: string,
     *     payment_full: bool,
     *     starts_at: CarbonImmutable|null,
     *     ends_at: CarbonImmutable|null,
     *     opens_at: CarbonImmutable|null
     * }
     */
    private function evaluateCheckIn(Booking $booking): array
    {
        $status = strtolower((string) ($booking->status ?? 'pending'));
        $usageStatus = strtolower((string) (
            $booking->usage_status
            ?? 'not_checked_in'
        ));

        $window = $this->resolveScheduleWindow($booking);
        $fullPayment = $this->isFullPayment($booking);
        $earlyMinutes = max(
            0,
            (int) config('booking_lifecycle.check_in_early_minutes', 15),
        );

        $opensAt = $window !== null
            ? $window['starts_at']->subMinutes($earlyMinutes)
            : null;

        $now = CarbonImmutable::now($this->timezone());

        if ($status === 'cancelled') {
            return $this->evaluation(false, 'Đơn đã bị hủy và không thể check-in.', $fullPayment, $window, $opensAt);
        }

        if ($status === 'completed' || $usageStatus === 'checked_out') {
            return $this->evaluation(false, 'Đơn đã kết thúc và không thể check-in.', $fullPayment, $window, $opensAt);
        }

        if ($usageStatus === 'checked_in') {
            return $this->evaluation(false, 'Đơn này đã check-in và sân đang hoạt động.', $fullPayment, $window, $opensAt);
        }

        if (! $fullPayment) {
            return $this->evaluation(false, 'Khách chưa thanh toán đầy đủ số tiền còn lại.', false, $window, $opensAt);
        }

        if (! in_array($status, ['confirmed', 'pending'], true)) {
            return $this->evaluation(false, 'Trạng thái đơn hiện tại không cho phép check-in.', true, $window, $opensAt);
        }

        if ($window === null || $opensAt === null) {
            return $this->evaluation(false, 'Không xác định được ngày hoặc khung giờ của đơn.', true, null, null);
        }

        if ($now->lessThan($opensAt)) {
            return $this->evaluation(
                false,
                'Chưa đến thời gian check-in. Có thể check-in từ '.$opensAt->format('H:i d/m/Y').'.',
                true,
                $window,
                $opensAt,
            );
        }

        if ($now->greaterThanOrEqualTo($window['ends_at'])) {
            return $this->evaluation(false, 'Khung giờ đặt sân đã kết thúc.', true, $window, $opensAt);
        }

        return $this->evaluation(
            true,
            'Mã đơn hợp lệ. Nhân viên có thể xác nhận check-in.',
            true,
            $window,
            $opensAt,
        );
    }

    /**
     * @param array{starts_at:CarbonImmutable,ends_at:CarbonImmutable}|null $window
     * @return array{
     *     can_check_in: bool,
     *     message: string,
     *     payment_full: bool,
     *     starts_at: CarbonImmutable|null,
     *     ends_at: CarbonImmutable|null,
     *     opens_at: CarbonImmutable|null
     * }
     */
    private function evaluation(
        bool $canCheckIn,
        string $message,
        bool $fullPayment,
        ?array $window,
        ?CarbonImmutable $opensAt,
    ): array {
        return [
            'can_check_in' => $canCheckIn,
            'message' => $message,
            'payment_full' => $fullPayment,
            'starts_at' => $window['starts_at'] ?? null,
            'ends_at' => $window['ends_at'] ?? null,
            'opens_at' => $opensAt,
        ];
    }

    /**
     * @return array{starts_at:CarbonImmutable,ends_at:CarbonImmutable}|null
     */
    private function resolveScheduleWindow(Booking $booking): ?array
    {
        $timezone = $this->timezone();
        $details = collect($booking->bookingDetails ?? []);
        $windows = [];

        foreach ($details as $detail) {
            $dateValue = data_get($detail, 'booking_date')
                ?? data_get($detail, 'date')
                ?? data_get($booking, 'booking_date');

            $startValue = data_get($detail, 'slot_start_time')
                ?? data_get($detail, 'start_time')
                ?? data_get($detail, 'timeSlot.start_time');

            $endValue = data_get($detail, 'slot_end_time')
                ?? data_get($detail, 'end_time')
                ?? data_get($detail, 'timeSlot.end_time');

            if ($dateValue === null || $startValue === null || $endValue === null) {
                continue;
            }

            try {
                $date = $dateValue instanceof CarbonInterface
                    ? $dateValue->format('Y-m-d')
                    : CarbonImmutable::parse($dateValue, $timezone)->format('Y-m-d');

                $startsAt = CarbonImmutable::parse($date.' '.$startValue, $timezone);
                $endsAt = CarbonImmutable::parse($date.' '.$endValue, $timezone);

                if ($endsAt->lessThanOrEqualTo($startsAt)) {
                    $endsAt = $endsAt->addDay();
                }

                $windows[] = [
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ];
            } catch (Throwable) {
                continue;
            }
        }

        if ($windows === []) {
            return null;
        }

        usort(
            $windows,
            static fn (array $left, array $right): int =>
                $left['starts_at']->getTimestamp()
                <=> $right['starts_at']->getTimestamp(),
        );

        $startsAt = $windows[0]['starts_at'];
        $endsAt = $windows[0]['ends_at'];

        foreach ($windows as $window) {
            if ($window['ends_at']->greaterThan($endsAt)) {
                $endsAt = $window['ends_at'];
            }
        }

        return [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    private function isFullPayment(Booking $booking): bool
    {
        $paymentStatus = strtolower((string) (
            $booking->payment_status
            ?? 'unpaid'
        ));

        if (in_array($paymentStatus, ['paid', 'paid_full', 'completed'], true)) {
            return true;
        }

        $paidAmount = max(0, (float) ($booking->paid_amount ?? 0));
        $totalPayable = max(
            0,
            (float) (
                $booking->final_amount
                ?? $booking->total_amount
                ?? $booking->total_price
                ?? $booking->total
                ?? 0
            ),
        );

        return $totalPayable > 0 && $paidAmount >= $totalPayable;
    }

    private function bookingCodeColumn(): ?string
    {
        if (Schema::hasColumn('bookings', 'booking_code')) {
            return 'booking_code';
        }

        if (Schema::hasColumn('bookings', 'code')) {
            return 'code';
        }

        return null;
    }

    private function displayCode(Booking $booking): string
    {
        return (string) (
            $booking->booking_code
            ?? $booking->code
            ?? $booking->id
        );
    }

    private function timezone(): string
    {
        return (string) config(
            'booking_lifecycle.timezone',
            config('app.timezone', 'Asia/Ho_Chi_Minh'),
        );
    }

    private function writeStatusHistory(
        Booking $booking,
        string $fromStatus,
        CarbonImmutable $occurredAt,
    ): void {
        if (! Schema::hasTable('booking_status_histories')) {
            return;
        }

        $payload = [];
        $columns = [
            'booking_id' => $booking->id,
            'category' => 'usage_status',
            'from_status' => $fromStatus,
            'to_status' => 'checked_in',
            'source' => 'admin_code_scan',
            'reason' => 'staff_confirmed_booking_code',
            'occurred_at' => $occurredAt,
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ];

        foreach ($columns as $column => $value) {
            if (Schema::hasColumn('booking_status_histories', $column)) {
                $payload[$column] = $value;
            }
        }

        if (array_key_exists('booking_id', $payload)) {
            DB::table('booking_status_histories')->insert($payload);
        }
    }
}
