@extends('admin.layouts.app')

@section('title', 'Sửa vai trò')
@section('page-title', 'Sửa vai trò')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Sửa vai trò</h4>
            <div class="text-muted">Cập nhật thông tin vai trò trong hệ thống</div>
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
                <div class="rounded-4 bg-warning-subtle text-warning d-flex align-items-center justify-content-center mb-3"
                     style="width: 58px; height: 58px;">
                    <i class="bi bi-shield-check fs-3"></i>
                </div>

                <h5 class="fw-bold mb-2">{{ $role->name }}</h5>

                <div class="mb-3">
                    <span class="badge bg-light text-dark border">
                        {{ $role->slug }}
                    </span>
                </div>

                <p class="text-muted">
                    Bạn đang chỉnh sửa vai trò này. Hãy kiểm tra kỹ trước khi lưu thay đổi.
                </p>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ID</span>
                        <span class="fw-bold">#{{ $role->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Trạng thái</span>

                        @if($role->status)
                            <span class="badge bg-success-subtle text-success">Hoạt động</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">Không hoạt động</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>
                        Cập nhật thông tin
                    </h5>
                    <div class="text-muted small">Thay đổi thông tin vai trò và bấm lưu</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Tên vai trò <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $role->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Nhập tên vai trò"
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
                                   value="{{ old('slug', $role->slug) }}"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   placeholder="Nhập slug vai trò"
                                   required>

                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                Không nên đổi slug nếu vai trò này đang được sử dụng trong hệ thống.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Mô tả</label>

                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Nhập mô tả vai trò...">{{ old('description', $role->description) }}</textarea>

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
                                       {{ old('status', $role->status) ? 'checked' : '' }}>

                                <label for="status" class="form-check-label fw-semibold">
                                    Kích hoạt vai trò
                                </label>
                            </div>
                            <div class="text-muted small mt-1">
                                Tắt trạng thái nếu bạn không muốn vai trò này được sử dụng.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-light">
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