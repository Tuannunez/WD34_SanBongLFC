<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Support\DatabaseSchemaInspector;
use App\ValueObjects\Bookings\BookingScheduleWindow;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final class BookingLifecycleService
{
    public const ACTION_CANCELLED = 'cancelled';
    public const ACTION_CONFIRMED = 'confirmed';
    public const ACTION_NO_SHOW = 'no_show';
    public const ACTION_CHECKED_IN = 'checked_in';
    public const ACTION_CHECKED_OUT = 'checked_out';
    public const ACTION_COMPLETED = 'completed';

    public const RESULT_CHECKED_IN = 'checked_in';
    public const RESULT_ALREADY_CHECKED_IN = 'already_checked_in';
    public const RESULT_TOO_EARLY = 'too_early';
    public const RESULT_NO_SHOW = 'no_show';
    public const RESULT_NOT_PAID = 'not_paid';
    public const RESULT_MISSING_SCHEDULE = 'missing_schedule';
    public const RESULT_NOT_FOUND = 'not_found';
    public const RESULT_UNAVAILABLE = 'unavailable';

    public function __construct(
        private readonly BookingScheduleResolver $scheduleResolver,
        private readonly DatabaseSchemaInspector $schema,
    ) {
    }

    /**
     * @return array{
     *   scanned:int,
     *   changed:int,
     *   cancelled:int,
     *   confirmed:int,
     *   no_show:int,
     *   checked_in:int,
     *   checked_out:int,
     *   completed:int,
     *   missing_schedule:int,
     *   errors:int,
     *   actions:list<array<string, mixed>>
     * }
     */
    public function synchronize(
        ?CarbonInterface $at = null,
        ?int $bookingId = null,
        bool $dryRun = false,
    ): array {
        $this->assertPrerequisites();
        $now = $this->normalizeNow($at);

        $summary = [
            'scanned' => 0,
            'changed' => 0,
            'cancelled' => 0,
            'confirmed' => 0,
            'no_show' => 0,
            'checked_in' => 0,
            'checked_out' => 0,
            'completed' => 0,
            'missing_schedule' => 0,
            'errors' => 0,
            'actions' => [],
        ];

        Booking::query()
            ->select('id')
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                'paid',
            ])
            ->when($bookingId !== null, fn ($query) => $query->whereKey($bookingId))
            ->orderBy('id')
            ->chunkById(
                (int) config('booking_lifecycle.chunk_size', 200),
                function ($rows) use (&$summary, $now, $dryRun): void {
                    foreach ($rows as $row) {
                        $summary['scanned']++;

                        try {
                            $result = $this->synchronizeOne((int) $row->id, $now, $dryRun);

                            foreach ($result['actions'] as $action) {
                                $summary['changed']++;
                                $summary['actions'][] = $action;

                                $type = (string) ($action['action'] ?? '');

                                if (array_key_exists($type, $summary)) {
                                    $summary[$type]++;
                                }
                            }

                            if ($result['missing_schedule']) {
                                $summary['missing_schedule']++;
                            }
                        } catch (Throwable $exception) {
                            $summary['errors']++;

                            Log::error('Không thể đồng bộ vòng đời booking.', [
                                'booking_id' => $row->id,
                                'error' => $exception->getMessage(),
                                'exception' => $exception,
                            ]);
                        }
                    }
                },
            );

        return $summary;
    }

    /**
     * Người dùng chủ động check-in. Scheduler tuyệt đối không tự check-in.
     *
     * @return array<string, mixed>
     */
    public function checkInByUser(
        int $bookingId,
        int $userId,
        ?CarbonInterface $at = null,
    ): array {
        $this->assertPrerequisites();
        $now = $this->normalizeNow($at);

        return DB::transaction(function () use ($bookingId, $userId, $now): array {
            /** @var Booking|null $booking */
            $booking = Booking::query()
                ->whereKey($bookingId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($booking === null) {
                return ['result' => self::RESULT_NOT_FOUND];
            }

            if (
                strtolower((string) $booking->status) !== Booking::STATUS_CONFIRMED
                || strtolower((string) $booking->payment_status) === Booking::PAYMENT_REFUNDED
            ) {
                return ['result' => self::RESULT_UNAVAILABLE];
            }

            if (! $this->hasSuccessfulPaymentEvidence($booking)) {
                return ['result' => self::RESULT_NOT_PAID];
            }

            $window = $this->scheduleResolver->resolve($booking);

            if ($window === null) {
                return ['result' => self::RESULT_MISSING_SCHEDULE];
            }

            $usageStatus = strtolower((string) ($booking->usage_status ?: Booking::USAGE_NOT_CHECKED_IN));

            if ($usageStatus === Booking::USAGE_CHECKED_IN) {
                return [
                    'result' => self::RESULT_ALREADY_CHECKED_IN,
                    'checked_in_at' => $booking->checked_in_at,
                ];
            }

            if ($usageStatus === Booking::USAGE_CHECKED_OUT) {
                return ['result' => self::RESULT_UNAVAILABLE];
            }

            $opensAt = $window->startsAt->subMinutes($this->checkInEarlyMinutes());
            $deadline = $this->noShowDeadline($booking, $window);

            if ($now->lessThan($opensAt)) {
                return [
                    'result' => self::RESULT_TOO_EARLY,
                    'opens_at' => $opensAt,
                    'deadline_at' => $deadline,
                ];
            }

            if ($now->greaterThanOrEqualTo($deadline)) {
                $action = $this->applyNoShowCancellation(
                    $booking,
                    $window,
                    $deadline,
                    false,
                );

                $metadata = (array) ($action['metadata'] ?? []);

                return [
                    'result' => self::RESULT_NO_SHOW,
                    'deadline_at' => $deadline,
                    'grace_minutes' => (int) ($metadata['grace_minutes'] ?? 15),
                    'payment_type' => (string) ($metadata['payment_type'] ?? 'deposit'),
                    'forfeited_amount' => (float) ($metadata['forfeited_amount'] ?? 0),
                ];
            }

            $action = $this->actionRow(
                booking: $booking,
                action: self::ACTION_CHECKED_IN,
                category: 'usage',
                from: Booking::USAGE_NOT_CHECKED_IN,
                to: Booking::USAGE_CHECKED_IN,
                reason: 'user_check_in',
                occurredAt: $now,
                window: $window,
                source: 'user',
            );

            $data = $this->schema->filterColumns('bookings', [
                'usage_status' => Booking::USAGE_CHECKED_IN,
                'checked_in_at' => $now,
                'check_in_source' => 'user_web',
                'updated_at' => now(),
            ]);

            $booking->forceFill($data)->save();
            $this->recordHistory($action);
            $this->insertNotification(
                $booking,
                'Check-in thành công',
                'Đơn '.$this->bookingCode($booking).' đã check-in thành công lúc '.$now->format('H:i d/m/Y').'.',
            );

            return [
                'result' => self::RESULT_CHECKED_IN,
                'checked_in_at' => $now,
                'starts_at' => $window->startsAt,
                'ends_at' => $window->endsAt,
                'deadline_at' => $deadline,
            ];
        }, 3);
    }

    /**
     * @return array{actions:list<array<string, mixed>>,missing_schedule:bool}
     */
    public function synchronizeOne(
        int $bookingId,
        ?CarbonInterface $at = null,
        bool $dryRun = false,
    ): array {
        $now = $this->normalizeNow($at);

        return DB::transaction(function () use ($bookingId, $now, $dryRun): array {
            /** @var Booking|null $booking */
            $booking = Booking::query()
                ->lockForUpdate()
                ->find($bookingId);

            if ($booking === null) {
                return ['actions' => [], 'missing_schedule' => false];
            }

            $status = strtolower((string) $booking->status);

            if (in_array($status, [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED], true)) {
                return ['actions' => [], 'missing_schedule' => false];
            }

            if (strtolower((string) $booking->payment_status) === Booking::PAYMENT_REFUNDED) {
                return ['actions' => [], 'missing_schedule' => false];
            }

            $window = $this->scheduleResolver->resolve($booking);
            $actions = [];

            if ($status === 'paid') {
                $status = Booking::STATUS_CONFIRMED;
                $action = $this->actionRow(
                    $booking,
                    self::ACTION_CONFIRMED,
                    'status',
                    'paid',
                    Booking::STATUS_CONFIRMED,
                    'legacy_paid_normalized',
                    $now,
                );
                $actions[] = $action;

                if (! $dryRun) {
                    $booking->forceFill(['status' => Booking::STATUS_CONFIRMED])->save();
                    $this->recordHistory($action);
                }
            }

            if ($status === Booking::STATUS_PENDING) {
                if ($this->hasSuccessfulPaymentEvidence($booking)) {
                    $action = $this->actionRow(
                        $booking,
                        self::ACTION_CONFIRMED,
                        'status',
                        Booking::STATUS_PENDING,
                        Booking::STATUS_CONFIRMED,
                        'payment_evidence_detected',
                        $now,
                    );
                    $actions[] = $action;

                    if (! $dryRun) {
                        $booking->forceFill([
                            'status' => Booking::STATUS_CONFIRMED,
                            'usage_status' => $booking->usage_status ?: Booking::USAGE_NOT_CHECKED_IN,
                        ])->save();
                        $this->recordHistory($action);
                        $this->syncDetailStatus($booking, Booking::STATUS_CONFIRMED);
                    }

                    $status = Booking::STATUS_CONFIRMED;
                } else {
                    $expiresAt = $this->resolveExpiresAt($booking, $window);

                    if ($expiresAt !== null && $now->greaterThanOrEqualTo($expiresAt)) {
                        $action = $this->actionRow(
                            $booking,
                            self::ACTION_CANCELLED,
                            'status',
                            Booking::STATUS_PENDING,
                            Booking::STATUS_CANCELLED,
                            'payment_hold_expired',
                            $expiresAt,
                        );
                        $actions[] = $action;

                        if (! $dryRun) {
                            $booking->forceFill([
                                'status' => Booking::STATUS_CANCELLED,
                                'usage_status' => Booking::USAGE_NOT_CHECKED_IN,
                                'cancelled_at' => $booking->cancelled_at ?? $expiresAt,
                            ])->save();
                            $this->recordHistory($action);
                            $this->syncDetailStatus($booking, Booking::STATUS_CANCELLED);
                            $this->insertNotification(
                                $booking,
                                'Đơn đặt sân đã tự động hủy',
                                'Đơn '.$this->bookingCode($booking).' đã hết thời gian giữ sân nhưng chưa thanh toán.',
                            );
                        }
                    }

                    return ['actions' => $actions, 'missing_schedule' => false];
                }
            }

            if ($status !== Booking::STATUS_CONFIRMED) {
                return ['actions' => $actions, 'missing_schedule' => false];
            }

            if ($window === null) {
                return ['actions' => $actions, 'missing_schedule' => true];
            }

            // Không xử lý no-show hoặc sử dụng sân nếu chưa có bằng chứng thanh toán.
            if (! $this->hasSuccessfulPaymentEvidence($booking)) {
                return ['actions' => $actions, 'missing_schedule' => false];
            }

            $usageStatus = strtolower((string) ($booking->usage_status ?: Booking::USAGE_NOT_CHECKED_IN));
            $deadline = $this->noShowDeadline($booking, $window);

            // Áp dụng hạn 15 phút cho đặt cọc, 30 phút cho thanh toán đủ.
            if (
                $usageStatus === Booking::USAGE_NOT_CHECKED_IN
                && $now->greaterThanOrEqualTo($deadline)
            ) {
                $action = $this->applyNoShowCancellation($booking, $window, $deadline, $dryRun);
                $actions[] = $action;

                return ['actions' => $actions, 'missing_schedule' => false];
            }

            // Tự chữa dữ liệu lệch: đã check-out nhưng trạng thái đơn vẫn confirmed.
            if (
                $usageStatus === Booking::USAGE_CHECKED_OUT
                && $now->greaterThanOrEqualTo($window->endsAt)
            ) {
                $completeAction = $this->actionRow(
                    $booking,
                    self::ACTION_COMPLETED,
                    'status',
                    Booking::STATUS_CONFIRMED,
                    Booking::STATUS_COMPLETED,
                    'repair_completed_after_existing_check_out',
                    $window->endsAt,
                    $window,
                );
                $actions[] = $completeAction;

                if (! $dryRun) {
                    $booking->forceFill([
                        'status' => Booking::STATUS_COMPLETED,
                        'completed_at' => $booking->completed_at
                            ?? $booking->checked_out_at
                            ?? $window->endsAt,
                    ])->save();

                    $this->recordHistory($completeAction);
                    $this->syncDetailStatus($booking, Booking::STATUS_COMPLETED);
                }

                return ['actions' => $actions, 'missing_schedule' => false];
            }

            // Chỉ đơn đã được khách check-in mới được tự check-out và hoàn tất.
            if (
                $usageStatus === Booking::USAGE_CHECKED_IN
                && $now->greaterThanOrEqualTo($window->endsAt)
            ) {
                $checkOutAction = $this->actionRow(
                    $booking,
                    self::ACTION_CHECKED_OUT,
                    'usage',
                    Booking::USAGE_CHECKED_IN,
                    Booking::USAGE_CHECKED_OUT,
                    'automatic_check_out',
                    $window->endsAt,
                    $window,
                );
                $actions[] = $checkOutAction;

                $completeAction = $this->actionRow(
                    $booking,
                    self::ACTION_COMPLETED,
                    'status',
                    Booking::STATUS_CONFIRMED,
                    Booking::STATUS_COMPLETED,
                    'booking_schedule_ended_after_check_in',
                    $window->endsAt,
                    $window,
                );
                $actions[] = $completeAction;

                if (! $dryRun) {
                    $booking->forceFill([
                        'status' => Booking::STATUS_COMPLETED,
                        'usage_status' => Booking::USAGE_CHECKED_OUT,
                        'checked_out_at' => $booking->checked_out_at ?? $window->endsAt,
                        'completed_at' => $booking->completed_at ?? $window->endsAt,
                    ])->save();
                    $this->recordHistory($checkOutAction);
                    $this->recordHistory($completeAction);
                    $this->syncDetailStatus($booking, Booking::STATUS_COMPLETED);
                    $this->insertNotification(
                        $booking,
                        'Đơn đặt sân đã hoàn tất',
                        'Đơn '.$this->bookingCode($booking).' đã tự động check-out khi hết giờ sân.',
                    );
                }
            }

            return ['actions' => $actions, 'missing_schedule' => false];
        }, 3);
    }

    private function applyNoShowCancellation(
        Booking $booking,
        BookingScheduleWindow $window,
        CarbonImmutable $deadline,
        bool $dryRun,
    ): array {
        $money = $this->noShowMoney($booking);
        $isFullPayment = $money['payment_type'] === 'full';

        $action = $this->actionRow(
            booking: $booking,
            action: self::ACTION_NO_SHOW,
            category: 'status',
            from: Booking::STATUS_CONFIRMED,
            to: Booking::STATUS_CANCELLED,
            reason: $isFullPayment
                ? 'full_payment_no_show_timeout'
                : 'deposit_no_show_timeout',
            occurredAt: $deadline,
            window: $window,
        );

        $action['metadata'] = array_merge($action['metadata'] ?? [], [
            'check_in_deadline_at' => $deadline->toDateTimeString(),
            'payment_type' => $money['payment_type'],
            'grace_minutes' => $money['grace_minutes'],
            'forfeited_amount' => $money['forfeited'],
            'deposit_forfeited_amount' => $money['forfeited'],
            'refund_amount' => $money['refundable'],
        ]);

        if ($dryRun) {
            return $action;
        }

        $refundStatus = $money['refundable'] > 0
            ? Booking::REFUND_PENDING
            : Booking::REFUND_NONE;

        $lossMessage = $isFullPayment
            ? 'Toàn bộ số tiền đã thanh toán bị giữ lại.'
            : 'Tiền cọc bị giữ lại.';

        $data = $this->schema->filterColumns('bookings', [
            'status' => Booking::STATUS_CANCELLED,
            'usage_status' => Booking::USAGE_NOT_CHECKED_IN,
            'cancelled_at' => $booking->cancelled_at ?? $deadline,
            'no_show_at' => $booking->no_show_at ?? $deadline,
            'no_show_payment_type' => $money['payment_type'],
            'no_show_grace_minutes' => $money['grace_minutes'],
            'no_show_forfeited_amount' => $money['forfeited'],
            // Giữ cột cũ để các giao diện/câu truy vấn cũ vẫn hoạt động.
            'deposit_forfeited_amount' => $money['forfeited'],
            'refund_amount' => $money['refundable'],
            'refund_status' => $refundStatus,
            'cancel_note' => 'Khách không check-in trong vòng '
                .$money['grace_minutes'].' phút sau giờ bắt đầu. '
                .$lossMessage,
            'cancellation_reason' => $isFullPayment
                ? 'full_payment_no_show_timeout'
                : 'deposit_no_show_timeout',
            'updated_at' => now(),
        ]);

        $booking->forceFill($data)->save();
        $this->recordHistory($action);
        $this->syncDetailStatus($booking, Booking::STATUS_CANCELLED);

        $content = 'Đơn '.$this->bookingCode($booking)
            .' đã bị hủy do không check-in trong vòng '
            .$money['grace_minutes'].' phút. ';

        if ($isFullPayment) {
            $content .= 'Toàn bộ số tiền '
                .number_format($money['forfeited'], 0, ',', '.')
                .'đ không được hoàn lại.';
        } else {
            $content .= 'Tiền cọc '
                .number_format($money['forfeited'], 0, ',', '.')
                .'đ không được hoàn lại.';
        }

        if ($money['refundable'] > 0) {
            $content .= ' Phần tiền còn lại '
                .number_format($money['refundable'], 0, ',', '.')
                .'đ đã được ghi nhận chờ hoàn.';
        }

        $this->insertNotification($booking, 'Hủy sân do không đến', $content);

        return $action;
    }

    /**
     * @return array{
     *   paid:float,
     *   forfeited:float,
     *   refundable:float,
     *   payment_type:string,
     *   grace_minutes:int
     * }
     */
    private function noShowMoney(Booking $booking): array
    {
        $policy = $this->noShowPolicy($booking);
        $effectivePaid = $this->effectivePaidAmount($booking);

        if ($policy['payment_type'] === 'full') {
            $forfeited = (bool) config(
                'booking_lifecycle.forfeit_full_payment_on_no_show',
                true,
            ) ? $effectivePaid : 0;
        } else {
            $deposit = max(0, (float) ($booking->deposit_amount ?? 0));

            $forfeited = (bool) config(
                'booking_lifecycle.forfeit_deposit_on_no_show',
                true,
            ) && $deposit > 0
                ? min($effectivePaid, $deposit)
                : 0;
        }

        return [
            'paid' => round($effectivePaid, 2),
            'forfeited' => round($forfeited, 2),
            'refundable' => round(max(0, $effectivePaid - $forfeited), 2),
            'payment_type' => $policy['payment_type'],
            'grace_minutes' => $policy['grace_minutes'],
        ];
    }

    /** @return array{payment_type:string,grace_minutes:int} */
    private function noShowPolicy(Booking $booking): array
    {
        $isFullPayment = $this->isFullPayment($booking);

        return [
            'payment_type' => $isFullPayment ? 'full' : 'deposit',
            'grace_minutes' => $isFullPayment
                ? max(0, (int) config(
                    'booking_lifecycle.full_payment_no_show_grace_minutes',
                    30,
                ))
                : max(0, (int) config(
                    'booking_lifecycle.deposit_no_show_grace_minutes',
                    15,
                )),
        ];
    }

    private function isFullPayment(Booking $booking): bool
    {
        $paymentStatus = strtolower((string) (
            $booking->payment_status
            ?? Booking::PAYMENT_UNPAID
        ));

        if (in_array($paymentStatus, [
            Booking::PAYMENT_PAID,
            'completed',
            'paid_full',
        ], true)) {
            return true;
        }

        $totalPayable = $this->totalPayableAmount($booking);

        return $totalPayable > 0
            && $this->effectivePaidAmount($booking) >= $totalPayable;
    }

    private function effectivePaidAmount(Booking $booking): float
    {
        $paymentStatus = strtolower((string) (
            $booking->payment_status
            ?? Booking::PAYMENT_UNPAID
        ));

        $recordedPaid = max(0, (float) ($booking->paid_amount ?? 0));
        $deposit = max(0, (float) ($booking->deposit_amount ?? 0));
        $totalPayable = $this->totalPayableAmount($booking);

        if (
            $paymentStatus === Booking::PAYMENT_DEPOSIT_PAID
            || (bool) ($booking->is_deposit_paid ?? false)
        ) {
            $recordedPaid = max($recordedPaid, $deposit);
        }

        if (in_array($paymentStatus, [
            Booking::PAYMENT_PAID,
            'completed',
            'paid_full',
        ], true)) {
            $recordedPaid = max($recordedPaid, $totalPayable, $deposit);
        }

        return $recordedPaid;
    }

    private function totalPayableAmount(Booking $booking): float
    {
        return max(
            0,
            (float) (
                $booking->final_amount
                ?? $booking->total_amount
                ?? $booking->total_price
                ?? $booking->total
                ?? 0
            ),
        );
    }

    private function noShowDeadline(
        Booking $booking,
        BookingScheduleWindow $window,
    ): CarbonImmutable {
        $policy = $this->noShowPolicy($booking);

        return $window->startsAt->addMinutes($policy['grace_minutes']);
    }

    private function checkInEarlyMinutes(): int
    {
        return max(0, (int) config('booking_lifecycle.check_in_early_minutes', 15));
    }

    private function assertPrerequisites(): void
    {
        if (! $this->schema->tableExists('bookings')) {
            throw new \RuntimeException('Không tìm thấy bảng bookings. Hãy hoàn tất migration trước.');
        }

        foreach ([
            'status',
            'usage_status',
            'payment_status',
            'hold_expires_at',
            'checked_in_at',
            'checked_out_at',
            'completed_at',
            'cancelled_at',
            'no_show_at',
            'deposit_forfeited_amount',
        ] as $column) {
            if (! $this->schema->columnExists('bookings', $column)) {
                throw new \RuntimeException('Bảng bookings còn thiếu cột '.$column.'. Hãy chạy migration của bước no-show.');
            }
        }
    }

    private function normalizeNow(?CarbonInterface $at): CarbonImmutable
    {
        return CarbonImmutable::instance($at ?? now())
            ->setTimezone((string) config(
                'booking_lifecycle.timezone',
                config('app.timezone', 'Asia/Ho_Chi_Minh'),
            ));
    }

    private function hasSuccessfulPaymentEvidence(Booking $booking): bool
    {
        $paymentStatus = strtolower((string) ($booking->payment_status ?? Booking::PAYMENT_UNPAID));

        return in_array($paymentStatus, [
            Booking::PAYMENT_DEPOSIT_PAID,
            Booking::PAYMENT_PAID,
            'completed',
            'paid_full',
        ], true)
            || (float) ($booking->paid_amount ?? 0) > 0
            || (bool) ($booking->is_deposit_paid ?? false);
    }

    private function resolveExpiresAt(
        Booking $booking,
        ?BookingScheduleWindow $window,
    ): ?CarbonImmutable {
        $expiresAt = null;

        if ($booking->hold_expires_at !== null) {
            $expiresAt = CarbonImmutable::instance($booking->hold_expires_at);
        } elseif ($booking->created_at !== null) {
            $expiresAt = CarbonImmutable::instance($booking->created_at)
                ->addMinutes((int) config('booking_lifecycle.hold_minutes', 5));
        }

        if (
            $expiresAt !== null
            && $window !== null
            && (bool) config('booking_lifecycle.expire_unpaid_at_start', true)
            && $window->startsAt->lessThan($expiresAt)
        ) {
            return $window->startsAt;
        }

        return $expiresAt;
    }

    /**
     * @return array<string, mixed>
     */
    private function actionRow(
        Booking $booking,
        string $action,
        string $category,
        ?string $from,
        string $to,
        string $reason,
        CarbonImmutable $occurredAt,
        ?BookingScheduleWindow $window = null,
        string $source = 'scheduler',
    ): array {
        return [
            'booking_id' => (int) $booking->getKey(),
            'booking_code' => $this->bookingCode($booking),
            'action' => $action,
            'category' => $category,
            'from_status' => $from,
            'to_status' => $to,
            'source' => $source,
            'reason' => $reason,
            'metadata' => $window === null ? null : [
                'starts_at' => $window->startsAt->toDateTimeString(),
                'ends_at' => $window->endsAt->toDateTimeString(),
            ],
            'occurred_at' => $occurredAt,
        ];
    }

    /**
     * @param  array<string, mixed>  $action
     */
    private function recordHistory(array $action): void
    {
        if (! $this->schema->tableExists('booking_status_histories')) {
            return;
        }

        DB::table('booking_status_histories')->insert([
            'booking_id' => $action['booking_id'],
            'category' => $action['category'],
            'from_status' => $action['from_status'],
            'to_status' => $action['to_status'],
            'source' => $action['source'],
            'reason' => $action['reason'],
            'metadata' => $action['metadata'] === null
                ? null
                : json_encode($action['metadata'], JSON_THROW_ON_ERROR),
            'occurred_at' => $action['occurred_at'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function syncDetailStatus(Booking $booking, string $status): void
    {
        if (
            ! $this->schema->tableExists('booking_details')
            || ! $this->schema->columnExists('booking_details', 'status')
        ) {
            return;
        }

        $data = ['status' => $status];

        if ($this->schema->columnExists('booking_details', 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::table('booking_details')
            ->where('booking_id', $booking->getKey())
            ->update($data);
    }

    private function insertNotification(Booking $booking, string $title, string $content): void
    {
        if (! $this->schema->tableExists('notifications') || $booking->user_id === null) {
            return;
        }

        $data = $this->schema->filterColumns('notifications', [
            'user_id' => $booking->user_id,
            'title' => $title,
            'content' => $content,
            'type' => 'booking',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($data !== []) {
            DB::table('notifications')->insert($data);
        }
    }

    private function bookingCode(Booking $booking): string
    {
        return (string) (
            $booking->booking_code
            ?? $booking->code
            ?? '#'.$booking->getKey()
        );
    }
}