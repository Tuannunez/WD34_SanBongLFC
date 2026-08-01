@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn đặt sân')

@push('styles')
<style>
    .detail-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
    }

    .summary-box {
        min-height: 122px;
        border: 1px solid #e9eef5;
        border-radius: 17px;
        background: #fff;
    }

    .lifecycle-track {
        position: relative;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .lifecycle-track::before {
        content: "";
        position: absolute;
        top: 24px;
        left: 14%;
        right: 14%;
        height: 3px;
        background: #e2e8f0;
        z-index: 0;
    }

    .lifecycle-step {
        position: relative;
        z-index: 1;
        text-align: center;
    }

    .lifecycle-icon {
        width: 50px;
        height: 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        font-size: 20px;
        border: 5px solid #fff;
    }

    .lifecycle-step.done .lifecycle-icon {
        color: #fff;
        background: #16a34a;
    }

    .lifecycle-step.current .lifecycle-icon {
        color: #fff;
        background: #0d6efd;
        box-shadow: 0 0 0 6px rgba(13, 110, 253, .12);
    }

    .lifecycle-step.failed .lifecycle-icon {
        color: #fff;
        background: #dc3545;
        box-shadow: 0 0 0 6px rgba(220, 53, 69, .12);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 11px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .info-row:last-child {
        border-bottom: 0;
    }

    @media (max-width: 767.98px) {
        .lifecycle-track {
            grid-template-columns: 1fr;
        }

        .lifecycle-track::before {
            display: none;
        }

        .lifecycle-step {
            text-align: left;
            display: flex;
            gap: 12px;
            align-items: center;
        }
    }
</style>
@endpush

@section('content')
@php
    $bookingDetails = $bookingDetails
        ?? $booking->bookingDetails
        ?? collect();

    $bookingServices = $bookingServices
        ?? $booking->bookingServices
        ?? collect();

    $payments = $payments
        ?? $booking->payments
        ?? collect();

    $statusHistories = $statusHistories ?? collect();

    $status = strtolower((string) ($booking->status ?? 'pending'));
    $usageStatus = strtolower((string) ($booking->usage_status ?? 'not_checked_in'));
    $paymentStatus = strtolower((string) ($booking->payment_status ?? 'unpaid'));

    $statusMeta = [
        'pending' => ['Chờ thanh toán', 'warning'],
        'confirmed' => ['Đã xác nhận', 'primary'],
        'completed' => ['Hoàn thành', 'success'],
        'cancelled' => ['Đã hủy', 'danger'],
    ];

    $usageMeta = [
        'not_checked_in' => ['Chưa check-in', 'secondary'],
        'checked_in' => ['Đang sử dụng sân', 'info'],
        'checked_out' => ['Đã check-out', 'success'],
    ];

    $paymentMeta = [
        'unpaid' => ['Chưa thanh toán', 'secondary'],
        'deposit_paid' => ['Đã thanh toán cọc', 'warning'],
        'paid' => ['Đã thanh toán', 'success'],
        'partially_refunded' => ['Hoàn một phần', 'info'],
        'refunded' => ['Đã hoàn tiền', 'primary'],
    ];

    [$statusText, $statusColor] = $statusMeta[$status]
        ?? [ucfirst($status), 'secondary'];

    [$usageText, $usageColor] = $usageMeta[$usageStatus]
        ?? [ucfirst($usageStatus), 'secondary'];

    [$paymentText, $paymentColor] = $paymentMeta[$paymentStatus]
        ?? [ucfirst($paymentStatus), 'secondary'];

    $checkedInAt = !empty($booking->checked_in_at)
        ? \Carbon\Carbon::parse($booking->checked_in_at)
        : null;

    $checkedOutAt = !empty($booking->checked_out_at)
        ? \Carbon\Carbon::parse($booking->checked_out_at)
        : null;

    $noShowAt = !empty($booking->no_show_at)
        ? \Carbon\Carbon::parse($booking->no_show_at)
        : null;

    $firstDetail = $bookingDetails->first();
    $lastDetail = $bookingDetails->last();

    $bookingDate = data_get($firstDetail, 'booking_date')
        ?? data_get($firstDetail, 'date')
        ?? data_get($booking, 'booking_date');

    $startTime = data_get($firstDetail, 'slot_start_time')
        ?? data_get($firstDetail, 'start_time')
        ?? data_get($firstDetail, 'timeSlot.start_time');

    $endTime = data_get($lastDetail, 'slot_end_time')
        ?? data_get($lastDetail, 'end_time')
        ?? data_get($lastDetail, 'timeSlot.end_time');

    $totalMoney = $booking->final_amount
        ?? $booking->total_amount
        ?? $booking->total_price
        ?? $booking->total
        ?? 0;

    $depositAmount = (float) ($booking->deposit_amount ?? 0);
    $forfeitedAmount = (float) ($booking->deposit_forfeited_amount ?? 0);
    $refundAmount = (float) ($booking->refund_amount ?? 0);

    $customerName = data_get($booking, 'user.name')
        ?? $booking->customer_name
        ?? $booking->name
        ?? 'Khách hàng';

    $customerEmail = data_get($booking, 'user.email')
        ?? $booking->customer_email
        ?? $booking->email
        ?? '-';

    $customerPhone = $booking->customer_phone
        ?? $booking->phone
        ?? data_get($booking, 'user.phone')
        ?? '-';

    $isNoShow = $noShowAt !== null;
    $isCheckedIn = $usageStatus === 'checked_in';
    $isCheckedOut = $usageStatus === 'checked_out';
@endphp

<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h3 class="fw-bold mb-0">
                    Đơn đặt sân #{{ $booking->id }}
                </h3>

                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }}">
                    {{ $statusText }}
                </span>

                <span class="badge bg-{{ $usageColor }}-subtle text-{{ $usageColor }}">
                    {{ $usageText }}
                </span>
            </div>

            <div class="text-muted mt-1">
                {{ $booking->booking_code ?? $booking->code ?? 'Không có mã đơn' }}
            </div>
        </div>

        <div class="d-flex gap-2">
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

            <a
                href="{{ route('admin.bookings.index') }}"
                class="btn btn-light border"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Quay lại
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @if($isNoShow)
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
            <div class="d-flex gap-3">
                <i class="bi bi-person-x-fill fs-3"></i>
                <div>
                    <div class="fw-bold fs-5">
                        Khách không check-in trong vòng 15 phút
                    </div>
                    <div>
                        Hệ thống đã tự động hủy đơn lúc
                        <strong>{{ $noShowAt->format('H:i d/m/Y') }}</strong>
                        và giữ lại tiền cọc
                        <strong>{{ number_format($forfeitedAmount, 0, ',', '.') }}đ</strong>.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="summary-box p-3 h-100">
                <div class="text-muted small">Trạng thái đơn</div>
                <div class="mt-2">
                    <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} px-3 py-2">
                        {{ $statusText }}
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
            <div class="summary-box p-3 h-100">
                <div class="text-muted small">Check-in / Check-out</div>
                <div class="mt-2">
                    <span class="badge bg-{{ $usageColor }}-subtle text-{{ $usageColor }} px-3 py-2">
                        {{ $usageText }}
                    </span>
                </div>
                <div class="small text-muted mt-3">
                    Vào: {{ $checkedInAt?->format('H:i d/m/Y') ?? 'Chưa có' }}
                </div>
                <div class="small text-muted">
                    Ra: {{ $checkedOutAt?->format('H:i d/m/Y') ?? 'Chưa có' }}
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-box p-3 h-100">
                <div class="text-muted small">Thanh toán</div>
                <div class="mt-2">
                    <span class="badge bg-{{ $paymentColor }}-subtle text-{{ $paymentColor }} px-3 py-2">
                        {{ $paymentText }}
                    </span>
                </div>
                <div class="small text-muted mt-3">
                    Cọc: {{ number_format($depositAmount, 0, ',', '.') }}đ
                </div>
                <div class="small text-muted">
                    Đã trả:
                    {{ number_format((float) ($booking->paid_amount ?? 0), 0, ',', '.') }}đ
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="summary-box p-3 h-100">
                <div class="text-muted small">Tổng tiền</div>
                <div class="fs-3 fw-bold text-success mt-2">
                    {{ number_format((float) $totalMoney, 0, ',', '.') }}đ
                </div>
                <div class="small text-muted mt-3">
                    Hoàn lại: {{ number_format($refundAmount, 0, ',', '.') }}đ
                </div>
            </div>
        </div>
    </div>

    <div class="card detail-card mb-4">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-diagram-3 text-primary me-2"></i>
                        Vòng đời sử dụng sân
                    </h5>
                    <div class="text-muted small">
                        Khách tự check-in; Scheduler tự check-out và xử lý no-show.
                    </div>
                </div>

                <span class="badge bg-light text-secondary border align-self-start">
                    Tự cập nhật mỗi 20 giây
                </span>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="lifecycle-track">
                <div class="lifecycle-step {{ in_array($status, ['confirmed', 'completed'], true) || $isCheckedIn || $isCheckedOut ? 'done' : ($status === 'pending' ? 'current' : '') }}">
                    <span class="lifecycle-icon">
                        <i class="bi bi-patch-check"></i>
                    </span>
                    <div class="fw-bold mt-2">Xác nhận đơn</div>
                    <div class="small text-muted">
                        {{ !empty($booking->confirmed_at)
                            ? \Carbon\Carbon::parse($booking->confirmed_at)->format('H:i d/m/Y')
                            : $statusText }}
                    </div>
                </div>

                <div class="lifecycle-step {{ $isNoShow ? 'failed' : ($isCheckedIn || $isCheckedOut ? 'done' : ($status === 'confirmed' ? 'current' : '')) }}">
                    <span class="lifecycle-icon">
                        <i class="bi {{ $isNoShow ? 'bi-person-x' : 'bi-box-arrow-in-right' }}"></i>
                    </span>
                    <div class="fw-bold mt-2">
                        {{ $isNoShow ? 'Không đến sân' : 'Check-in' }}
                    </div>
                    <div class="small text-muted">
                        @if($isNoShow)
                            {{ $noShowAt->format('H:i d/m/Y') }}
                        @elseif($checkedInAt)
                            {{ $checkedInAt->format('H:i d/m/Y') }}
                        @else
                            Chờ khách check-in
                        @endif
                    </div>
                </div>

                <div class="lifecycle-step {{ $isCheckedOut ? 'done' : ($isCheckedIn ? 'current' : '') }}">
                    <span class="lifecycle-icon">
                        <i class="bi bi-box-arrow-right"></i>
                    </span>
                    <div class="fw-bold mt-2">Check-out</div>
                    <div class="small text-muted">
                        {{ $checkedOutAt
                            ? $checkedOutAt->format('H:i d/m/Y')
                            : ($isCheckedIn ? 'Sẽ tự động khi hết giờ' : 'Chưa bắt đầu') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card detail-card h-100">
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
                                        ?? $bookingDate;

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
                                        Chưa có chi tiết sân.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card detail-card h-100">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        Khách hàng
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="info-row">
                        <span class="text-muted">Họ tên</span>
                        <strong class="text-end">{{ $customerName }}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-muted">Điện thoại</span>
                        <strong class="text-end">{{ $customerPhone }}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-muted">Email</span>
                        <strong class="text-end text-break">{{ $customerEmail }}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-muted">Ngày đặt</span>
                        <strong class="text-end">
                            {{ $bookingDate
                                ? \Carbon\Carbon::parse($bookingDate)->format('d/m/Y')
                                : '-' }}
                        </strong>
                    </div>
                    <div class="info-row">
                        <span class="text-muted">Giờ sân</span>
                        <strong class="text-end">
                            {{ $startTime ?? '-' }} – {{ $endTime ?? '-' }}
                        </strong>
                    </div>
                    <div class="info-row">
                        <span class="text-muted">Ghi chú</span>
                        <strong class="text-end">
                            {{ $booking->note ?? '-' }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($bookingServices->isNotEmpty())
        <div class="card detail-card mb-4">
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

    @if($status === 'cancelled' && $refundAmount > 0)
        <div class="card detail-card mb-4">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-1 text-danger">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                    Xử lý hoàn tiền
                </h5>
                <div class="text-muted small">
                    Tiền cọc bị giữ:
                    <strong>{{ number_format($forfeitedAmount, 0, ',', '.') }}đ</strong>.
                    Số tiền cần hoàn:
                    <strong>{{ number_format($refundAmount, 0, ',', '.') }}đ</strong>.
                </div>
            </div>

            <div class="card-body p-4">
                @if(in_array((string) ($booking->refund_status ?? 'pending'), ['pending', 'disputed'], true))
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
                                    Ảnh chứng từ chuyển khoản
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
                                    placeholder="Ví dụ: Đã chuyển khoản lúc 15:30"
                                >
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-cloud-arrow-up me-1"></i>
                                    Xác nhận đã hoàn tiền
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        Chứng từ hoàn tiền đã được ghi nhận.
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($statusHistories->isNotEmpty())
        <div class="card detail-card">
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
        const hasRefundForm = document.querySelector('[data-refund-form]');

        if (hasRefundForm) {
            return;
        }

        window.setTimeout(function () {
            window.location.reload();
        }, 20000);
    });
</script>
@endpush