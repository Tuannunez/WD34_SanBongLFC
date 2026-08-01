@extends('admin.layouts.app')

@section('title', 'Quản lý đơn đặt sân')

@push('styles')
<style>
    .booking-stat {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
    }

    .booking-stat-icon {
        width: 46px;
        height: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        font-size: 20px;
    }

    .booking-table-card {
        border: 0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .06);
    }

    .booking-code {
        font-size: 13px;
        color: #64748b;
    }

    .usage-dot {
        width: 8px;
        height: 8px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 6px;
    }

    .schedule-line {
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
@php
    $orderStatusMeta = [
        'pending' => ['Chờ thanh toán', 'warning'],
        'confirmed' => ['Đã xác nhận', 'primary'],
        'completed' => ['Hoàn thành', 'success'],
        'cancelled' => ['Đã hủy', 'danger'],
    ];

    $usageStatusMeta = [
        'not_checked_in' => ['Chưa check-in', 'secondary'],
        'checked_in' => ['Đang sử dụng sân', 'info'],
        'checked_out' => ['Đã check-out', 'success'],
    ];

    $paymentStatusMeta = [
        'unpaid' => ['Chưa thanh toán', 'secondary'],
        'deposit_paid' => ['Đã thanh toán cọc', 'warning'],
        'paid' => ['Đã thanh toán', 'success'],
        'partially_refunded' => ['Hoàn một phần', 'info'],
        'refunded' => ['Đã hoàn tiền', 'primary'],
    ];
@endphp

<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Quản lý đơn đặt sân</h3>
            <p class="text-muted mb-0">
                Theo dõi thanh toán, check-in, check-out và khách vắng mặt.
            </p>
        </div>

        <div class="text-end">
            <span class="badge bg-light text-secondary border px-3 py-2">
                <i class="bi bi-arrow-repeat me-1"></i>
                Tự làm mới mỗi 30 giây
            </span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-2">
            <div class="card booking-stat h-100">
                <div class="card-body">
                    <span class="booking-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-calendar2-check"></i>
                    </span>
                    <div class="fs-3 fw-bold mt-3">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-muted small">Tổng đơn</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card booking-stat h-100">
                <div class="card-body">
                    <span class="booking-stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </span>
                    <div class="fs-3 fw-bold mt-3">{{ $stats['pending'] ?? 0 }}</div>
                    <div class="text-muted small">Chờ thanh toán</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card booking-stat h-100">
                <div class="card-body">
                    <span class="booking-stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-patch-check"></i>
                    </span>
                    <div class="fs-3 fw-bold mt-3">{{ $stats['confirmed'] ?? 0 }}</div>
                    <div class="text-muted small">Đã xác nhận</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card booking-stat h-100">
                <div class="card-body">
                    <span class="booking-stat-icon bg-info-subtle text-info">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </span>
                    <div class="fs-3 fw-bold mt-3">{{ $stats['checked_in'] ?? 0 }}</div>
                    <div class="text-muted small">Đang sử dụng</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card booking-stat h-100">
                <div class="card-body">
                    <span class="booking-stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check2-circle"></i>
                    </span>
                    <div class="fs-3 fw-bold mt-3">{{ $stats['completed'] ?? 0 }}</div>
                    <div class="text-muted small">Hoàn thành</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card booking-stat h-100">
                <div class="card-body">
                    <span class="booking-stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-person-x"></i>
                    </span>
                    <div class="fs-3 fw-bold mt-3">{{ $stats['no_show'] ?? 0 }}</div>
                    <div class="text-muted small">Không đến sân</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card booking-table-card">
        <div class="card-header bg-white border-0 p-4">
            <form method="GET" action="{{ route('admin.bookings.index') }}">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label small fw-semibold text-muted">
                            Tìm đơn
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                type="text"
                                name="keyword"
                                class="form-control"
                                value="{{ request('keyword') }}"
                                placeholder="Mã đơn, khách hàng, điện thoại..."
                            >
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="form-label small fw-semibold text-muted">
                            Trạng thái đơn
                        </label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="pending" @selected(request('status') === 'pending')>
                                Chờ thanh toán
                            </option>
                            <option value="confirmed" @selected(request('status') === 'confirmed')>
                                Đã xác nhận
                            </option>
                            <option value="completed" @selected(request('status') === 'completed')>
                                Hoàn thành
                            </option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>
                                Đã hủy
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="form-label small fw-semibold text-muted">
                            Sử dụng sân
                        </label>
                        <select name="usage_status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="not_checked_in" @selected(request('usage_status') === 'not_checked_in')>
                                Chưa check-in
                            </option>
                            <option value="checked_in" @selected(request('usage_status') === 'checked_in')>
                                Đã check-in
                            </option>
                            <option value="checked_out" @selected(request('usage_status') === 'checked_out')>
                                Đã check-out
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="form-label small fw-semibold text-muted">
                            Thanh toán
                        </label>
                        <select name="payment_status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="unpaid" @selected(request('payment_status') === 'unpaid')>
                                Chưa thanh toán
                            </option>
                            <option value="deposit_paid" @selected(request('payment_status') === 'deposit_paid')>
                                Đã cọc
                            </option>
                            <option value="paid" @selected(request('payment_status') === 'paid')>
                                Đã thanh toán
                            </option>
                            <option value="refunded" @selected(request('payment_status') === 'refunded')>
                                Đã hoàn tiền
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            Lọc
                        </button>
                        <a
                            href="{{ route('admin.bookings.index') }}"
                            class="btn btn-light border"
                            title="Xóa bộ lọc"
                        >
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Đơn đặt sân</th>
                        <th>Khách hàng</th>
                        <th>Lịch sân</th>
                        <th class="text-end">Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái đơn</th>
                        <th>Check-in / Check-out</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $orderStatus = strtolower((string) ($booking->status ?? 'pending'));
                            [$orderText, $orderColor] = $orderStatusMeta[$orderStatus]
                                ?? [ucfirst($orderStatus), 'secondary'];

                            $usageStatus = strtolower((string) ($booking->usage_status ?? 'not_checked_in'));
                            [$usageText, $usageColor] = $usageStatusMeta[$usageStatus]
                                ?? [ucfirst($usageStatus), 'secondary'];

                            $paymentStatus = strtolower((string) ($booking->payment_status ?? 'unpaid'));
                            [$paymentText, $paymentColor] = $paymentStatusMeta[$paymentStatus]
                                ?? [ucfirst($paymentStatus), 'secondary'];

                            $details = $booking->bookingDetails ?? collect();
                            $firstDetail = $details->first();

                            $date = data_get($firstDetail, 'booking_date')
                                ?? data_get($firstDetail, 'date')
                                ?? data_get($booking, 'booking_date');

                            $start = data_get($firstDetail, 'slot_start_time')
                                ?? data_get($firstDetail, 'start_time')
                                ?? data_get($firstDetail, 'timeSlot.start_time');

                            $end = data_get($details->last(), 'slot_end_time')
                                ?? data_get($details->last(), 'end_time')
                                ?? data_get($details->last(), 'timeSlot.end_time');

                            $customerName = data_get($booking, 'user.name')
                                ?? $booking->customer_name
                                ?? $booking->name
                                ?? 'Khách hàng';

                            $customerPhone = $booking->customer_phone
                                ?? $booking->phone
                                ?? data_get($booking, 'user.phone')
                                ?? '-';

                            $totalMoney = $booking->final_amount
                                ?? $booking->total_amount
                                ?? $booking->total_price
                                ?? $booking->total
                                ?? 0;

                            $isNoShow = !empty($booking->no_show_at);
                        @endphp

                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">
                                    #{{ $booking->id }}
                                </div>
                                <div class="booking-code">
                                    {{ $booking->booking_code ?? $booking->code ?? 'Không có mã đơn' }}
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ !empty($booking->created_at)
                                        ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i')
                                        : '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">{{ $customerName }}</div>
                                <div class="small text-muted">{{ $customerPhone }}</div>
                            </td>

                            <td>
                                @if($date)
                                    <div class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                    </div>
                                @else
                                    <div class="text-muted">Chưa có ngày</div>
                                @endif

                                <div class="small text-muted schedule-line">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $start ?? '-' }} – {{ $end ?? '-' }}
                                </div>
                            </td>

                            <td class="text-end fw-bold text-success">
                                {{ number_format((float) $totalMoney, 0, ',', '.') }}đ
                            </td>

                            <td>
                                <span class="badge bg-{{ $paymentColor }}-subtle text-{{ $paymentColor }}">
                                    {{ $paymentText }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-{{ $orderColor }}-subtle text-{{ $orderColor }}">
                                    {{ $orderText }}
                                </span>

                                @if($isNoShow)
                                    <div class="small text-danger mt-1">
                                        <i class="bi bi-person-x me-1"></i>
                                        No-show
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-{{ $usageColor }}-subtle text-{{ $usageColor }}">
                                    <span class="usage-dot bg-{{ $usageColor }}"></span>
                                    {{ $usageText }}
                                </span>

                                @if(!empty($booking->checked_in_at))
                                    <div class="small text-muted mt-1">
                                        Vào:
                                        {{ \Carbon\Carbon::parse($booking->checked_in_at)->format('H:i d/m') }}
                                    </div>
                                @endif

                                @if(!empty($booking->checked_out_at))
                                    <div class="small text-muted">
                                        Ra:
                                        {{ \Carbon\Carbon::parse($booking->checked_out_at)->format('H:i d/m') }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <a
                                    href="{{ route('admin.bookings.show', $booking->id) }}"
                                    class="btn btn-sm btn-primary"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                <span class="text-muted">
                                    Không tìm thấy đơn đặt sân phù hợp.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="card-footer bg-white border-0 p-4">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        window.setTimeout(function () {
            window.location.reload();
        }, 30000);
    });
</script>
@endpush