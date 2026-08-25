@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Header trạng thái -->
                <div class="bg-success bg-gradient text-white text-center py-4 px-3 position-relative">
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10 bg-pattern"></div>
                    <div class="mb-2 position-relative">
                        <div class="d-inline-flex align-items-center justify-content-center bg-white text-success rounded-circle shadow-sm" style="width: 70px; height: 70px;">
                            <i class="bi bi-check-lg fs-1 fw-bold"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 position-relative">Thanh toán thành công!</h3>
                    <p class="mb-0 text-white-50 small position-relative">Giao dịch của bạn đã được xác nhận an toàn qua VNPay</p>
                </div>

                <!-- Body hóa đơn -->
                <div class="card-body p-4 p-md-5 bg-white">
                    <div class="text-center mb-4">
                        <span class="text-muted small">Cảm ơn bạn đã tin tưởng và đồng hành cùng</span>
                        <h5 class="fw-bold text-success mt-1">SanBongLFC</h5>
                    </div>

                    <!-- Khung thông tin chi tiết biên lai -->
                    <div class="bg-light rounded-4 p-4 border border-1 border-light-subtle mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-2 border-white">
                            <span class="text-muted small"><i class="bi bi-receipt me-1"></i> Mã đơn hàng</span>
                            <span class="fw-bold text-dark font-monospace">{{ $booking->booking_code ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-2 border-white">
                            <span class="text-muted small"><i class="bi bi-calendar-check me-1"></i> Trạng thái sân</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold">Đã xác nhận giữ sân</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="bi bi-cash-stack me-1"></i> Tổng tiền đã trả</span>
                            <span class="fw-extrabold text-danger fs-5">{{ number_format($booking->paid_amount ?? 0, 0, ',', '.') }} <small class="fs-6 fw-normal text-secondary">VNĐ</small></span>
                        </div>
                    </div>

                    <!-- Nút bấm hành động -->
                    <div class="d-grid gap-2.5">
                        <a href="{{ route('user.bookings.index') }}" class="btn btn-success btn-lg fw-bold rounded-3 py-2.5 shadow-sm">
                            <i class="bi bi-card-list me-2"></i> Quản lý đơn đặt của tôi
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-light text-secondary fw-semibold rounded-3 py-2 border">
                            <i class="bi bi-house-door me-1"></i> Quay về trang chủ
                        </a>
                    </div>
                </div>

                <!-- Footer nhỏ -->
                <div class="card-footer bg-light text-center py-3 border-0 text-muted small">
                    <i class="bi bi-shield-check text-success me-1"></i> Hệ thống thanh toán bảo mật tuyệt đối
                </div>
            </div>
        </div>
    </div>
</div>
@endsection