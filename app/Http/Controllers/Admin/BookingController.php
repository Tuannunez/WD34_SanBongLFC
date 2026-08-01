<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::query()
            ->with([
                'user',
                'bookingDetails.field',
                'bookingDetails.timeSlot',
            ])
            ->orderByDesc('id');

        $this->applyFilters($query, $request);

        $bookings = $query
            ->paginate(15)
            ->withQueryString();

        $bookings->getCollection()->each(
            function (Booking $booking): void {
                $booking->setAttribute(
                    'admin_lifecycle',
                    $this->lifecyclePresentation($booking),
                );
            },
        );

        $stats = [
            'total' => Booking::query()->count(),
            'pending' => Booking::query()
                ->where('status', 'pending')
                ->count(),
            'confirmed' => Booking::query()
                ->where('status', 'confirmed')
                ->count(),
            'checked_in' => Booking::query()
                ->where('usage_status', 'checked_in')
                ->count(),
            'completed' => Booking::query()
                ->where('status', 'completed')
                ->count(),
            'no_show' => Schema::hasColumn('bookings', 'no_show_at')
                ? Booking::query()->whereNotNull('no_show_at')->count()
                : 0,
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'user',
            'bookingDetails.field',
            'bookingDetails.timeSlot',
            'bookingServices.service',
            'payments.paymentMethod',
            'review',
        ]);

        $bookingDetails = $booking->bookingDetails ?? collect();
        $bookingServices = $booking->bookingServices ?? collect();
        $payments = $booking->payments ?? collect();
        $bookingReview = $booking->review;
        $lifecycle = $this->lifecyclePresentation($booking);

        $statusHistories = collect();

        if (Schema::hasTable('booking_status_histories')) {
            $statusHistories = DB::table('booking_status_histories')
                ->where('booking_id', $booking->id)
                ->orderByDesc('occurred_at')
                ->orderByDesc('id')
                ->limit(30)
                ->get();
        }

        return view('admin.bookings.show', compact(
            'booking',
            'bookingDetails',
            'bookingServices',
            'payments',
            'bookingReview',
            'statusHistories',
            'lifecycle',
        ));
    }

    /**
     * Admin chỉ xử lý ngoại lệ hoàn tiền.
     * Check-in do khách thực hiện; check-out/no-show do Scheduler xử lý.
     */
    public function processRefund(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'refund_proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'refund_proof_note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'refund_proof_image.required' => 'Vui lòng tải ảnh chứng từ hoàn tiền.',
            'refund_proof_image.image' => 'Chứng từ hoàn tiền phải là hình ảnh.',
            'refund_proof_image.max' => 'Ảnh chứng từ không được vượt quá 5 MB.',
        ]);

        $path = $data['refund_proof_image']->store('refunds', 'public');

        try {
            DB::transaction(function () use ($id, $path, $data): void {
                /** @var Booking $booking */
                $booking = Booking::query()
                    ->lockForUpdate()
                    ->findOrFail($id);

                abort_unless(
                    strtolower((string) $booking->status) === 'cancelled'
                    && (float) ($booking->refund_amount ?? 0) > 0,
                    422,
                    'Đơn này không thuộc luồng cần hoàn tiền.',
                );

                $updates = [];

                if (Schema::hasColumn('bookings', 'refund_status')) {
                    $updates['refund_status'] = 'completed';
                }

                if (Schema::hasColumn('bookings', 'refund_proof_image')) {
                    $updates['refund_proof_image'] = 'storage/'.$path;
                }

                if (Schema::hasColumn('bookings', 'refund_proof')) {
                    $updates['refund_proof'] = $path;
                }

                if (Schema::hasColumn('bookings', 'refund_proof_note')) {
                    $updates['refund_proof_note'] = $data['refund_proof_note'] ?? null;
                }

                if (Schema::hasColumn('bookings', 'refund_processed_at')) {
                    $updates['refund_processed_at'] = now();
                }

                if ($updates !== []) {
                    $booking->forceFill($updates)->save();
                }
            }, 3);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }

        return back()->with(
            'success',
            'Đã lưu chứng từ hoàn tiền. Khách hàng có thể kiểm tra và xác nhận.',
        );
    }

    public function invoice(int $id): View
    {
        /** @var Booking $booking */
        $booking = Booking::query()
            ->with([
                'user',
                'bookingDetails.field',
                'bookingDetails.timeSlot',
                'bookingServices.service',
                'payments.paymentMethod',
            ])
            ->findOrFail($id);

        if (
            Schema::hasColumn('bookings', 'invoice_issued_at')
            && $booking->invoice_issued_at === null
        ) {
            $booking->forceFill([
                'invoice_issued_at' => now(),
            ])->save();
        }

        return view('admin.bookings.invoice', compact('booking'));
    }

    /**
     * Tính trạng thái hiển thị theo thời gian cho admin.
     *
     * Phương thức này KHÔNG cập nhật database và KHÔNG tự check-in.
     * Trạng thái checked_in chỉ xuất hiện khi usage_status trong database
     * thực sự bằng checked_in.
     *
     * @return array<string, mixed>
     */
    private function lifecyclePresentation(Booking $booking): array
    {
        $timezone = (string) config(
            'booking_lifecycle.timezone',
            config('app.timezone', 'Asia/Ho_Chi_Minh'),
        );

        $now = CarbonImmutable::now($timezone);
        $status = strtolower((string) ($booking->status ?? 'pending'));
        $usageStatus = strtolower((string) (
            $booking->usage_status
            ?? 'not_checked_in'
        ));
        $paymentStatus = strtolower((string) (
            $booking->payment_status
            ?? 'unpaid'
        ));

        $window = $this->resolveScheduleWindow($booking, $timezone);
        $fullPayment = $this->isFullPayment($booking);
        $graceMinutes = $fullPayment
            ? max(
                0,
                (int) config(
                    'booking_lifecycle.full_payment_no_show_grace_minutes',
                    30,
                ),
            )
            : max(
                0,
                (int) config(
                    'booking_lifecycle.deposit_no_show_grace_minutes',
                    15,
                ),
            );

        $opensAt = $window !== null
            ? $window['starts_at']->subMinutes(
                max(
                    0,
                    (int) config(
                        'booking_lifecycle.check_in_early_minutes',
                        15,
                    ),
                ),
            )
            : null;

        $deadlineAt = $window !== null
            ? $window['starts_at']->addMinutes($graceMinutes)
            : null;

        $noShowAt = ! empty($booking->no_show_at)
            ? CarbonImmutable::parse($booking->no_show_at, $timezone)
            : null;

        if ($noShowAt !== null) {
            return [
                'phase' => 'no_show',
                'label' => $fullPayment
                    ? 'No-show – mất toàn bộ tiền'
                    : 'No-show – mất tiền cọc',
                'color' => 'danger',
                'icon' => 'bi-person-x-fill',
                'description' => $fullPayment
                    ? 'Khách thanh toán đủ nhưng không check-in trong 30 phút.'
                    : 'Khách đặt cọc nhưng không check-in trong 15 phút.',
                'starts_at' => $window['starts_at'] ?? null,
                'ends_at' => $window['ends_at'] ?? null,
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if ($status === 'cancelled') {
            return [
                'phase' => 'cancelled',
                'label' => 'Đơn đã hủy',
                'color' => 'danger',
                'icon' => 'bi-x-circle-fill',
                'description' => 'Đơn đã bị hủy.',
                'starts_at' => $window['starts_at'] ?? null,
                'ends_at' => $window['ends_at'] ?? null,
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if ($usageStatus === 'checked_out' || $status === 'completed') {
            return [
                'phase' => 'checked_out',
                'label' => 'Đã check-out',
                'color' => 'success',
                'icon' => 'bi-check2-all',
                'description' => 'Khung giờ đã kết thúc và đơn đã hoàn thành.',
                'starts_at' => $window['starts_at'] ?? null,
                'ends_at' => $window['ends_at'] ?? null,
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if ($usageStatus === 'checked_in') {
            return [
                'phase' => 'checked_in',
                'label' => 'Khách đã check-in',
                'color' => 'info',
                'icon' => 'bi-box-arrow-in-right',
                'description' => 'Khách đang sử dụng sân. Scheduler sẽ tự check-out khi hết giờ.',
                'starts_at' => $window['starts_at'] ?? null,
                'ends_at' => $window['ends_at'] ?? null,
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if (
            $status === 'pending'
            || in_array($paymentStatus, ['', 'unpaid', 'pending'], true)
        ) {
            return [
                'phase' => 'waiting_payment',
                'label' => 'Chờ thanh toán',
                'color' => 'secondary',
                'icon' => 'bi-credit-card',
                'description' => 'Đơn chưa đủ điều kiện mở check-in.',
                'starts_at' => $window['starts_at'] ?? null,
                'ends_at' => $window['ends_at'] ?? null,
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if ($window === null || $opensAt === null || $deadlineAt === null) {
            return [
                'phase' => 'missing_schedule',
                'label' => 'Thiếu lịch sân',
                'color' => 'danger',
                'icon' => 'bi-calendar-x',
                'description' => 'Không xác định được ngày hoặc khung giờ của đơn.',
                'starts_at' => null,
                'ends_at' => null,
                'opens_at' => null,
                'deadline_at' => null,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if ($now->lessThan($opensAt)) {
            return [
                'phase' => 'upcoming',
                'label' => 'Chưa đến giờ check-in',
                'color' => 'secondary',
                'icon' => 'bi-clock',
                'description' => 'Check-in mở lúc '.$opensAt->format('H:i d/m/Y').'.',
                'starts_at' => $window['starts_at'],
                'ends_at' => $window['ends_at'],
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if ($now->lessThan($window['starts_at'])) {
            return [
                'phase' => 'check_in_open',
                'label' => 'Đã mở check-in',
                'color' => 'primary',
                'icon' => 'bi-unlock-fill',
                'description' => 'Khách có thể bấm check-in trước giờ bắt đầu.',
                'starts_at' => $window['starts_at'],
                'ends_at' => $window['ends_at'],
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        if ($now->lessThan($deadlineAt)) {
            return [
                'phase' => 'waiting_check_in',
                'label' => 'Đang chờ khách check-in',
                'color' => 'warning',
                'icon' => 'bi-person-clock',
                'description' => 'Giờ sân đã bắt đầu nhưng khách chưa bấm check-in.',
                'starts_at' => $window['starts_at'],
                'ends_at' => $window['ends_at'],
                'opens_at' => $opensAt,
                'deadline_at' => $deadlineAt,
                'grace_minutes' => $graceMinutes,
                'payment_type' => $fullPayment ? 'full' : 'deposit',
            ];
        }

        return [
            'phase' => 'overdue_waiting_scheduler',
            'label' => 'Quá hạn – chờ hệ thống xử lý',
            'color' => 'danger',
            'icon' => 'bi-exclamation-octagon-fill',
            'description' => 'Đã quá hạn check-in nhưng Scheduler chưa cập nhật no-show.',
            'starts_at' => $window['starts_at'],
            'ends_at' => $window['ends_at'],
            'opens_at' => $opensAt,
            'deadline_at' => $deadlineAt,
            'grace_minutes' => $graceMinutes,
            'payment_type' => $fullPayment ? 'full' : 'deposit',
        ];
    }

    /**
     * @return array{starts_at:CarbonImmutable,ends_at:CarbonImmutable}|null
     */
    private function resolveScheduleWindow(
        Booking $booking,
        string $timezone,
    ): ?array {
        $details = $booking->bookingDetails ?? collect();
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

                $startsAt = CarbonImmutable::parse(
                    $date.' '.$startValue,
                    $timezone,
                );

                $endsAt = CarbonImmutable::parse(
                    $date.' '.$endValue,
                    $timezone,
                );

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

        if (in_array($paymentStatus, [
            'paid',
            'paid_full',
            'completed',
        ], true)) {
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

    private function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString(),
            );
        }

        if ($request->filled('usage_status')) {
            $query->where(
                'usage_status',
                $request->string('usage_status')->toString(),
            );
        }

        if (
            $request->filled('payment_status')
            && Schema::hasColumn('bookings', 'payment_status')
        ) {
            $query->where(
                'payment_status',
                $request->string('payment_status')->toString(),
            );
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->string('keyword')->toString());

            $query->where(function (Builder $bookingQuery) use ($keyword): void {
                if (ctype_digit($keyword)) {
                    $bookingQuery->orWhere('id', (int) $keyword);
                }

                if (Schema::hasColumn('bookings', 'booking_code')) {
                    $bookingQuery->orWhere(
                        'booking_code',
                        'like',
                        "%{$keyword}%",
                    );
                }

                if (Schema::hasColumn('bookings', 'customer_name')) {
                    $bookingQuery->orWhere(
                        'customer_name',
                        'like',
                        "%{$keyword}%",
                    );
                }

                if (Schema::hasColumn('bookings', 'customer_phone')) {
                    $bookingQuery->orWhere(
                        'customer_phone',
                        'like',
                        "%{$keyword}%",
                    );
                }

                $bookingQuery->orWhereHas(
                    'user',
                    function (Builder $userQuery) use ($keyword): void {
                        $userQuery
                            ->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    },
                );
            });
        }
    }
}
