@extends('admin.layouts.app')

@section('title', 'Thêm cơ sở sân')
@section('page-title', 'Thêm cơ sở sân')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Thêm cơ sở sân bóng</h4>
            <div class="text-muted">Nhập thông tin cơ sở sân bóng mới vào hệ thống</div>
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
                <div class="rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-3"
                     style="width: 58px; height: 58px;">
                    <i class="bi bi-building-add fs-3"></i>
                </div>

                <h5 class="fw-bold mb-2">Thông tin cơ sở</h5>
                <p class="text-muted mb-4">
                    Cơ sở sân là địa điểm chính để quản lý các sân bóng, lịch đặt và dịch vụ liên quan.
                </p>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Tên cơ sở nên rõ ràng, dễ nhận biết.</span>
                </div>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Địa chỉ cần nhập đầy đủ để người dùng dễ tìm.</span>
                </div>

                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Ảnh đại diện giúp cơ sở sân hiển thị đẹp hơn.</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>
                        Nhập thông tin cơ sở sân
                    </h5>
                    <div class="text-muted small">Các trường có dấu * là bắt buộc</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.stadiums.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tên cơ sở sân <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Ví dụ: Sân bóng LFC Quận 1"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ảnh đại diện</label>
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
                                       value="{{ old('phone') }}"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="Nhập số điện thoại">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Nhập email liên hệ">
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
                                       value="{{ old('address') }}"
                                       class="form-control @error('address') is-invalid @enderror"
                                       placeholder="Nhập địa chỉ cơ sở sân"
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
                                        <option value="{{ $fieldType->id }}" {{ old('field_type_id') == $fieldType->id ? 'selected' : '' }}>
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
                                       value="{{ old('open_time') }}"
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
                                       value="{{ old('close_time') }}"
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
                                          placeholder="Nhập mô tả về cơ sở sân bóng...">{{ old('description') }}</textarea>
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
                                <i class="bi bi-check-circle me-1"></i>
                                Lưu cơ sở sân
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection