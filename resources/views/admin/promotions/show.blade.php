@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết khuyến mãi</h3>
            <p class="text-muted mb-0">Xem chi tiết thông tin khuyến mãi</p>
        </div>

        <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Thông tin khuyến mãi
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Mã khuyến mãi</h6>
                            <p class="fw-bold font-monospace fs-5">{{ $promotion->code }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Tên khuyến mãi</h6>
                            <p class="fw-semibold">{{ $promotion->name }}</p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Loại giảm giá</h6>
                            <div>
                                @if($promotion->discount_type == 'percent')
                                    <span class="badge bg-info-subtle text-info px-3 py-2">
                                        <i class="bi bi-percent me-1"></i> Phần trăm (%)
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                        <i class="bi bi-cash-coin me-1"></i> Tiền cố định (đ)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Giá trị giảm giá</h6>
                            <p class="fw-bold text-success fs-5">
                                @if($promotion->discount_type == 'percent')
                                    {{ $promotion->discount_value }}%
                                @else
                                    {{ number_format($promotion->discount_value, 0, ',', '.') }}đ
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Số tiền tối thiểu</h6>
                            <p class="fw-semibold">{{ number_format($promotion->min_order_amount, 0, ',', '.') }}đ</p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Số lần sử dụng</h6>
                            <p class="fw-semibold">
                                {{ $promotion->quantity ?? 'Không giới hạn' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Ngày bắt đầu</h6>
                            <p class="fw-semibold">
                                {{ $promotion->start_date ? $promotion->start_date->format('d/m/Y') : '---' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Ngày kết thúc</h6>
                            <p class="fw-semibold">
                                {{ $promotion->end_date ? $promotion->end_date->format('d/m/Y') : '---' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Trạng thái</h6>
                            <div>
                                @if($promotion->status == 1)
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i> Hoạt động
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i> Tạm ẩn
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-muted fw-semibold mb-2">Thời gian tạo</h6>
                            <p class="fw-semibold">{{ $promotion->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="col-md-12">
                            <h6 class="text-muted fw-semibold mb-2">Thời gian cập nhật</h6>
                            <p class="fw-semibold">{{ $promotion->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-sliders text-primary me-2"></i>
                        Thao tác
                    </h5>
                </div>

                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                           class="btn btn-primary rounded-3">
                            <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa
                        </a>

                        <form action="{{ route('admin.promotions.destroy', $promotion->id) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger rounded-3 w-100">
                                <i class="bi bi-trash me-1"></i> Xóa khuyến mãi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mt-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-chart-pie text-primary me-2"></i>
                        Thống kê
                    </h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Số đơn đặt sân sử dụng</small>
                            <small class="fw-semibold">{{ $promotion->bookings()->count() }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Tổng tiền giảm</small>
                            <small class="fw-semibold text-success">
                                {{ number_format($promotion->bookings()->sum('discount_amount'), 0, ',', '.') }}đ
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
