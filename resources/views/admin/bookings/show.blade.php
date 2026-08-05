@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn đặt sân')

@push('styles')
<style>
    .admin-booking-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
    }

    .lifecycle-hero {
        overflow: hidden;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, .22), transparent 34%),
            linear-gradient(135deg, #0f172a, #1d4ed8);
    }

    .lifecycle-state-icon {
        width: 62px;
        height: 62px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        color: #fff;
        background: rgba(255, 255, 255, .15);
        font-size: 27px;
    }

    .admin-flow {
        position: relative;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    .admin-flow::before {
        content: "";
        position: absolute;
        top: 27px;
        left: 10%;
        right: 10%;
        height: 3px;
        background: #e2e8f0;
    }

    .admin-flow-step {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .admin-flow-icon {
        width: 56px;
        height: 56px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 5px solid #fff;
        border-radius: 50%;
        color: #64748b;
        background: #e2e8f0;
        font-size: 21px;
    }

    .admin-flow-step.done .admin-flow-icon {
        color: #fff;
        background: #16a34a;
    }

    .admin-flow-step.current .admin-flow-icon {
        color: #fff;
        background: #2563eb;
        box-shadow: 0 0 0 7px rgba(37, 99, 235, .12);
    }

    .admin-flow-step.warning .admin-flow-icon {
        color: #fff;
        background: #d97706;
        box-shadow: 0 0 0 7px rgba(217, 119, 6, .12);
    }

    .admin-flow-step.failed .admin-flow-icon {
        color: #fff;
        background: #dc2626;
        box-shadow: 0 0 0 7px rgba(220, 38, 38, .12);
    }

    .summary-tile {
        height: 100%;
        padding: 18px;
        border: 1px solid #e5e7eb;
        border-radius: 17px;
        background: #fff;
    }

    .admin-info-row {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 11px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .admin-info-row:last-child {
        border-bottom: 0;
    }

    @media (max-width: 767.98px) {
        .admin-flow {
            grid-template-columns: 1fr;
        }

        .admin-flow::before {
            display: none;
        }

        .admin-flow-step {
            display: flex;
            align-items: center;
            gap: 12px;
            text-align: left;
        }
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

    $payments = collect(
        $payments
        ?? $booking->payments
        ?? []
    );

    $statusHistories = collect($statusHistories ?? []);
    $lifecycle = (array) ($lifecycle ?? []);

    $status = strtolower((string) ($booking->status ?? 'pending'));
    $usageStatus = strtolower((string) (
        $booking->usage_status
        ?? 'not_checked_in'
    ));
    $paymentStatus = strtolower((string) (
        $booking->payment_status
        ?? 'unpaid'
    ));

    $phase = (string) ($lifecycle['phase'] ?? 'unknown');
    $phaseLabel = (string) (
        $lifecycle['label']
        ?? 'Chưa xác định'
    );
    $phaseDescription = (string) (
        $lifecycle['description']
        ?? 'Chưa có thông tin vòng đời.'
    );
    $phaseColor = (string) ($lifecycle['color'] ?? 'secondary');
    $phaseIcon = (string) ($lifecycle['icon'] ?? 'bi-question-circle');

    $startsAt = $lifecycle['starts_at'] ?? null;
    $endsAt = $lifecycle['ends_at'] ?? null;
    $opensAt = $lifecycle['opens_at'] ?? null;
    $deadlineAt = $lifecycle['deadline_at'] ?? null;
    $graceMinutes = (int) ($lifecycle['grace_minutes'] ?? 15);
    $paymentType = (string) ($lifecycle['payment_type'] ?? 'deposit');

    $checkedInAt = !empty($booking->checked_in_at)
        ? \Carbon\CarbonImmutable::parse($booking->checked_in_at)
        : null;

    $checkedOutAt = !empty($booking->checked_out_at)
        ? \Carbon\CarbonImmutable::parse($booking->checked_out_at)
        : null;

    $noShowAt = !empty($booking->no_show_at)
        ? \Carbon\CarbonImmutable::parse($booking->no_show_at)
        : null;

    $orderStatusMap = [
        'pending' => ['Chờ thanh toán', 'warning'],
        'confirmed' => ['Đã xác nhận', 'primary'],
        'completed' => ['Hoàn thành', 'success'],
        'cancelled' => ['Đã hủy', 'danger'],
    ];

    $paymentStatusMap = [
        'unpaid' => ['Chưa thanh toán', 'secondary'],
        'deposit_paid' => ['Đã đặt cọc', 'warning'],
        'paid' => ['Đã thanh toán đủ', 'success'],
        'paid_full' => ['Đã thanh toán đủ', 'success'],
        'completed' => ['Đã thanh toán đủ', 'success'],
        'partially_refunded' => ['Hoàn một phần', 'info'],
        'refunded' => ['Đã hoàn tiền', 'primary'],
    ];

    [$orderStatusText, $orderStatusColor] = $orderStatusMap[$status]
        ?? [ucfirst($status), 'secondary'];

    [$paymentStatusText, $paymentStatusColor] = $paymentStatusMap[$paymentStatus]
        ?? [ucfirst($paymentStatus), 'secondary'];

    $totalMoney = max(
        0,
        (float) (
            $booking->final_amount
            ?? $booking->total_amount
            ?? $booking->total_price
            ?? $booking->total
            ?? 0
        )
    );

    $paidAmount = max(
        0,
        (float) ($booking->paid_amount ?? 0)
    );

    if (
        in_array($paymentStatus, ['paid', 'paid_full', 'completed'], true)
        && $paidAmount <= 0
    ) {
        $paidAmount = $totalMoney;
    }

    $remainingAmount = max(0, $totalMoney - $paidAmount);

    $depositAmount = max(
        0,
        (float) ($booking->deposit_amount ?? 0)
    );

    $forfeitedAmount = max(
        0,
        (float) (
            $booking->no_show_forfeited_amount
            ?? $booking->deposit_forfeited_amount
            ?? 0
        )
    );

    $refundAmount = max(
        0,
        (float) ($booking->refund_amount ?? 0)
    );

    $customerName = data_get($booking, 'user.name')
        ?? $booking->customer_name
        ?? $booking->name
        ?? 'Khách hàng';

    $customerPhone = $booking->customer_phone
        ?? $booking->phone
        ?? data_get($booking, 'user.phone')
        ?? '-';

    $customerEmail = data_get($booking, 'user.email')
        ?? $booking->customer_email
        ?? $booking->email
        ?? '-';

    $confirmStep = in_array(
        $status,
        ['confirmed', 'completed'],
        true
    ) || in_array(
        $phase,
        ['checked_in', 'checked_out', 'no_show'],
        true
    )
        ? 'done'
        : 'current';

    $checkInStep = match ($phase) {
        'checked_in', 'checked_out' => 'done',
        'check_in_open' => 'current',
        'waiting_check_in', 'overdue_waiting_scheduler' => 'warning',
        'no_show' => 'failed',
        default => '',
    };

    $useStep = match ($phase) {
        'checked_in' => 'current',
        'checked_out' => 'done',
        default => '',
    };

    $checkOutStep = $phase === 'checked_out'
        ? 'done'
        : '';

    $policyText = $paymentType === 'full'
        ? 'Thanh toán đủ: quá '.$graceMinutes.' phút không check-in sẽ mất toàn bộ số tiền.'
        : 'Đặt cọc: quá '.$graceMinutes.' phút không check-in sẽ mất tiền cọc.';
@endphp

<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h3 class="fw-bold mb-0">
                    Đơn đặt sân #{{ $booking->id }}
                </h3>

                <span class="badge bg-{{ $orderStatusColor }}-subtle text-{{ $orderStatusColor }}">
                    {{ $orderStatusText }}
                </span>
            </div>

            <div class="text-muted mt-1">
                {{ $booking->booking_code
                    ?? $booking->code
                    ?? 'Không có mã đơn' }}
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if(\Illuminate\Support\Facades\Route::has('admin.bookings.invoice'))
                <a
                    href="{{ route('admin.bookings.invoice', $booking->id) }}"
                    class="btn btn-outline-primary"
                    target="_blank"
                >
                    <i class="bi bi-receipt me-1"></i>
                    Hóa đơn
                </a>
            @endif

            @if(
                in_array(
                    strtolower((string) ($booking->status ?? '')),
                    ['cancelled', 'completed'],
                    true
                )
                && strtolower((string) (
                    $booking->usage_status
                    ?? 'not_checked_in'
                )) !== 'checked_in'
                && \Illuminate\Support\Facades\Route::has(
                    'admin.bookings.destroy'
                )
            )
                <form
                    method="POST"
                    action="{{ route(
                        'admin.bookings.destroy',
                        $booking->id
                    ) }}"
                    onsubmit="return confirm(
                        'Xóa vĩnh viễn đơn #{{ $booking->id }}? '
                        + 'Thao tác này không thể hoàn tác.'
                    );"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="btn btn-outline-danger"
                    >
                        <i class="bi bi-trash3-fill me-1"></i>
                        Xóa đơn
                    </button>
                </form>
            @endif

            <a
                href="{{ route('admin.bookings.index') }}"
                class="btn btn-light border"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Danh sách đơn
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card admin-booking-card lifecycle-hero mb-4">
        <div class="card-body p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3">
                        <span class="lifecycle-state-icon">
                            <i class="bi {{ $phaseIcon }}"></i>
                        </span>

                        <div>
                            <div class="small text-white-50">
                                Trạng thái vận hành hiện tại
                            </div>
                            <h2 class="fw-bold mb-1">{{ $phaseLabel }}</h2>
                            <div class="text-white-50">
                                {{ $phaseDescription }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="p-3 rounded-4" style="background: rgba(255,255,255,.12);">
                        <div class="small text-white-50">Chính sách no-show</div>
                        <div class="fw-semibold mt-1">{{ $policyText }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($phase === 'waiting_check_in')
        <div class="alert alert-warning rounded-4 border-0 shadow-sm mb-4">
            <i class="bi bi-person-clock fs-4 me-2"></i>
            <strong>Giờ sân đã bắt đầu nhưng khách chưa check-in.</strong>
            Hệ thống không tự nhận khách đã đến. Khách phải bấm nút check-in bên tài khoản của họ.
        </div>
    @elseif($phase === 'overdue_waiting_scheduler')
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i>
            <strong>Đơn đã quá hạn check-in.</strong>
            Scheduler chưa cập nhật trạng thái no-show; hãy kiểm tra tiến trình
            <code>php artisan schedule:work</code>.
        </div>
    @elseif($phase === 'no_show')
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
            <div class="d-flex gap-3">
                <i class="bi bi-person-x-fill fs-3"></i>
                <div>
                    <div class="fw-bold fs-5">
                        Khách không đến sân
                    </div>
                    <div>
                        Hệ thống hủy đơn lúc
                        <strong>{{ $noShowAt?->format('H:i d/m/Y') ?? '-' }}</strong>.
                        Số tiền bị giữ:
                        <strong>{{ number_format($forfeitedAmount, 0, ',', '.') }}đ</strong>.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="summary-tile">
                <div class="text-muted small">Đơn hàng</div>
                <div class="mt-2">
                    <span class="badge bg-{{ $orderStatusColor }}-subtle text-{{ $orderStatusColor }} px-3 py-2">
                        {{ $orderStatusText }}
                    </span>
                </div>
                <div class="small text-muted mt-3">
                    Tạo lúc:
                    {{ !empty($booking->created_at)
                        ? \Carbon\Carbon::parse($booking->created_at)->format('H:i d/m/Y')
                        : '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-tile">
                <div class="text-muted small">Check-in</div>
                <div class="fw-bold mt-2">
                    {{ $checkedInAt?->format('H:i d/m/Y') ?? 'Chưa check-in' }}
                </div>
                <div class="small text-muted mt-3">
                    Hạn:
                    {{ $deadlineAt?->format('H:i d/m/Y') ?? '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-tile">
                <div class="text-muted small">Check-out</div>
                <div class="fw-bold mt-2">
                    {{ $checkedOutAt?->format('H:i d/m/Y') ?? 'Chưa check-out' }}
                </div>
                <div class="small text-muted mt-3">
                    Tự động lúc:
                    {{ $endsAt?->format('H:i d/m/Y') ?? '-' }}
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-tile">
                <div class="text-muted small">Thanh toán</div>
                <div class="mt-2">
                    <span class="badge bg-{{ $paymentStatusColor }}-subtle text-{{ $paymentStatusColor }} px-3 py-2">
                        {{ $paymentStatusText }}
                    </span>
                </div>
                <div class="small text-muted mt-3">
                    Đã trả:
                    {{ number_format($paidAmount, 0, ',', '.') }}đ
                </div>
            </div>
        </div>
    </div>

    <div class="card admin-booking-card mb-4">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>
                        Luồng check-in và check-out
                    </h5>
                    <div class="text-muted small">
                        Admin theo dõi; khách tự check-in; hệ thống tự check-out.
                    </div>
                </div>

                <span class="badge bg-light text-secondary border align-self-start">
                    Tự cập nhật mỗi 15 giây
                </span>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="admin-flow">
                <div class="admin-flow-step {{ $confirmStep }}">
                    <span class="admin-flow-icon">
                        <i class="bi bi-patch-check"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Xác nhận đơn</div>
                        <div class="small text-muted">
                            Thanh toán hợp lệ
                        </div>
                    </div>
                </div>

                <div class="admin-flow-step {{ $checkInStep }}">
                    <span class="admin-flow-icon">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Khách check-in</div>
                        <div class="small text-muted">
                            {{ $checkedInAt
                                ? $checkedInAt->format('H:i d/m/Y')
                                : 'Chờ khách xác nhận có mặt' }}
                        </div>
                    </div>
                </div>

                <div class="admin-flow-step {{ $useStep }}">
                    <span class="admin-flow-icon">
                        <i class="bi bi-dribbble"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Sử dụng sân</div>
                        <div class="small text-muted">
                            {{ $phase === 'checked_in'
                                ? 'Khách đang sử dụng sân'
                                : 'Chưa bắt đầu' }}
                        </div>
                    </div>
                </div>

                <div class="admin-flow-step {{ $checkOutStep }}">
                    <span class="admin-flow-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </span>
                    <div>
                        <div class="fw-bold mt-2">Tự check-out</div>
                        <div class="small text-muted">
                            {{ $checkedOutAt
                                ? $checkedOutAt->format('H:i d/m/Y')
                                : 'Khi hết giờ sân' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-light border rounded-4 mt-4 mb-0">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="text-muted small">Mở check-in</div>
                        <div class="fw-semibold">
                            {{ $opensAt?->format('H:i d/m/Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted small">Giờ bắt đầu</div>
                        <div class="fw-semibold">
                            {{ $startsAt?->format('H:i d/m/Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted small">Hạn check-in</div>
                        <div class="fw-semibold text-danger">
                            {{ $deadlineAt?->format('H:i d/m/Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted small">Kết thúc sân</div>
                        <div class="fw-semibold text-success">
                            {{ $endsAt?->format('H:i d/m/Y') ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card admin-booking-card h-100">
                <div class="card-header bg-white border-0 p-4">
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
                                <th>Ngày đá</th>
                                <th>Khung giờ</th>
                                <th class="text-end pe-4">Giá sân</th>
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

                                    $detailPrice = data_get($detail, 'price')
                                        ?? data_get($detail, 'field_price')
                                        ?? data_get($detail, 'field_price_per_hour')
                                        ?? 0;

                                    $fieldName = data_get($detail, 'field.name')
                                        ?? data_get($detail, 'field.field_name')
                                        ?? data_get($detail, 'field_name')
                                        ?? 'Sân chưa xác định';
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

        <div class="col-xl-4">
            <div class="card admin-booking-card h-100">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        Khách hàng
                    </h5>
                </div>

                <div class="card-body px-4">
                    <div class="admin-info-row">
                        <span class="text-muted">Họ tên</span>
                        <strong class="text-end">{{ $customerName }}</strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Điện thoại</span>
                        <strong class="text-end">{{ $customerPhone }}</strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Email</span>
                        <strong class="text-end text-break">{{ $customerEmail }}</strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Loại thanh toán</span>
                        <strong class="text-end">
                            {{ $paymentType === 'full'
                                ? 'Thanh toán đủ'
                                : 'Đặt cọc' }}
                        </strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Tổng tiền</span>
                        <strong class="text-end">
                            {{ number_format($totalMoney, 0, ',', '.') }}đ
                        </strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Đã đóng</span>
                        <strong class="text-end text-success">
                            {{ number_format($paidAmount, 0, ',', '.') }}đ
                        </strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Còn lại</span>
                        <strong class="text-end text-danger">
                            {{ number_format($remainingAmount, 0, ',', '.') }}đ
                        </strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Tiền bị giữ</span>
                        <strong class="text-end text-danger">
                            {{ number_format($forfeitedAmount, 0, ',', '.') }}đ
                        </strong>
                    </div>

                    <div class="admin-info-row">
                        <span class="text-muted">Cần hoàn</span>
                        <strong class="text-end text-primary">
                            {{ number_format($refundAmount, 0, ',', '.') }}đ
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($bookingServices->isNotEmpty())
        <div class="card admin-booking-card mb-4">
            <div class="card-header bg-white border-0 p-4">
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

    {{-- KHU VỰC XỬ LÝ HOÀN TIỀN & XEM PHẢN HỒI SỰ CỐ CỦA KHÁCH --}}
    @if($status === 'cancelled' && $refundAmount > 0)
        <div class="card admin-booking-card mb-4 {{ ($booking->refund_status ?? '') === 'disputed' ? 'border border-danger' : '' }}">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold text-danger mb-1">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                    Xử lý hoàn tiền & Phản hồi sự cố
                </h5>
                <div class="text-muted small">
                    Tiền bị giữ: <strong>{{ number_format($forfeitedAmount, 0, ',', '.') }}đ</strong>.
                    Số tiền cần hoàn: <strong>{{ number_format($refundAmount, 0, ',', '.') }}đ</strong>.
                    <br>Trạng thái hiện tại: 
                    <strong class="text-uppercase">
                        @php $rStt = $booking->refund_status ?? 'pending'; @endphp
                        @if($rStt === 'disputed')
                            <span class="text-danger">Khách báo cáo sự cố (Chưa nhận được tiền)</span>
                        @elseif($rStt === 'completed')
                            <span class="text-success">Đã gửi Bill / Đã hoàn tiền</span>
                        @elseif($rStt === 'confirmed_by_user')
                            <span class="text-primary">Khách đã xác nhận nhận đủ tiền</span>
                        @else
                            <span class="text-warning">Đang chờ chuyển khoản</span>
                        @endif
                    </strong>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- NẾU KHÁCH CÓ BÁO SỰ CỐ THÌ HIỂN THỊ CẢNH BÁO, NỘI DUNG VÀ ẢNH KHÁCH GỬI --}}
                @if(!empty($booking->user_dispute_reason))
                    <div class="alert alert-danger mb-3 rounded-3">
                        <strong><i class="bi bi-chat-square-exclamation me-1"></i> Nội dung khách phản hồi sự cố:</strong><br>
                        "{{ $booking->user_dispute_reason }}"
                    </div>
                @endif

                @php
                    $disputeImg = $booking->dispute_image ?? null;
                    if(!$disputeImg && !empty($booking->note) && preg_match('/\(Ảnh minh chứng: (.*?)\)/', $booking->note, $matches)) {
                        $disputeImg = $matches[1];
                    }
                @endphp

                @if(!empty($disputeImg))
                    <div class="mb-4 p-3 bg-light border rounded-3 text-start">
                        <span class="small fw-semibold text-danger d-block mb-2">
                            <i class="bi bi-image me-1"></i> Ảnh minh chứng sự cố khách gửi kèm:
                        </span>
                        <a href="{{ asset($disputeImg) }}" target="_blank">
                            <img src="{{ asset($disputeImg) }}" alt="Ảnh sự cố khách gửi" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 250px;" title="Click để xem ảnh lớn">
                        </a>
                    </div>
                @endif

                {{-- LUÔN HIỆN FORM GỬI/GỬI LẠI BILL HOÀN TIỀN CHO ĐẾN KHI KHÁCH XÁC NHẬN NHẬN ĐỦ TIỀN --}}
                @if($rStt !== 'confirmed_by_user')
                    @if($rStt === 'completed' || $rStt === 'disputed')
                        <div class="alert alert-info py-2 px-3 small mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i> Bạn có thể gửi lại hoặc cập nhật chứng từ chuyển khoản mới bên dưới nếu đơn hàng gặp sự cố.
                        </div>
                    @endif

                    <form
                        action="{{ route('admin.bookings.processRefund', $booking->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        data-refund-form
                    >
                        @csrf

                        <div class="row g-3">
                            <div class="col-lg-7">
                                <label class="form-label fw-semibold">
                                    Ảnh chứng từ chuyển khoản hoàn tiền
                                </label>
                                <input
                                    type="file"
                                    name="refund_proof_image"
                                    class="form-control"
                                    accept="image/*"
                                    required
                                >
                            </div>

                            <div class="col-lg-5">
                                <label class="form-label fw-semibold">
                                    Ghi chú
                                </label>
                                <input
                                    type="text"
                                    name="refund_proof_note"
                                    class="form-control"
                                    placeholder="Ví dụ: Đã kiểm tra và chuyển lại tiền"
                                >
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-cloud-arrow-up me-1"></i>
                                    Xác nhận & Gửi Bill hoàn tiền
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-success mb-0 rounded-3">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Khách hàng đã xác nhận nhận đủ tiền. Giao dịch hoàn tất!
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($statusHistories->isNotEmpty())
        <div class="card admin-booking-card">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    Lịch sử trạng thái
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Thời gian</th>
                            <th>Nhóm</th>
                            <th>Thay đổi</th>
                            <th>Nguồn</th>
                            <th class="pe-4">Lý do</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($statusHistories as $history)
                            <tr>
                                <td class="ps-4">
                                    {{ !empty($history->occurred_at)
                                        ? \Carbon\Carbon::parse($history->occurred_at)->format('H:i:s d/m/Y')
                                        : '-' }}
                                </td>
                                <td>{{ $history->category ?? '-' }}</td>
                                <td>
                                    <span class="text-muted">
                                        {{ $history->from_status ?? '-' }}
                                    </span>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    <strong>{{ $history->to_status ?? '-' }}</strong>
                                </td>
                                <td>{{ $history->source ?? '-' }}</td>
                                <td class="pe-4">{{ $history->reason ?? '-' }}</td>
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
        const activePhases = [
            'upcoming',
            'check_in_open',
            'waiting_check_in',
            'overdue_waiting_scheduler',
            'checked_in'
        ];

        if (
            activePhases.includes(@json($phase))
            && !document.querySelector('[data-refund-form]')
        ) {
            window.setTimeout(function () {
                if (!document.hidden) {
                    window.location.reload();
                }
            }, 15000);
        }
    });
</script>
@endpush