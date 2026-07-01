@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết dịch vụ</h3>
            <p class="text-muted mb-0">Thông tin chi tiết dịch vụ đi kèm</p>
        </div>

        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width: 56px; height: 56px;">
                    <i class="bi bi-bag-check fs-4"></i>
                </div>

                <div>
                    <h4 class="fw-bold mb-1">{{ $service->name }}</h4>
                    <p class="text-muted mb-0">Mã dịch vụ: #{{ $service->id }}</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Giá dịch vụ</small>
                        <div class="fw-bold text-success fs-5">
                            {{ number_format($service->price, 0, ',', '.') }}đ
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Đơn vị</small>
                        <div class="fw-semibold">
                            {{ $service->unit ?? '---' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Trạng thái</small>
                        <div>
                            @if($service->status == 1)
                                <span class="badge bg-success-subtle text-success px-3 py-2 mt-1">Hoạt động</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-3 py-2 mt-1">Tạm ẩn</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="border rounded-4 p-3 h-100">
                        <small class="text-muted">Ngày tạo</small>
                        <div class="fw-semibold">
                            {{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : '---' }}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="border rounded-4 p-3">
                        <small class="text-muted">Mô tả</small>
                        <div class="mt-1">
                            {{ $service->description ?? 'Chưa có mô tả.' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-pencil-square me-1"></i> Sửa
                </a>

                <a href="{{ route('admin.services.index') }}" class="btn btn-light border rounded-3 px-4">
                    Danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection