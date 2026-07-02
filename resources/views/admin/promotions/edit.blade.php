@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chỉnh sửa khuyến mãi</h3>
            <p class="text-muted mb-0">Cập nhật thông tin mã khuyến mãi</p>
        </div>

        <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Thông tin khuyến mãi
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Mã khuyến mãi <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="code"
                               class="form-control rounded-3 @error('code') is-invalid @enderror"
                               value="{{ old('code', $promotion->code) }}"
                               placeholder="Ví dụ: SUMMER2024, WELCOME10"
                               style="text-transform: uppercase;">

                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Tên khuyến mãi <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name', $promotion->name) }}"
                               placeholder="Ví dụ: Mùa hè khuyến mãi, Giảm 10% cho khách mới">

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Loại giảm giá <span class="text-danger">*</span>
                        </label>

                        <select name="discount_type"
                                class="form-select rounded-3 @error('discount_type') is-invalid @enderror">
                            <option value="">-- Chọn loại giảm giá --</option>
                            <option value="percent" {{ old('discount_type', $promotion->discount_type) == 'percent' ? 'selected' : '' }}>
                                Phần trăm (%)
                            </option>
                            <option value="fixed" {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>
                                Tiền cố định (đ)
                            </option>
                        </select>

                        @error('discount_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Giá trị giảm giá <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="discount_value"
                               class="form-control rounded-3 @error('discount_value') is-invalid @enderror"
                               value="{{ old('discount_value', $promotion->discount_value) }}"
                               min="0"
                               step="0.01"
                               placeholder="Ví dụ: 10 (nếu % ) hoặc 50000 (nếu đ)">

                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số tiền tối thiểu để áp dụng</label>

                        <input type="number"
                               name="min_order_amount"
                               class="form-control rounded-3 @error('min_order_amount') is-invalid @enderror"
                               value="{{ old('min_order_amount', $promotion->min_order_amount) }}"
                               min="0"
                               step="0.01"
                               placeholder="Ví dụ: 100000 (tối thiểu 100k để áp dụng)">

                        @error('min_order_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số lần sử dụng tối đa</label>

                        <input type="number"
                               name="quantity"
                               class="form-control rounded-3 @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', $promotion->quantity) }}"
                               min="1"
                               placeholder="Để trống = không giới hạn">

                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ngày bắt đầu</label>

                        <input type="date"
                               name="start_date"
                               class="form-control rounded-3 @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $promotion->start_date?->format('Y-m-d')) }}">

                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ngày kết thúc</label>

                        <input type="date"
                               name="end_date"
                               class="form-control rounded-3 @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', $promotion->end_date?->format('Y-m-d')) }}">

                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Trạng thái <span class="text-danger">*</span>
                        </label>

                        <select name="status"
                                class="form-select rounded-3 @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', $promotion->status) == 1 ? 'selected' : '' }}>
                                Hoạt động
                            </option>
                            <option value="0" {{ old('status', $promotion->status) == 0 ? 'selected' : '' }}>
                                Tạm ẩn
                            </option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-save me-1"></i> Cập nhật khuyến mãi
                    </button>

                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-light border rounded-3 px-4">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
