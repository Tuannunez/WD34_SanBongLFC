<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Bookings\BookingScheduleResolver;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class BookingCheckInScannerController extends Controller
{
    public function __construct(
        private readonly BookingScheduleResolver $scheduleResolver,
    ) {
    }

    /**
     * Mở trang scanner và xử lý luôn việc tra cứu từ:
     * /admin/bookings/check-in?booking_code=BK...
     */
    public function index(Request $request): View
    {
        $rawCode = trim((string) $request->query('booking_code', ''));

        if ($rawCode === '') {
            return $this->scannerView();
        }

        $code = $this->normalizeBookingCode($rawCode);
        $booking = $this->findBooking($code);

        if ($booking === null) {
            return $this->scannerView(
                booking: null,
                checkIn: [],
                lookupError: 'Không tìm thấy đơn đặt sân với mã: '.$code,
            );
        }

        $this->loadScannerRelations($booking);

        return $this->scannerView(
            booking: $booking,
            checkIn: $this->buildCheckInData($booking),
            lookupError: null,
        );
    }

    /**
     * POST chỉ dùng để XÁC NHẬN check-in.
     *
     * Nếu payment_confirmed=1:
     * - ghi nhận phần tiền còn lại tại quầy;
     * - cập nhật thanh toán;
     * - cập nhật check-in;
     * tất cả nằm trong cùng DB transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'booking_code' => ['required', 'string', 'max:500'],
            'payment_confirmed' => ['nullable', 'boolean'],
        ], [
            'booking_code.required' => 'Vui lòng quét QR hoặc nhập mã đơn.',
            'payment_confirmed.boolean' => 'Giá trị xác nhận thanh toán không hợp lệ.',
        ]);

        $code = $this->normalizeBookingCode(
            trim((string) $data['booking_code'])
        );

        $booking = $this->findBooking($code);

        if ($booking === null) {
            return redirect()
                ->route('admin.bookings.check-in.index', [
                    'booking_code' => $code,
                ])
                ->withErrors([
                    'booking_code' => 'Không tìm thấy đơn đặt sân theo mã vừa quét.',
                ]);
        }

        try {
            $result = $this->confirmPaymentAndCheckIn(
                bookingId: (int) $booking->getKey(),
                paymentConfirmed: $request->boolean('payment_confirmed'),
                adminId: (int) $request->user()->getAuthIdentifier(),
            );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('admin.bookings.check-in.index', [
                    'booking_code' => $code,
                ])
                ->withErrors([
                    'booking_code' => $exception->getMessage(),
                ]);
        }

        return redirect()
            ->route('admin.bookings.check-in.index', [
                'booking_code' => $code,
            ])
            ->with('success', $result['message']);
    }

    /**
     * Tìm theo ID, booking_code hoặc code.
     */
    private function findBooking(string $code): ?Booking
    {
        if ($code === '') {
            return null;
        }

        $query = Booking::query();

        $query->where(function ($builder) use ($code): void {
            if (ctype_digit($code)) {
                $builder->orWhere('id', (int) $code);
            }

            if (Schema::hasColumn('bookings', 'booking_code')) {
                $builder->orWhere('booking_code', $code);
            }

            if (Schema::hasColumn('bookings', 'code')) {
                $builder->orWhere('code', $code);
            }
        });

        return $query->first();
    }

    /**
     * Nhận được cả:
     * - BK2026...
     * - ID số
     * - #15
     * - URL có ?booking_code=...
     * - JSON {"booking_code":"BK..."}
     */
    private function normalizeBookingCode(string $raw): string
    {
        $raw = trim($raw);

        if ($raw === '') {
            return '';
        }

        if (preg_match('/^#(\d+)$/', $raw, $matches) === 1) {
            return $matches[1];
        }

        $json = json_decode($raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            foreach (['booking_code', 'code', 'booking', 'id'] as $key) {
                if (
                    array_key_exists($key, $json)
                    && trim((string) $json[$key]) !== ''
                ) {
                    return trim((string) $json[$key]);
                }
            }
        }

        if (filter_var($raw, FILTER_VALIDATE_URL) !== false) {
            $parts = parse_url($raw);

            if (! empty($parts['query'])) {
                parse_str($parts['query'], $query);

                foreach (['booking_code', 'code', 'booking'] as $key) {
                    if (
                        isset($query[$key])
                        && trim((string) $query[$key]) !== ''
                    ) {
                        return trim((string) $query[$key]);
                    }
                }
            }

            $path = trim((string) ($parts['path'] ?? ''), '/');

            if ($path !== '') {
                $segments = array_values(
                    array_filter(explode('/', $path))
                );

                $last = urldecode(
                    (string) ($segments[array_key_last($segments)] ?? '')
                );

                if (
                    preg_match('/^(BK[A-Z0-9_-]+|\d+)$/i', $last) === 1
                ) {
                    return $last;
                }
            }
        }

        return $raw;
    }

    /**
     * Dữ liệu để Blade quyết định hiển thị nút:
     * - can_check_in
     * - can_pay_and_check_in
     * - payment_full
     * - thời gian check-in
     *
     * @return array<string,mixed>
     */
    private function buildCheckInData(Booking $booking): array
    {
        $status = strtolower((string) ($booking->status ?? 'pending'));
        $usageStatus = strtolower((string) (
            $booking->usage_status ?? 'not_checked_in'
        ));

        $summary = $this->makeSummary($booking);
        $window = $this->scheduleResolver->resolve($booking);

        $base = [
            'can_check_in' => false,
            'can_pay_and_check_in' => false,
            'payment_full' => $summary['is_full_payment'],
            'total_amount' => $summary['total_amount'],
            'paid_amount' => $summary['paid_amount'],
            'remaining_amount' => $summary['remaining_amount'],
            'starts_at' => $window?->startsAt,
            'ends_at' => $window?->endsAt,
            'opens_at' => null,
            'deadline_at' => null,
            'message' => 'Đơn hiện chưa đủ điều kiện check-in.',
        ];

        if ($status === 'cancelled') {
            $base['message'] = 'Đơn đã bị hủy, không thể check-in.';

            return $base;
        }

        if ($status === 'completed' || $usageStatus === 'checked_out') {
            $base['message'] = 'Đơn đã hoàn thành/check-out.';

            return $base;
        }

        if ($usageStatus === 'checked_in') {
            $base['message'] = 'Đơn này đã được check-in trước đó.';

            return $base;
        }

        /*
         * Hỗ trợ trường hợp callback đã ghi nhận payment nhưng Scheduler
         * chưa kịp chuẩn hóa pending -> confirmed.
         */
        $effectiveConfirmed = in_array(
            $status,
            ['confirmed', 'paid'],
            true,
        ) || (
            $status === 'pending'
            && $this->hasSuccessfulPaymentEvidence($booking)
        );

        if (! $effectiveConfirmed) {
            $base['message'] = 'Đơn chưa được xác nhận thanh toán nên chưa thể check-in.';

            return $base;
        }

        if ($window === null) {
            $base['message'] = 'Không xác định được ngày/khung giờ của đơn.';

            return $base;
        }

        if ($summary['total_amount'] <= 0) {
            $base['message'] = 'Không xác định được tổng tiền của đơn, chưa thể check-in.';

            return $base;
        }

        $now = $this->now();

        $earlyMinutes = max(
            0,
            (int) config('booking_lifecycle.check_in_early_minutes', 15)
        );

        $graceMinutes = $summary['is_full_payment']
            ? max(
                0,
                (int) config(
                    'booking_lifecycle.full_payment_no_show_grace_minutes',
                    30
                )
            )
            : max(
                0,
                (int) config(
                    'booking_lifecycle.deposit_no_show_grace_minutes',
                    15
                )
            );

        $opensAt = $window->startsAt->subMinutes($earlyMinutes);
        $deadlineAt = $window->startsAt->addMinutes($graceMinutes);

        $base['opens_at'] = $opensAt;
        $base['deadline_at'] = $deadlineAt;

        if ($now->lessThan($opensAt)) {
            $base['message'] = 'Chưa đến thời gian check-in. Có thể check-in từ '
                .$opensAt->format('H:i d/m/Y').'.';

            return $base;
        }

        if ($now->greaterThanOrEqualTo($deadlineAt)) {
            $base['message'] = 'Đơn đã quá hạn check-in lúc '
                .$deadlineAt->format('H:i d/m/Y').'.';

            return $base;
        }

        if ($summary['is_full_payment']) {
            $base['can_check_in'] = true;
            $base['message'] = 'Đơn hợp lệ và đã thanh toán đủ. Có thể check-in.';

            return $base;
        }

        /*
         * Khách đã có booking hợp lệ nhưng mới đặt cọc:
         * cho phép nhân viên thu phần còn lại + check-in đồng bộ.
         */
        if ($summary['remaining_amount'] > 0.01) {
            $base['can_pay_and_check_in'] = true;
            $base['message'] = 'Khách còn thiếu '
                .number_format(
                    $summary['remaining_amount'],
                    0,
                    ',',
                    '.'
                )
                .'đ. Hãy xác nhận đã thu đủ tiền để check-in.';

            return $base;
        }

        return $base;
    }

    /**
     * Thanh toán phần còn lại + check-in nằm trong CÙNG transaction.
     *
     * @return array{message:string}
     */
    private function confirmPaymentAndCheckIn(
        int $bookingId,
        bool $paymentConfirmed,
        int $adminId,
    ): array {
        return DB::transaction(function () use (
            $bookingId,
            $paymentConfirmed,
            $adminId,
        ): array {
            /** @var Booking $booking */
            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($bookingId);

            $status = strtolower((string) ($booking->status ?? 'pending'));
            $usageStatus = strtolower((string) (
                $booking->usage_status ?? 'not_checked_in'
            ));

            if ($status === 'cancelled') {
                throw new \RuntimeException(
                    'Đơn đã bị hủy, không thể check-in.'
                );
            }

            if ($status === 'completed' || $usageStatus === 'checked_out') {
                throw new \RuntimeException(
                    'Đơn đã hoàn thành/check-out.'
                );
            }

            if ($usageStatus === 'checked_in') {
                throw new \RuntimeException(
                    'Đơn này đã được check-in trước đó.'
                );
            }

            /*
             * Chuẩn hóa trạng thái cũ/độ trễ Scheduler.
             */
            if (
                $status === 'paid'
                || (
                    $status === 'pending'
                    && $this->hasSuccessfulPaymentEvidence($booking)
                )
            ) {
                $booking->forceFill(
                    $this->filterColumns('bookings', [
                        'status' => 'confirmed',
                        'confirmed_at' => $booking->confirmed_at ?? now(),
                        'updated_at' => now(),
                    ])
                )->save();

                $status = 'confirmed';
            }

            if ($status !== 'confirmed') {
                throw new \RuntimeException(
                    'Đơn chưa ở trạng thái Đã xác nhận nên chưa thể check-in.'
                );
            }

            $window = $this->scheduleResolver->resolve($booking);

            if ($window === null) {
                throw new \RuntimeException(
                    'Không xác định được ngày/khung giờ của đơn để check-in.'
                );
            }

            $summaryBeforePayment = $this->makeSummary($booking);

            if ($summaryBeforePayment['total_amount'] <= 0) {
                throw new \RuntimeException(
                    'Không xác định được tổng tiền của đơn.'
                );
            }

            $now = $this->now();

            $earlyMinutes = max(
                0,
                (int) config(
                    'booking_lifecycle.check_in_early_minutes',
                    15
                )
            );

            $graceMinutes = $summaryBeforePayment['is_full_payment']
                ? max(
                    0,
                    (int) config(
                        'booking_lifecycle.full_payment_no_show_grace_minutes',
                        30
                    )
                )
                : max(
                    0,
                    (int) config(
                        'booking_lifecycle.deposit_no_show_grace_minutes',
                        15
                    )
                );

            $opensAt = $window->startsAt->subMinutes($earlyMinutes);
            $deadlineAt = $window->startsAt->addMinutes($graceMinutes);

            if ($now->lessThan($opensAt)) {
                throw new \RuntimeException(
                    'Chưa đến thời gian check-in. Có thể check-in từ '
                    .$opensAt->format('H:i d/m/Y').'.'
                );
            }

            if ($now->greaterThanOrEqualTo($deadlineAt)) {
                throw new \RuntimeException(
                    'Đơn đã quá hạn check-in lúc '
                    .$deadlineAt->format('H:i d/m/Y')
                    .'. Hãy để Scheduler xử lý no-show.'
                );
            }

            $totalAmount = $summaryBeforePayment['total_amount'];
            $paidAmount = $summaryBeforePayment['paid_amount'];
            $remainingAmount = max(0, $totalAmount - $paidAmount);

            if ($remainingAmount > 0.01) {
                if (! $paymentConfirmed) {
                    throw new \RuntimeException(
                        'Khách còn thiếu '
                        .number_format(
                            $remainingAmount,
                            0,
                            ',',
                            '.'
                        )
                        .'đ. Nhân viên phải xác nhận đã thu đủ tiền trước khi check-in.'
                    );
                }

                $this->recordCounterPayment(
                    booking: $booking,
                    amount: $remainingAmount,
                    adminId: $adminId,
                );

                $booking->forceFill(
                    $this->filterColumns('bookings', [
                        'paid_amount' => $totalAmount,
                        'payment_status' => 'paid',
                        'payment_type' => 'full',
                        'is_deposit_paid' => true,
                        'counter_payment_confirmed_at' => now(),
                        'counter_payment_confirmed_by' => $adminId,
                        'updated_at' => now(),
                    ])
                )->save();
            }

            $booking->forceFill(
                $this->filterColumns('bookings', [
                    'usage_status' => 'checked_in',
                    'checked_in_at' => $now,
                    'check_in_source' => 'admin_qr',
                    'updated_at' => now(),
                ])
            )->save();

            $this->recordHistory(
                bookingId: (int) $booking->getKey(),
                adminId: $adminId,
                remainingPaid: $remainingAmount,
                occurredAt: $now,
            );

            $this->notifyCustomer($booking, $now);

            return [
                'message' => $remainingAmount > 0.01
                    ? 'Đã xác nhận thu đủ '
                        .number_format(
                            $remainingAmount,
                            0,
                            ',',
                            '.'
                        )
                        .'đ và check-in thành công.'
                    : 'Check-in thành công. Đơn đã thanh toán đủ trước đó.',
            ];
        }, 3);
    }

    /**
     * Ghi giao dịch thu tiền tại sân.
     */
    private function recordCounterPayment(
        Booking $booking,
        float $amount,
        int $adminId,
    ): void {
        if (! Schema::hasTable('payments')) {
            throw new \RuntimeException(
                'Không tìm thấy bảng payments để ghi nhận khoản thu tại quầy.'
            );
        }

        $transactionRef = 'COUNTER_'
            .$booking->getKey().'_'
            .now()->format('YmdHis').'_'
            .Str::upper(Str::random(5));

        $payment = [
            'booking_id' => $booking->getKey(),
            'amount' => $amount,
            'provider' => 'counter',
            'payment_type' => 'full',
            'status' => 'succeeded',
            'transaction_ref' => $transactionRef,
            'transaction_code' => $transactionRef,
            'transaction_id' => $transactionRef,
            'response_code' => '00',
            'paid_at' => now(),
            'confirmed_by' => $adminId,
            'note' => 'Nhân viên xác nhận thu phần còn lại khi quét QR check-in.',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('payments', 'payment_method_id')) {
            $paymentMethodId = $this->ensureCounterPaymentMethod();

            if ($paymentMethodId === null) {
                throw new \RuntimeException(
                    'Không xác định được phương thức "Thanh toán tại sân".'
                );
            }

            $payment['payment_method_id'] = $paymentMethodId;
        }

        $filtered = $this->filterColumns('payments', $payment);

        if ($filtered === []) {
            throw new \RuntimeException(
                'Không có cột phù hợp để ghi giao dịch thanh toán.'
            );
        }

        DB::table('payments')->insert($filtered);
    }

    /**
     * Tạo/lấy payment method dùng cho khoản thu tại quầy.
     */
    private function ensureCounterPaymentMethod(): ?int
    {
        if (! Schema::hasTable('payment_methods')) {
            return null;
        }

        $query = DB::table('payment_methods');

        if (Schema::hasColumn('payment_methods', 'code')) {
            $existingId = (clone $query)
                ->where('code', 'PAY_AT_FIELD')
                ->value('id');

            if ($existingId !== null) {
                return (int) $existingId;
            }

            $data = $this->filterColumns('payment_methods', [
                'name' => 'Thanh toán tại sân',
                'code' => 'PAY_AT_FIELD',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payment_methods')->insert($data);

            return (int) DB::table('payment_methods')
                ->where('code', 'PAY_AT_FIELD')
                ->value('id');
        }

        if (Schema::hasColumn('payment_methods', 'name')) {
            $existingId = DB::table('payment_methods')
                ->where('name', 'like', '%tại sân%')
                ->value('id');

            if ($existingId !== null) {
                return (int) $existingId;
            }
        }

        $firstId = DB::table('payment_methods')->value('id');

        return $firstId !== null ? (int) $firstId : null;
    }

    private function recordHistory(
        int $bookingId,
        int $adminId,
        float $remainingPaid,
        CarbonImmutable $occurredAt,
    ): void {
        if (! Schema::hasTable('booking_status_histories')) {
            return;
        }

        $data = $this->filterColumns(
            'booking_status_histories',
            [
                'booking_id' => $bookingId,
                'category' => 'usage',
                'from_status' => 'not_checked_in',
                'to_status' => 'checked_in',
                'source' => 'admin_qr',
                'reason' => 'staff_scanned_qr',
                'metadata' => json_encode([
                    'admin_id' => $adminId,
                    'counter_payment' => $remainingPaid,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'occurred_at' => $occurredAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if ($data !== []) {
            DB::table('booking_status_histories')->insert($data);
        }
    }

    private function notifyCustomer(
        Booking $booking,
        CarbonImmutable $checkedInAt,
    ): void {
        if (
            ! Schema::hasTable('notifications')
            || empty($booking->user_id)
        ) {
            return;
        }

        $data = $this->filterColumns('notifications', [
            'user_id' => $booking->user_id,
            'title' => 'Check-in thành công',
            'content' => 'Đơn '
                .$this->bookingCode($booking)
                .' đã được nhân viên xác nhận check-in lúc '
                .$checkedInAt->format('H:i d/m/Y').'.',
            'type' => 'booking',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($data !== []) {
            DB::table('notifications')->insert($data);
        }
    }

    /**
     * @return array{
     *   total_amount:float,
     *   paid_amount:float,
     *   remaining_amount:float,
     *   is_full_payment:bool,
     *   booking_code:string
     * }
     */
    private function makeSummary(Booking $booking): array
    {
        $totalAmount = max(
            0,
            (float) (
                $booking->final_amount
                ?? $booking->total_amount
                ?? $booking->total_price
                ?? $booking->total
                ?? $booking->amount
                ?? 0
            )
        );

        $paidAmount = max(
            0,
            (float) ($booking->paid_amount ?? 0)
        );

        $paymentStatus = strtolower((string) (
            $booking->payment_status ?? 'unpaid'
        ));

        $paymentType = strtolower((string) (
            $booking->payment_type ?? ''
        ));

        $depositAmount = max(
            0,
            (float) ($booking->deposit_amount ?? 0)
        );

        /*
         * payment_status=paid trong dự án có thể chỉ là giao dịch cọc thành công.
         * paid_amount + payment_type mới quyết định đã trả đủ hay chưa.
         */
        if (
            $paidAmount <= 0
            && (
                $paymentStatus === 'deposit_paid'
                || $paymentType === 'deposit'
                || (bool) ($booking->is_deposit_paid ?? false)
            )
            && $depositAmount > 0
        ) {
            $paidAmount = $depositAmount;
        }

        if (
            $totalAmount > 0
            && (
                in_array(
                    $paymentStatus,
                    ['paid_full', 'completed'],
                    true
                )
                || (
                    in_array(
                        $paymentType,
                        ['full', 'full_payment'],
                        true
                    )
                    && $paymentStatus === 'paid'
                )
            )
        ) {
            $paidAmount = max($paidAmount, $totalAmount);
        }

        if ($totalAmount > 0) {
            $paidAmount = min($paidAmount, $totalAmount);
        }

        $remainingAmount = max(0, $totalAmount - $paidAmount);

        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'is_full_payment' => $totalAmount > 0
                && $remainingAmount <= 0.01,
            'booking_code' => $this->bookingCode($booking),
        ];
    }

    private function hasSuccessfulPaymentEvidence(
        Booking $booking,
    ): bool {
        $paymentStatus = strtolower((string) (
            $booking->payment_status ?? 'unpaid'
        ));

        if (
            in_array(
                $paymentStatus,
                ['paid', 'deposit_paid', 'paid_full', 'completed'],
                true
            )
        ) {
            return true;
        }

        return (float) ($booking->paid_amount ?? 0) > 0
            || (bool) ($booking->is_deposit_paid ?? false);
    }

    private function loadScannerRelations(Booking $booking): void
    {
        $booking->loadMissing([
            'user',
            'bookingDetails.field',
            'bookingDetails.timeSlot',
            'payments',
        ]);
    }

    /**
     * Giữ đúng tên biến mà index.blade.php hiện tại đang sử dụng.
     */
    private function scannerView(
        ?Booking $booking = null,
        array $checkIn = [],
        ?string $lookupError = null,
    ): View {
        return view('admin.bookings.check-in.index', [
            'booking' => $booking,
            'checkIn' => $checkIn,
            'lookupError' => $lookupError,
        ]);
    }

    private function bookingCode(Booking $booking): string
    {
        return (string) (
            $booking->booking_code
            ?? $booking->code
            ?? $booking->getKey()
        );
    }

    private function now(): CarbonImmutable
    {
        return CarbonImmutable::now(
            (string) config(
                'booking_lifecycle.timezone',
                config('app.timezone', 'Asia/Ho_Chi_Minh'),
            )
        );
    }

    /**
     * Chỉ ghi các cột thật sự tồn tại trong database hiện tại.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function filterColumns(
        string $table,
        array $data,
    ): array {
        if (! Schema::hasTable($table)) {
            return [];
        }

        return collect($data)
            ->filter(
                fn ($value, $column): bool =>
                    Schema::hasColumn($table, (string) $column)
            )
            ->all();
    }
}