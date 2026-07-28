@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <span class="badge text-bg-primary rounded-pill mb-2">Dịch vụ</span>
            <h1 class="fw-bold mb-2">{{ $service->name }}</h1>
            <p class="text-muted mb-0">Thông tin chi tiết dịch vụ đi kèm khi đặt sân.</p>
        </div>
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary rounded-3">Quay lại danh sách</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                @if($service->image)
                    <img src="{{ $service->image }}" alt="{{ $service->name }}" class="card-img-top" style="height: 420px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 420px;">
                        <i class="bi bi-bag-check fs-1 text-muted"></i>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <div class="mb-4">
                    <h2 class="fw-semibold">Giá: <span class="text-success">{{ number_format((float) $service->price, 0, ',', '.') }}đ</span></h2>
                    <p class="text-muted mb-0">Đơn vị: {{ $service->unit ?? 'lượt' }}</p>
                </div>

                <div class="mb-4">
                    <h5 class="fw-semibold mb-2">Mô tả</h5>
                    <p class="text-muted mb-0">{{ $service->description ?? 'Chưa có mô tả cho dịch vụ này.' }}</p>
                </div>

                <div class="mb-4">
                    <h5 class="fw-semibold mb-2">Trạng thái</h5>
                    @if($service->status)
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-secondary">Tạm ẩn</span>
                    @endif
                </div>

                <a href="{{ route('stadiums.index') }}" class="btn btn-primary rounded-3">Đặt sân ngay</a>
            </div>
        </div>
    </div>
</div>
@endsection
