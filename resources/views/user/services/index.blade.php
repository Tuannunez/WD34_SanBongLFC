@extends('layouts.app')

@section('title', 'Dịch vụ')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary rounded-pill mb-2">Dịch vụ</span>
            <h1 class="fw-bold mb-2">Các dịch vụ đi kèm đặt sân</h1>
            <p class="text-muted mb-0">Xem chi tiết từng dịch vụ, giá và hình ảnh minh họa để lựa chọn phù hợp.</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary rounded-3">Quay về trang chủ</a>
    </div>

    @if($services->isNotEmpty())
        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        @if($service->image)
                            <img src="{{ $service->image }}" alt="{{ $service->name }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
                                <i class="bi bi-bag-check fs-1 text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h3 class="h5 fw-semibold mb-2">{{ $service->name }}</h3>
                            <p class="mb-2 text-muted">{{ $service->description ?? 'Dịch vụ đi kèm sân bóng.' }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-semibold text-success">{{ number_format((float) $service->price, 0, ',', '.') }}đ</span>
                                <small class="text-muted">/{{ $service->unit ?? 'lượt' }}</small>
                            </div>
                            <div class="text-center text-secondary small">Vui lòng chọn dịch vụ khi đặt sân. Chúng tôi sẽ hỗ trợ bạn chuẩn bị đầy đủ.</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 p-4 bg-light rounded-4 border">
            <h3 class="h5 fw-semibold mb-3">Lời khuyên khi dùng dịch vụ</h3>
            <ul class="list-unstyled mb-4">
                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Chọn dịch vụ phù hợp với số lượng người và thời gian chơi.</li>
                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Liên hệ trước với nhân viên nếu cần thuê trang bị thêm.</li>
                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Đặt dịch vụ cùng lúc khi đặt sân để được phục vụ nhanh chóng.</li>
            </ul>
            <div class="text-muted">Cảm ơn bạn đã chọn SanBongLFC. Chúng tôi luôn sẵn sàng hỗ trợ bạn có trải nghiệm đặt sân tốt nhất.</div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
            <p class="mb-0">Chưa có dịch vụ nào được cung cấp.</p>
        </div>
    @endif
</div>
@endsection
