@extends('layouts.app')

@section('title', 'Chi tiết đơn đặt sân')

@push('styles')
<style>
    .booking-shell {
        --flow-primary: #2563eb;
        --flow-success: #16a34a;
        --flow-warning: #d97706;
        --flow-danger: #dc2626;
        --flow-muted: #64748b;
    }

    .booking-card {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15, 23, 42, .07);
    }

    .booking-hero {
        position: relative;
        overflow: hidden;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, .22), transparent 34%),
            linear-gradient(135deg, #0f172a, #1d4ed8);
    }

    .booking-hero::after {
        content: "";
        position: absolute;
        right: -70px;
        bottom: -90px;
        width: 230px;
        height: 230px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
    }

    .phase-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border: 1px solid rgba(255, 255, 255, .22);
        border-radius: 999px;
        background: rgba(255, 255, 255, .13);
        backdrop-filter: blur(8px);
        font-weight: 700;
    }

    .flow-grid {
        position: relative;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .flow-grid::before {
        content: "";
        position: absolute;
        top: 25px;
        left: 11%;
        right: 11%;
        height: 3px;
        background: #e2e8f0;
    }

    .flow-step {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .flow-step-icon {
        width: 52px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 5px solid #fff;
        border-radius: 50%;
        color: #64748b;
        background: #e2e8f0;
        font-size: 20px;
        transition: .2s ease;
    }

    .flow-step.is-done .flow-step-icon {
        color: #fff;
        background: var(--flow-success);
    }

    .flow-step.is-current .flow-step-icon {
        color: #fff;
        background: var(--flow-primary);
        box-shadow: 0 0 0 7px rgba(37, 99, 235, .13);
    }

    .flow-step.is-warning .flow-step-icon {
        color: #fff;
        background: var(--flow-warning);
        box-shadow: 0 0 0 7px rgba(217, 119, 6, .13);
    }

    .flow-step.is-failed .flow-step-icon {
        color: #fff;
        background: var(--flow-danger);
        box-shadow: 0 0 0 7px rgba(220, 38, 38, .13);
    }

    .action-panel {
        border: 1px solid #dbeafe;
        border-radius: 20px;
        background: linear-gradient(145deg, #eff6ff, #ffffff);
    }

    .time-box {
        height: 100%;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
    }

    .countdown-box {
        padding: 14px;
        border-radius: 16px;
        background: #0f172a;
        color: #fff;
        text-align: center;
    }

    .countdown-value {
        font-size: 1.55rem;
        font-weight: 800;
        letter-spacing: .03em;
    }

    .checkin-button {
        min-height: 54px;
        border: 0;
        border-radius: 14px;
        font-size: 1.05rem;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(22, 163, 74, .22);
    }

    .checkin-button:disabled {
        box-shadow: none;
    }

    .info-line {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .info-line:last-child {
        border-bottom: 0;
    }

    @media (max-width: 767.98px) {
        .flow-grid {
            grid-template-columns: 1fr;
        }

        .flow-grid::before {
            display: none;
        }

        .flow-step {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }

        .flow-step-icon {
            flex: 0 0 52px;
        }
    }

    .booking-header-actions .btn {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding-inline: 16px;
        font-weight: 700;
        box-shadow: 0 7px 18px rgba(15, 23, 42, .055);
    }

    .booking-invoice-button {
        border-color: #334155;
        color: #334155;
        background: #fff;
    }

    .booking-invoice-button:hover {
        border-color: #0f172a;
        color: #fff;
        background: #0f172a;
    }

    .booking-card {
        border: 1px solid rgba(226, 232, 240, .72);
        transition: box-shadow .18s ease;
    }

    .booking-card:hover {
        box-shadow: 0 16px 42px rgba(15, 23, 42, .085);
    }

</style>
@endpush

@section('content')
@php
    $bookingDetails = collect(
        $bookingDetails
        ?? $booking->bookingDetails
        ?? []
    );

    $bookingServices = collect(
        $bookingServices
        ?? $booking->bookingServices
        ?? []
    );

    $timezone = (string) config(
        'booking_lifecycle.timezone',
        config('app.timezone', 'Asia/Ho_Chi_Minh')
    );

    $now = \Carbon\CarbonImmutable::now($timezone);
    $status = strtolower((string) ($booking->status ?? 'pending'));
    $usageStatus = strtolower((string) (
        $booking->usage_status
        ?? 'not_checked_in'
    ));
    $paymentStatus = strtolower((string) (
        $booking->payment_status
        ?? 'unpaid'
    ));

    $totalPayable = max(
        0,
        (float) (
            $booking->final_amount
            ?? $booking->total_amount
            ?? $booking->total_price
            ?? $booking->total
            ?? 0
        )
    );

    $depositAmount = max(
        0,
        (float) ($booking->deposit_amount ?? 0)
    );

    $effectivePaid = max(
        0,
        (float) ($booking->paid_amount ?? 0)
    );

    if (
        $paymentStatus === 'deposit_paid'
        || (bool) ($booking->is_deposit_paid ?? false)
    ) {
        $effectivePaid = max($effectivePaid, $depositAmount);
    }

    if (in_array($paymentStatus, ['paid', 'paid_full', 'completed'], true)) {
        $effectivePaid = max(
            $effectivePaid,
            $totalPayable,
            $depositAmount
        );
    }

    $isFullPayment = in_array(
        $paymentStatus,
        ['paid', 'paid_full', 'completed'],
        true
    ) || (
        $totalPayable > 0
        && $effectivePaid >= $totalPayable
    );

    $isPaid = $effectivePaid > 0
        || in_array(
            $paymentStatus,
            ['deposit_paid', 'paid', 'paid_full', 'completed'],
            true
        )
        || (bool) ($booking->is_deposit_paid ?? false);

    $checkInEarlyMinutes = max(
        0,
        (int) config(
            'booking_lifecycle.check_in_early_minutes',
            15
        )
    );

    $graceMinutes = $isFullPayment
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

    $paymentPolicyText = $isFullPayment
        ? 'Thanh toán đủ: quá '.$graceMinutes.' phút sẽ mất toàn bộ số tiền đã thanh toán.'
        : 'Đặt cọc: quá '.$graceMinutes.' phút sẽ mất tiền cọc.';

    $windows = $bookingDetails
        ->map(function ($detail) use ($booking, $timezone) {
            $dateValue = data_get($detail, 'booking_date')
                ?? data_get($detail, 'date')
                ?? data_get($booking, 'booking_date');

            $startValue = data_get($detail, 'slot_start_time')
                ?? data_get($detail, 'start_time')
                ?? data_get($detail, 'timeSlot.start_time')
                ?? data_get($detail, 'time_slot.start_time');

            $endValue = data_get($detail, 'slot_end_time')
                ?? data_get($detail, 'end_time')
                ?? data_get($detail, 'timeSlot.end_time')
                ?? data_get($detail, 'time_slot.end_time');

            if (!$dateValue || !$startValue || !$endValue) {
                return null;
            }

            try {
                $date = $dateValue instanceof \Carbon\CarbonInterface
                    ? $dateValue->format('Y-m-d')
                    : \Carbon\CarbonImmutable::parse(
                        $dateValue,
                        $timezone
                    )->format('Y-m-d');

                $startsAt = \Carbon\CarbonImmutable::parse(
                    $date.' '.$startValue,
                    $timezone
                );

                $endsAt = \Carbon\CarbonImmutable::parse(
                    $date.' '.$endValue,
                    $timezone
                );

                if ($endsAt->lessThanOrEqualTo($startsAt)) {
                    $endsAt = $endsAt->addDay();
                }

                return [
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ];
            } catch (\Throwable) {
                return null;
            }
        })
        ->filter()
        ->sortBy(fn (array $window) => $window['starts_at']->getTimestamp())
        ->values();

    $startsAt = data_get($windows->first(), 'starts_at');
    $endsAt = $windows->isNotEmpty()
        ? $windows->max(
            fn (array $window) => $window['ends_at']->getTimestamp()
        )
        : null;

    if (is_int($endsAt)) {
        $endsAt = \Carbon\CarbonImmutable::createFromTimestamp(
            $endsAt,
            $timezone
        );
    }

    $opensAt = $startsAt?->subMinutes($checkInEarlyMinutes);
    $deadlineAt = $startsAt?->addMinutes($graceMinutes);

    $checkedInAt = !empty($booking->checked_in_at)
        ? \Carbon\CarbonImmutable::parse(
            $booking->checked_in_at,
            $timezone
        )
        : null;

    $checkedOutAt = !empty($booking->checked_out_at)
        ? \Carbon\CarbonImmutable::parse(
            $booking->checked_out_at,
            $timezone
        )
        : null;

    $noShowAt = !empty($booking->no_show_at)
        ? \Carbon\CarbonImmutable::parse(
            $booking->no_show_at,
            $timezone
        )
        : null;

    $phase = 'waiting_payment';
    $phaseLabel = 'Chờ thanh toán';
    $phaseDescription = 'Đơn chưa đủ điều kiện mở check-in.';
    $phaseColor = 'secondary';
    $phaseIcon = 'bi-credit-card';

    if ($noShowAt) {
        $phase = 'no_show';
        $phaseLabel = $isFullPayment
            ? 'No-show – mất toàn bộ tiền'
            : 'No-show – mất tiền cọc';
        $phaseDescription = 'Khách không check-in đúng hạn nên đơn đã tự động bị hủy.';
        $phaseColor = 'danger';
        $phaseIcon = 'bi-person-x-fill';
    } elseif ($status === 'cancelled') {
        $phase = 'cancelled';
        $phaseLabel = 'Đơn đã hủy';
        $phaseDescription = 'Đơn không còn hiệu lực.';
        $phaseColor = 'danger';
        $phaseIcon = 'bi-x-circle-fill';
    } elseif ($usageStatus === 'checked_out' || $status === 'completed') {
        $phase = 'checked_out';
        $phaseLabel = 'Đã check-out';
        $phaseDescription = 'Phiên sử dụng sân đã hoàn tất.';
        $phaseColor = 'success';
        $phaseIcon = 'bi-check2-all';
    } elseif ($usageStatus === 'checked_in') {
        $phase = 'checked_in';
        $phaseLabel = 'Đang sử dụng sân';
        $phaseDescription = 'Bạn đã check-in. Hệ thống sẽ tự check-out khi hết giờ.';
        $phaseColor = 'info';
        $phaseIcon = 'bi-dribbble';
    } elseif ($status === 'pending' || !$isPaid) {
        $phase = 'waiting_payment';
    } elseif (!$startsAt || !$endsAt || !$opensAt || !$deadlineAt) {
        $phase = 'missing_schedule';
        $phaseLabel = 'Thiếu thông tin lịch sân';
        $phaseDescription = 'Không xác định được thời gian mở check-in.';
        $phaseColor = 'danger';
        $phaseIcon = 'bi-calendar-x';
    } elseif ($now->lessThan($opensAt)) {
        $phase = 'upcoming';
        $phaseLabel = 'Chưa đến giờ check-in';
        $phaseDescription = 'Nút check-in sẽ mở trước giờ đá '.$checkInEarlyMinutes.' phút.';
        $phaseColor = 'secondary';
        $phaseIcon = 'bi-clock';
    } elseif ($now->lessThan($startsAt)) {
        $phase = 'check_in_open';
        $phaseLabel = 'Đã mở check-in';
        $phaseDescription = 'Hãy chỉ check-in khi bạn đã có mặt tại sân.';
        $phaseColor = 'primary';
        $phaseIcon = 'bi-unlock-fill';
    } elseif ($now->lessThan($deadlineAt)) {
        $phase = 'waiting_check_in';
        $phaseLabel = 'Giờ sân đã bắt đầu';
        $phaseDescription = 'Bạn vẫn có thể check-in trước hạn cuối.';
        $phaseColor = 'warning';
        $phaseIcon = 'bi-person-clock';
    } else {
        $phase = 'overdue';
        $phaseLabel = 'Đã quá hạn check-in';
        $phaseDescription = 'Hệ thống đang xử lý đơn vắng mặt.';
        $phaseColor = 'danger';
        $phaseIcon = 'bi-exclamation-octagon-fill';
    }

    $canCheckIn = in_array(
        $phase,
        ['check_in_open', 'waiting_check_in'],
        true
    )
        && $status === 'confirmed'
        && $usageStatus === 'not_checked_in'
        && $isPaid;

    $orderStatusMap = [
        'pending' => ['Chờ thanh toán', 'warning'],
        'confirmed' => ['Đã xác nhận', 'primary'],
        'completed' => ['Hoàn thành', 'success'],
        'cancelled' => ['Đã hủy', 'danger'],
    ];

    [$orderStatusText, $orderStatusColor] = $orderStatusMap[$status]
        ?? [ucfirst($status), 'secondary'];

    $customerName = data_get($booking, 'user.name')
        ?? $booking->customer_name
        ?? $booking->name
        ?? optional(auth()->user())->name
        ?? 'Khách hàng';

    $customerPhone = $booking->customer_phone
        ?? $booking->phone
        ?? data_get($booking, 'user.phone')
        ?? '-';

    $customerEmail = data_get($booking, 'user.email')
        ?? $booking->customer_email
        ?? $booking->email
        ?? optional(auth()->user())->email
        ?? '-';

    $forfeitedAmount = max(
        0,
        (float) (
            $booking->no_show_forfeited_amount
            ?? $booking->deposit_forfeited_amount
            ?? 0
        )
    );

    $stepConfirmClass = in_array(
        $status,
        ['confirmed', 'completed'],
        true
    ) || in_array(
        $phase,
        ['checked_in', 'checked_out', 'no_show'],
        true
    )
        ? 'is-done'
        : 'is-current';

    $stepCheckInClass = match ($phase) {
        'checked_in', 'checked_out' => 'is-done',
        'check_in_open' => 'is-current',
        'waiting_check_in', 'overdue' => 'is-warning',
        'no_show' => 'is-failed',
        default => '',
    };

    $stepUseClass = match ($phase) {
        'checked_in' => 'is-current',
        'checked_out' => 'is-done',
        default => '',
    };

    $stepCheckOutClass = $phase === 'checked_out'
        ? 'is-done'
        : '';
@endphp

<div class="container py-4 booking-shell">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                <h3 class="fw-bold mb-0">
                    Đơn đặt sân #{{ $booking->id }}
                </h3>

                <span class="badge bg-{{ $orderStatusColor }}-subtle text-{{ $orderStatusColor }}">
                    {{ $orderStatusText }}
                </span>
            </div>

            <div class="text-muted">
                {{ $booking->booking_code
                    ?? $booking->code
                    ?? 'Không có mã đơn' }}
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 booking-header-actions">
            @if(\Illuminate\Support\Facades\Route::has('user.bookings.invoice'))
                <a
                    href="{{ route('user.bookings.invoice', $booking->id) }}"
                    class="btn booking-invoice-button rounded-3"
                    target="_blank"
                >
                    <i class="bi bi-receipt-cutoff me-2"></i>
                    Xem hóa đơn
                </a>
            @endif

            <a
                href="{{ route('user.bookings.index') }}"
                class="btn btn-light border rounded-3"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Đơn của tôi
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->has('check_in'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first('check_in') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card booking-card booking-hero mb-4">
        <div class="card-body position-relative p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8 position-relative" style="z-index: 1;">
                    <span class="phase-pill mb-3">
                        <i class="bi {{ $phaseIcon }}"></i>
                        {{ $phaseLabel }}
                    </span>

                    <h2 class="fw-bold mb-2">
                        {{ $phaseDescription }}
                    </h2>

                    <p class="mb-0 text-white-50">
                        {{ $paymentPolicyText }}
                    </p>
                </div>

                <div class="col-lg-4 position-relative" style="z-index: 1;">
                    <div class="p-3 rounded-4" style="background: rgba(255,255,255,.12);">
                        <div class="small text-white-50">Thời gian sân</div>
                        <div class="fw-bold fs-5 mt-1">
                            {{ $startsAt?->format('H:i') ?? '-' }}
                            –
                            {{ $endsAt?->format('H:i') ?? '-' }}
                        </div>
                        <div class="small text-white-50 mt-1">
                            {{ $startsAt?->format('d/m/Y') ?? 'Chưa xác định' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card booking-card mb-4">
        <div class="card-body p-4">
            <div class="flow-grid">
                <div class="flow-step {{ $stepConfirmClass }}">
                    <span class="flow-step-icon">
                        <i class="bi bi-patch-check"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Xác nhận đơn</div>
                        <div class="small text-muted">
                            Thanh toán hợp lệ
                        </div>
                    </div>
                </div>

                <div class="flow-step {{ $stepCheckInClass }}">
                    <span class="flow-step-icon">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Khách check-in</div>
                        <div class="small text-muted">
                            Xác nhận đã có mặt
                        </div>
                    </div>
                </div>

                <div class="flow-step {{ $stepUseClass }}">
                    <span class="flow-step-icon">
                        <i class="bi bi-dribbble"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Sử dụng sân</div>
                        <div class="small text-muted">
                            Đang trong khung giờ
                        </div>
                    </div>
                </div>

                <div class="flow-step {{ $stepCheckOutClass }}">
                    <span class="flow-step-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Tự check-out</div>
                        <div class="small text-muted">
                            Khi hết giờ sân
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card booking-card mb-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-0">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                        Check-in tại sân
                    </h5>
                    <div class="text-muted small">
                        Chỉ bấm khi bạn đã có mặt. Check-out được thực hiện tự động.
                    </div>
                </div>

                <span class="badge bg-{{ $phaseColor }}-subtle text-{{ $phaseColor }} px-3 py-2">
                    <i class="bi {{ $phaseIcon }} me-1"></i>
                    {{ $phaseLabel }}
                </span>
            </div>
        </div>

        <div class="card-body p-4">
            @if($phase === 'no_show')
                <div class="alert alert-danger rounded-4 mb-0">
                    <div class="d-flex gap-3">
                        <i class="bi bi-person-x-fill fs-3"></i>
                        <div>
                            <div class="fw-bold fs-5">
                                Đơn đã bị hủy do không check-in đúng hạn
                            </div>
                            <div class="mt-1">
                                Thời điểm xử lý:
                                <strong>{{ $noShowAt?->format('H:i d/m/Y') ?? '-' }}</strong>.
                                Số tiền bị giữ:
                                <strong>{{ number_format($forfeitedAmount, 0, ',', '.') }}đ</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($phase === 'checked_out')
                <div class="alert alert-success rounded-4 mb-0">
                    <div class="d-flex gap-3">
                        <i class="bi bi-check2-all fs-3"></i>
                        <div>
                            <div class="fw-bold fs-5">Phiên sử dụng sân đã hoàn tất</div>
                            <div class="mt-1">
                                Check-in:
                                <strong>{{ $checkedInAt?->format('H:i d/m/Y') ?? '-' }}</strong><br>
                                Check-out:
                                <strong>{{ $checkedOutAt?->format('H:i d/m/Y') ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($phase === 'checked_in')
                <div class="alert alert-info rounded-4 mb-0">
                    <div class="d-flex gap-3">
                        <i class="bi bi-dribbble fs-3"></i>
                        <div>
                            <div class="fw-bold fs-5">Bạn đang sử dụng sân</div>
                            <div class="mt-1">
                                Đã check-in lúc
                                <strong>{{ $checkedInAt?->format('H:i d/m/Y') ?? '-' }}</strong>.
                                Hệ thống sẽ tự check-out
                                @if($endsAt)
                                    lúc <strong>{{ $endsAt->format('H:i d/m/Y') }}</strong>.
                                @else
                                    khi hết khung giờ.
                                @endif
                            </div>
                            <div class="small mt-2">
                                Bạn không cần và không thể bấm check-out thủ công.
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    <div class="col-lg-7">
                        <div class="row g-3 h-100">
                            <div class="col-sm-6">
                                <div class="time-box">
                                    <div class="text-muted small">Mở check-in</div>
                                    <div class="fw-bold mt-1">
                                        {{ $opensAt?->format('H:i d/m/Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="time-box">
                                    <div class="text-muted small">Giờ bắt đầu sân</div>
                                    <div class="fw-bold mt-1">
                                        {{ $startsAt?->format('H:i d/m/Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="time-box">
                                    <div class="text-muted small">Hạn cuối check-in</div>
                                    <div class="fw-bold text-danger mt-1">
                                        {{ $deadlineAt?->format('H:i d/m/Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="time-box">
                                    <div class="text-muted small">Tự check-out</div>
                                    <div class="fw-bold text-success mt-1">
                                        {{ $endsAt?->format('H:i d/m/Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="action-panel p-3 h-100 d-flex flex-column justify-content-center">
                            @if(in_array($phase, ['upcoming', 'check_in_open', 'waiting_check_in'], true))
                                <div
                                    class="countdown-box mb-3"
                                    data-countdown
                                    data-target="{{ (
                                        $phase === 'upcoming'
                                            ? $opensAt
                                            : $deadlineAt
                                    )?->toIso8601String() }}"
                                    data-prefix="{{ $phase === 'upcoming'
                                        ? 'Mở check-in sau'
                                        : 'Còn thời gian check-in' }}"
                                >
                                    <div class="small text-white-50" data-countdown-label>
                                        {{ $phase === 'upcoming'
                                            ? 'Mở check-in sau'
                                            : 'Còn thời gian check-in' }}
                                    </div>
                                    <div class="countdown-value" data-countdown-value>
                                        --:--:--
                                    </div>
                                </div>
                            @endif

                            @if($canCheckIn)
                                <form
                                    method="POST"
                                    action="{{ route('user.bookings.check-in', $booking->id) }}"
                                    data-checkin-form
                                >
                                    @csrf

                                    <div class="form-check mb-3">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="presenceConfirmation"
                                            data-presence-confirmation
                                        >
                                        <label
                                            class="form-check-label fw-semibold"
                                            for="presenceConfirmation"
                                        >
                                            Tôi xác nhận mình đã có mặt tại sân.
                                        </label>
                                    </div>

                                    <button
                                        type="submit"
                                        class="btn btn-success checkin-button w-100"
                                        data-checkin-button
                                        disabled
                                    >
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Tôi đã có mặt – Check-in
                                    </button>
                                </form>

                                <div class="small text-muted text-center mt-2">
                                    Admin sẽ thấy trạng thái ngay sau khi bạn check-in.
                                </div>
                            @elseif($phase === 'upcoming')
                                <button
                                    type="button"
                                    class="btn btn-secondary checkin-button w-100"
                                    disabled
                                >
                                    <i class="bi bi-lock-fill me-2"></i>
                                    Chưa đến giờ check-in
                                </button>
                            @elseif($phase === 'overdue')
                                <button
                                    type="button"
                                    class="btn btn-danger checkin-button w-100"
                                    disabled
                                >
                                    <i class="bi bi-x-octagon-fill me-2"></i>
                                    Đã quá hạn check-in
                                </button>
                            @elseif($phase === 'waiting_payment')
                                <button
                                    type="button"
                                    class="btn btn-warning checkin-button w-100"
                                    disabled
                                >
                                    <i class="bi bi-credit-card me-2"></i>
                                    Chờ xác nhận thanh toán
                                </button>
                            @elseif($phase === 'cancelled')
                                <button
                                    type="button"
                                    class="btn btn-secondary checkin-button w-100"
                                    disabled
                                >
                                    <i class="bi bi-x-circle me-2"></i>
                                    Đơn đã hủy
                                </button>
                            @else
                                <button
                                    type="button"
                                    class="btn btn-secondary checkin-button w-100"
                                    disabled
                                >
                                    <i class="bi bi-calendar-x me-2"></i>
                                    Chưa đủ dữ liệu lịch sân
                                </button>
                            @endif

                            <div class="small text-muted mt-3">
                                <i class="bi bi-shield-check me-1"></i>
                                {{ $paymentPolicyText }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card booking-card h-100">
                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-calendar-event text-primary me-2"></i>
                        Sân và khung giờ
                    </h5>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Sân</th>
                                <th>Ngày</th>
                                <th>Khung giờ</th>
                                <th class="text-end pe-4">Giá</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($bookingDetails as $detail)
                                @php
                                    $detailDate = data_get($detail, 'booking_date')
                                        ?? data_get($detail, 'date')
                                        ?? data_get($booking, 'booking_date');

                                    $detailStart = data_get($detail, 'slot_start_time')
                                        ?? data_get($detail, 'start_time')
                                        ?? data_get($detail, 'timeSlot.start_time');

                                    $detailEnd = data_get($detail, 'slot_end_time')
                                        ?? data_get($detail, 'end_time')
                                        ?? data_get($detail, 'timeSlot.end_time');

                                    $fieldName = data_get($detail, 'field.name')
                                        ?? data_get($detail, 'field.field_name')
                                        ?? data_get($detail, 'field_name')
                                        ?? 'Sân chưa xác định';

                                    $detailPrice = data_get($detail, 'price')
                                        ?? data_get($detail, 'field_price')
                                        ?? data_get($detail, 'field_price_per_hour')
                                        ?? 0;
                                @endphp

                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $fieldName }}</td>
                                    <td>
                                        {{ $detailDate
                                            ? \Carbon\Carbon::parse($detailDate)->format('d/m/Y')
                                            : '-' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $detailStart ?? '-' }} – {{ $detailEnd ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success">
                                        {{ number_format((float) $detailPrice, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        Chưa có thông tin sân.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card booking-card h-100">
                <div class="card-header bg-white border-0 px-4 pt-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        Thông tin đơn
                    </h5>
                </div>

                <div class="card-body px-4">
                    <div class="info-line">
                        <span class="text-muted">Khách hàng</span>
                        <strong class="text-end">{{ $customerName }}</strong>
                    </div>

                    <div class="info-line">
                        <span class="text-muted">Điện thoại</span>
                        <strong class="text-end">{{ $customerPhone }}</strong>
                    </div>

                    <div class="info-line">
                        <span class="text-muted">Email</span>
                        <strong class="text-end text-break">{{ $customerEmail }}</strong>
                    </div>

                    <div class="info-line">
                        <span class="text-muted">Thanh toán</span>
                        <strong class="text-end">
                            {{ $isFullPayment
                                ? 'Thanh toán đủ'
                                : ($isPaid ? 'Đã đặt cọc' : 'Chưa thanh toán') }}
                        </strong>
                    </div>

                    <div class="info-line">
                        <span class="text-muted">Đã trả</span>
                        <strong class="text-end text-success">
                            {{ number_format($effectivePaid, 0, ',', '.') }}đ
                        </strong>
                    </div>

                    <div class="info-line">
                        <span class="text-muted">Tổng tiền</span>
                        <strong class="text-end">
                            {{ number_format($totalPayable, 0, ',', '.') }}đ
                        </strong>
                    </div>

                    <div class="info-line">
                        <span class="text-muted">Ghi chú</span>
                        <strong class="text-end">{{ $booking->note ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($bookingServices->isNotEmpty())
        <div class="card booking-card">
            <div class="card-header bg-white border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-basket text-primary me-2"></i>
                    Dịch vụ đi kèm
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Dịch vụ</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th class="text-end pe-4">Thành tiền</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($bookingServices as $item)
                            @php
                                $quantity = (int) ($item->quantity ?? 0);
                                $price = (float) ($item->price ?? 0);
                                $lineTotal = (float) (
                                    $item->total_price
                                    ?? $item->total
                                    ?? ($quantity * $price)
                                );
                            @endphp

                            <tr>
                                <td class="ps-4 fw-semibold">
                                    {{ data_get($item, 'service.name')
                                        ?? data_get($item, 'service.service_name')
                                        ?? $item->service_name
                                        ?? 'Dịch vụ' }}
                                </td>
                                <td>{{ $quantity }}</td>
                                <td>{{ number_format($price, 0, ',', '.') }}đ</td>
                                <td class="text-end pe-4 fw-bold">
                                    {{ number_format($lineTotal, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmation = document.querySelector(
            '[data-presence-confirmation]'
        );
        const checkInButton = document.querySelector(
            '[data-checkin-button]'
        );
        const checkInForm = document.querySelector(
            '[data-checkin-form]'
        );

        if (confirmation && checkInButton) {
            confirmation.addEventListener('change', function () {
                checkInButton.disabled = !confirmation.checked;
            });
        }

        if (checkInForm) {
            checkInForm.addEventListener('submit', function (event) {
                const accepted = window.confirm(
                    'Xác nhận bạn đang có mặt tại sân và muốn check-in?'
                );

                if (!accepted) {
                    event.preventDefault();
                    return;
                }

                if (checkInButton) {
                    checkInButton.disabled = true;
                    checkInButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>'
                        + 'Đang check-in...';
                }
            });
        }

        document.querySelectorAll('[data-countdown]').forEach(function (box) {
            const value = box.querySelector('[data-countdown-value]');
            const targetText = box.dataset.target;

            if (!value || !targetText) {
                return;
            }

            const target = new Date(targetText).getTime();

            const update = function () {
                const remaining = target - Date.now();

                if (remaining <= 0) {
                    value.textContent = '00:00:00';
                    window.location.reload();
                    return;
                }

                const totalSeconds = Math.floor(remaining / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                const clock = [
                    String(hours).padStart(2, '0'),
                    String(minutes).padStart(2, '0'),
                    String(seconds).padStart(2, '0'),
                ].join(':');

                value.textContent = days > 0
                    ? days + ' ngày ' + clock
                    : clock;
            };

            update();
            window.setInterval(update, 1000);
        });

        const livePhases = [
            'upcoming',
            'check_in_open',
            'waiting_check_in',
            'checked_in',
            'overdue'
        ];

        if (livePhases.includes(@json($phase))) {
            window.setTimeout(function () {
                if (!document.hidden) {
                    window.location.reload();
                }
            }, 20000);
        }
    });
</script>
@endpush