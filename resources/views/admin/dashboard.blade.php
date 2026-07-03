@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted mb-2">Tổng sân bóng</div>
                        <h3 class="fw-bold mb-0">{{ $totalFields ?? 0 }}</h3>
                    </div>

                    <div class="stat-icon">
                        <i class="bi bi-map"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted mb-2">Đơn đặt hôm nay</div>
                        <h3 class="fw-bold mb-0">{{ $todayBookings ?? 0 }}</h3>
                    </div>

                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted mb-2">Doanh thu tháng</div>
                        <h3 class="fw-bold mb-0 text-success">
                            {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}đ
                        </h3>
                    </div>

                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card border-0 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted mb-2">Khách hàng</div>
                        <h3 class="fw-bold mb-0">{{ $totalCustomers ?? 0 }}</h3>
                    </div>

                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="page-card h-100">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1">
                                <i class="bi bi-calendar2-check text-primary me-1"></i>
                                Đơn đặt sân mới nhất
                            </h5>
                            <div class="text-muted small">Các đơn đặt sân được tạo gần đây</div>
                        </div>

                        <a href="{{ url('/admin/bookings') }}" class="btn btn-sm btn-outline-primary">
                            Xem tất cả
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Sân</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Tổng tiền</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($latestBookings as $booking)
                                @php
                                    $status = $booking->status ?? 'pending';

                                    $statusText = match($status) {
                                        'confirmed', 'approved', 'success' => 'Đã xác nhận',
                                        'completed', 'done' => 'Hoàn thành',
                                        'cancelled', 'canceled' => 'Đã hủy',
                                        'paid' => 'Đã thanh toán',
                                        default => 'Chờ xử lý',
                                    };

                                    $statusClass = match($status) {
                                        'confirmed', 'approved', 'success' => 'bg-primary-subtle text-primary',
                                        'completed', 'done', 'paid' => 'bg-success-subtle text-success',
                                        'cancelled', 'canceled' => 'bg-danger-subtle text-danger',
                                        default => 'bg-warning-subtle text-warning',
                                    };
                                @endphp

                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            #{{ $booking->id }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $booking->user_name ?? 'Khách hàng' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $booking->user_email ?? 'Chưa có email' }}
                                        </small>
                                    </td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $booking->field_name ?? 'Chưa có sân' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="text-muted">
                                            @if(!empty($booking->booking_date))
                                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                                            @else
                                                Chưa cập nhật
                                            @endif
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>

                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($booking->display_total ?? 0, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Chưa có dữ liệu đặt sân.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="page-card h-100">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-activity text-primary me-1"></i>
                        Trạng thái hệ thống
                    </h5>
                    <div class="text-muted small">Tổng quan dữ liệu đang có</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">Cơ sở sân bóng</div>
                            <small class="text-muted">Quản lý địa điểm sân</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">
                            {{ $totalStadiums ?? 0 }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">Loại sân</div>
                            <small class="text-muted">Sân 5, sân 7, sân 11...</small>
                        </div>
                        <span class="badge bg-success rounded-pill">
                            {{ $totalFieldTypes ?? 0 }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">Khung giờ</div>
                            <small class="text-muted">Các ca đặt sân</small>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill">
                            {{ $totalTimeSlots ?? 0 }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-semibold">Dịch vụ</div>
                            <small class="text-muted">Nước uống, thuê bóng...</small>
                        </div>
                        <span class="badge bg-info rounded-pill">
                            {{ $totalServices ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection