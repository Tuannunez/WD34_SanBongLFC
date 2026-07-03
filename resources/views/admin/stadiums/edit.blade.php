@extends('admin.layouts.app')

@section('title', 'Sửa cơ sở sân')
@section('page-title', 'Sửa cơ sở sân')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Sửa cơ sở sân bóng</h4>
            <div class="text-muted">Cập nhật thông tin cơ sở sân bóng</div>
        </div>

        <a href="{{ route('admin.stadiums.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm">
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-circle me-1"></i>
                Dữ liệu chưa hợp lệ
            </div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="page-card p-4 h-100">
                <img src="{{ $stadium->image ? (str_starts_with($stadium->image, 'http') ? $stadium->image : asset('storage/' . $stadium->image)) : asset('images/logo.png') }}"
                     alt="{{ $stadium->name }}"
                     class="w-100 rounded-4 border mb-3"
                     style="height: 220px; object-fit: cover;">

                <h5 class="fw-bold mb-1">{{ $stadium->name }}</h5>

                <div class="text-muted mb-3">
                    <i class="bi bi-geo-alt me-1 text-danger"></i>
                    {{ $stadium->address }}
                </div>

                <div class="mb-3">
                    <span class="badge bg-info-subtle text-info">
                        <i class="bi bi-grid-3x3-gap me-1"></i>
                        {{ $stadium->fieldType?->name ?? 'Chưa chọn loại sân' }}
                    </span>
                </div>

                <p class="text-muted">
                    Bạn đang chỉnh sửa cơ sở sân này. Nếu không chọn ảnh mới, hệ thống sẽ giữ nguyên ảnh hiện tại.
                </p>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Số điện thoại</span>
                        <span class="fw-bold">{{ $stadium->phone ?: 'Chưa có' }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Email</span>
                        <span class="fw-bold">{{ $stadium->email ?: 'Chưa có' }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Giờ hoạt động</span>
                        <span class="fw-bold">
                            {{ $stadium->open_time ? \Carbon\Carbon::parse($stadium->open_time)->format('H:i') : '--:--' }}
                            -
                            {{ $stadium->close_time ? \Carbon\Carbon::parse($stadium->close_time)->format('H:i') : '--:--' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>
                        Cập nhật thông tin cơ sở sân
                    </h5>
                    <div class="text-muted small">Chỉnh sửa thông tin và bấm lưu thay đổi</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.stadiums.update', $stadium) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tên cơ sở sân <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $stadium->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ảnh đại diện mới</label>
                                <input type="file"
                                       name="image"
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text"
                                       name="phone"
                                       value="{{ old('phone', $stadium->phone) }}"
                                       class="form-control @error('phone') is-invalid @enderror">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email', $stadium->email) }}"
                                       class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Địa chỉ <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="address"
                                       value="{{ old('address', $stadium->address) }}"
                                       class="form-control @error('address') is-invalid @enderror"
                                       required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Loại sân <span class="text-danger">*</span>
                                </label>
                                <select name="field_type_id"
                                        class="form-select @error('field_type_id') is-invalid @enderror"
                                        required>
                                    <option value="">Chọn loại sân</option>
                                    @foreach($fieldTypes as $fieldType)
                                        <option value="{{ $fieldType->id }}" {{ old('field_type_id', $stadium->field_type_id) == $fieldType->id ? 'selected' : '' }}>
                                            {{ $fieldType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('field_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Giờ mở cửa <span class="text-danger">*</span>
                                </label>
                                <input type="time"
                                       name="open_time"
                                       value="{{ old('open_time', $stadium->open_time) }}"
                                       class="form-control @error('open_time') is-invalid @enderror"
                                       required>
                                @error('open_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Giờ đóng cửa <span class="text-danger">*</span>
                                </label>
                                <input type="time"
                                       name="close_time"
                                       value="{{ old('close_time', $stadium->close_time) }}"
                                       class="form-control @error('close_time') is-invalid @enderror"
                                       required>
                                @error('close_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Mô tả</label>
                                <textarea name="description"
                                          rows="4"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Nhập mô tả về cơ sở sân bóng...">{{ old('description', $stadium->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.stadiums.index') }}" class="btn btn-light">
                                Hủy
                            </a>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i>
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection