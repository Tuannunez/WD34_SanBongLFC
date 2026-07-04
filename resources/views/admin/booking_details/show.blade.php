@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết đặt sân #{{ $bookingDetail->id }}</h3>
            <p class="text-muted mb-0">Thông tin chi tiết sân, khách hàng và khung giờ đặt</p>
        </div>

        <a href="{{ route('admin.booking-details.index') }}" class="btn btn-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

    @php
        $detailPrice = $bookingDetail->price
            ?? $bookingDetail->field_price
            ?? $bookingDetail->total_price
            ?? $bookingDetail->total
            ?? $bookingDetail->amount
            ?? $bookingDetail->field_price_per_hour
            ?? 0;

        $bookingDate = $bookingDetail->booking_date
            ?? $bookingDetail->date
            ?? null;

        $startTime = $bookingDetail->slot_start_time
            ?? $bookingDetail->start_time
            ?? null;

        $endTime = $bookingDetail->slot_end_time
            ?? $bookingDetail->end_time
            ?? null;

        $status = $bookingDetail->booking_status
            ?? $bookingDetail->status
            ?? 'pending';

        $statusText = match ($status) {
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => 'Chờ xác nhận',
        };

        $statusClass = match ($status) {
            'confirmed' => 'bg-success-subtle text-success',
            'completed' => 'bg-primary-subtle text-primary',
            'cancelled' => 'bg-danger-subtle text-danger',
            default => 'bg-warning-subtle text-warning',
        };
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Mã chi tiết</p>
                    <h4 class="fw-bold mb-0">#{{ $bookingDetail->id }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Mã đơn</p>

                    @if(!empty($bookingDetail->booking_id))
                        <a href="{{ route('admin.bookings.show', $bookingDetail->booking_id) }}"
                           class="badge bg-primary-subtle text-primary px-3 py-2 text-decoration-none">
                            Đơn #{{ $bookingDetail->booking_id }}
                        </a>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                            Không có đơn
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Trạng thái</p>
                    <span class="badge {{ $statusClass }} px-3 py-2">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-clipboard-data text-primary me-2"></i>
                Thông tin chi tiết đặt sân
            </h5>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted mb-1">Khách hàng</label>
                        <div class="fw-semibold">
                            {{ $bookingDetail->user_name ?? $bookingDetail->customer_name ?? 'Chưa có thông tin' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Email</label>
                        <div class="fw-semibold">
                            {{ $bookingDetail->user_email ?? $bookingDetail->customer_email ?? '-' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Sân</label>
                        <div class="d-flex align-items-center">
                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-dribbble"></i>
                            </div>

                            <div>
                                <div class="fw-semibold">
                                    {{ $bookingDetail->field_name ?? 'Không có sân' }}
                                </div>
                                <small class="text-muted">Sân đặt</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Giá</label>
                        <div class="fw-bold text-success">
                            {{ number_format((float) $detailPrice, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="text-muted mb-1">Ngày đặt</label>
                        <div class="fw-semibold">
                            @if($bookingDate)
                                {{ \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Khung giờ</label>
                        <div>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                @if($startTime || $endTime)
                                    {{ $startTime ?? '' }} - {{ $endTime ?? '' }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Ngày tạo</label>
                        <div class="fw-semibold">
                            {{ !empty($bookingDetail->created_at) ? \Carbon\Carbon::parse($bookingDetail->created_at)->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>

                    <p>
                        <strong>Kiểu thanh toán:</strong> 
                        <span class="badge bg-info text-dark">
                            {{ $booking->method_name ?? 'Chưa xác định' }}
                        </span>
                    </p>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Cập nhật lần cuối</label>
                        <div class="fw-semibold">
                            {{ !empty($bookingDetail->updated_at) ? \Carbon\Carbon::parse($bookingDetail->updated_at)->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end gap-2">
                @if(!empty($bookingDetail->booking_id))
                    <a href="{{ route('admin.bookings.show', $bookingDetail->booking_id) }}"
                       class="btn btn-primary rounded-3">
                        <i class="bi bi-eye me-1"></i>
                        Xem đơn đặt sân
                    </a>
                @endif

                <a href="{{ route('admin.booking-details.index') }}"
                   class="btn btn-secondary rounded-3">
                    Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection