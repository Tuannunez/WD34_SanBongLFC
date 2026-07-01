@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết dịch vụ đặt sân</h3>
            <p class="text-muted mb-0">Thông tin dịch vụ đi kèm của đơn đặt sân</p>
        </div>

        <a href="{{ route('admin.booking-services.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width: 56px; height: 56px;">
                    <i class="bi bi-basket fs-4"></i>
                </div>

                <div>
                    <h4 class="fw-bold mb-1">
                        {{ $bookingService->service_name ?? 'Dịch vụ đặt sân' }}
                    </h4>
                    <p class="text-muted mb-0">
                        Mã đơn: {{ $bookingService->booking_code ?? 'Đơn #' . $bookingService->booking_id }}
                    </p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Khách hàng</small>
                        <div class="fw-semibold">
                            {{ $bookingService->customer_name ?? 'Chưa có thông tin' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Số điện thoại</small>
                        <div class="fw-semibold">
                            {{ $bookingService->customer_phone ?? 'Chưa có thông tin' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Email</small>
                        <div class="fw-semibold">
                            {{ $bookingService->customer_email ?? 'Chưa có thông tin' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Dịch vụ</small>
                        <div class="fw-semibold">
                            {{ $bookingService->service_name ?? 'Không có dịch vụ' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Số lượng</small>
                        <div class="fw-bold fs-5">
                            {{ $bookingService->quantity }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Giá</small>
                        <div class="fw-bold">
                            {{ number_format($bookingService->price, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Thành tiền</small>
                        <div class="fw-bold text-success fs-5">
                            {{ number_format($bookingService->total, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('admin.booking-services.edit', $bookingService->id) }}" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-pencil-square me-1"></i> Sửa
                </a>

                <a href="{{ route('admin.booking-services.index') }}" class="btn btn-light border rounded-3 px-4">
                    Danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection