@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Cập nhật dịch vụ</h3>
            <p class="text-muted mb-0">Chỉnh sửa thông tin dịch vụ</p>
        </div>

        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Thông tin dịch vụ
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên dịch vụ <span class="text-danger">*</span></label>
                        <input type="text"
                               name="name"
                               class="form-control rounded-3 @error('name') is-invalid @enderror"
                               value="{{ old('name', $service->name) }}">

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giá dịch vụ <span class="text-danger">*</span></label>
                        <input type="number"
                               name="price"
                               class="form-control rounded-3 @error('price') is-invalid @enderror"
                               value="{{ old('price', $service->price) }}"
                               min="0">

                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Đơn vị</label>
                        <input type="text"
                               name="unit"
                               class="form-control rounded-3 @error('unit') is-invalid @enderror"
                               value="{{ old('unit', $service->unit) }}">

                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status"
                                class="form-select rounded-3 @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', $service->status) == 1 ? 'selected' : '' }}>Hoạt động</option>
                            <option value="0" {{ old('status', $service->status) == 0 ? 'selected' : '' }}>Tạm ẩn</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description"
                                  rows="4"
                                  class="form-control rounded-3 @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-save me-1"></i> Cập nhật
                    </button>

                    <a href="{{ route('admin.services.index') }}" class="btn btn-light border rounded-3 px-4">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection