@extends('admin.layouts.app')

@section('title', 'Chi tiết cơ sở sân')
@section('page-title', 'Chi tiết cơ sở sân')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Chi tiết cơ sở sân</h4>
            <div class="text-muted">Xem thông tin chi tiết của cơ sở sân bóng</div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.stadiums.edit', $stadium->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>
                Sửa thông tin
            </a>

            <a href="{{ route('admin.stadiums.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Quay lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="page-card p-4">
                <img src="{{ $stadium->image ? (str_starts_with($stadium->image, 'http') ? $stadium->image : asset('storage/' . $stadium->image)) : asset('images/logo.png') }}"
                     class="w-100 rounded-4 border"
                     style="height: 330px; object-fit: cover;"
                     alt="{{ $stadium->name }}">

                <div class="mt-4">
                    <h4 class="fw-bold mb-2">{{ $stadium->name }}</h4>

                    <div class="text-muted">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                        {{ $stadium->address }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-info-circle text-primary me-1"></i>
                        Thông tin cơ sở
                    </h5>
                    <div class="text-muted small">Thông tin liên hệ, loại sân và giờ hoạt động</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border h-100">
                                <div class="text-muted small mb-1">Số điện thoại</div>
                                <div class="fw-bold">
                                    <i class="bi bi-telephone text-success me-1"></i>
                                    {{ $stadium->phone ?: 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border h-100">
                                <div class="text-muted small mb-1">Email</div>
                                <div class="fw-bold">
                                    <i class="bi bi-envelope text-primary me-1"></i>
                                    {{ $stadium->email ?: 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border h-100">
                                <div class="text-muted small mb-1">Loại sân</div>
                                <div class="fw-bold">
                                    <i class="bi bi-grid-3x3-gap text-info me-1"></i>
                                    {{ $stadium->fieldType?->name ?? 'Chưa chọn' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-4 bg-light border h-100">
                                <div class="text-muted small mb-1">Giờ hoạt động</div>
                                <div class="fw-bold">
                                    <i class="bi bi-clock text-success me-1"></i>
                                    {{ $stadium->open_time ? \Carbon\Carbon::parse($stadium->open_time)->format('H:i') : '--:--' }}
                                    -
                                    {{ $stadium->close_time ? \Carbon\Carbon::parse($stadium->close_time)->format('H:i') : '--:--' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-4 bg-light border">
                                <div class="text-muted small mb-2">Mô tả</div>
                                <div class="lh-lg">
                                    {{ $stadium->description ?: 'Chưa có mô tả cho cơ sở sân này.' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.stadiums.index') }}" class="btn btn-light">
                            Quay lại danh sách
                        </a>

                        <a href="{{ route('admin.stadiums.edit', $stadium->id) }}" class="btn btn-primary px-4">
                            <i class="bi bi-pencil-square me-1"></i>
                            Sửa cơ sở sân
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection