@extends('admin.layouts.app')

@section('title', 'Tạo vai trò')
@section('page-title', 'Tạo vai trò mới')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Tạo vai trò mới</h4>
            <div class="text-muted">Thêm vai trò dùng để phân quyền người dùng trong hệ thống</div>
        </div>

        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm">
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-circle me-1"></i>
                Dữ liệu chưa hợp lệ
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
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
                    <i class="bi bi-shield-plus fs-3"></i>
                </div>

                <h5 class="fw-bold mb-2">Thông tin vai trò</h5>
                <p class="text-muted mb-4">
                    Vai trò dùng để xác định quyền hạn của từng nhóm tài khoản trong hệ thống.
                </p>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Tên vai trò nên ngắn gọn, dễ hiểu.</span>
                </div>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Slug nên viết thường, không dấu.</span>
                </div>

                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Có thể tắt trạng thái nếu chưa muốn sử dụng.</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>
                        Nhập thông tin vai trò
                    </h5>
                    <div class="text-muted small">Vui lòng nhập đầy đủ các thông tin bắt buộc</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Tên vai trò <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Ví dụ: Quản trị viên, Nhân viên, Khách hàng..."
                                   required>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label fw-semibold">
                                Slug <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="slug"
                                   name="slug"
                                   value="{{ old('slug') }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="Ví dụ: admin, staff, customer..."
                                   required>

                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                Slug dùng để định danh vai trò trong hệ thống.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Mô tả</label>

                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Nhập mô tả ngắn về vai trò này...">{{ old('description') }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="p-3 rounded-4 bg-light border mb-4">
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox"
                                       name="status"
                                       value="1"
                                       class="form-check-input"
                                       id="status"
                                       {{ old('status', 1) ? 'checked' : '' }}>

                                <label for="status" class="form-check-label fw-semibold">
                                    Kích hoạt vai trò
                                </label>
                            </div>
                            <div class="text-muted small mt-1">
                                Vai trò được kích hoạt sẽ có thể sử dụng trong hệ thống.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
                                Hủy
                            </a>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>
                                Tạo vai trò
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection