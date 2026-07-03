@extends('admin.layouts.app')

@section('title', 'Tạo người dùng')
@section('page-title', 'Tạo người dùng mới')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Tạo người dùng mới</h4>
            <div class="text-muted">Thêm tài khoản mới vào hệ thống SanBongLFC</div>
        </div>

        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
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
                    <i class="bi bi-person-plus fs-3"></i>
                </div>

                <h5 class="fw-bold mb-2">Thông tin tài khoản</h5>

                <p class="text-muted mb-4">
                    Tài khoản được tạo tại đây có thể đăng nhập và sử dụng hệ thống theo vai trò được phân quyền.
                </p>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Email không được trùng với tài khoản đã có.</span>
                </div>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Mật khẩu nên đủ mạnh để bảo vệ tài khoản.</span>
                </div>

                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Chọn đúng vai trò trước khi tạo người dùng.</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>
                        Nhập thông tin người dùng
                    </h5>
                    <div class="text-muted small">Các trường có dấu * là bắt buộc</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tên người dùng <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Nhập họ và tên"
                                   required>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Email <span class="text-danger">*</span>
                                </label>

                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="Nhập email"
                                       required>

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>

                                <input type="tel"
                                       name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}"
                                       placeholder="Nhập số điện thoại">

                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Mật khẩu <span class="text-danger">*</span>
                                </label>

                                <input type="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Nhập mật khẩu"
                                       required>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Vai trò <span class="text-danger">*</span>
                                </label>

                                <select name="role"
                                        class="form-select @error('role') is-invalid @enderror"
                                        required>
                                    @foreach($roles as $slug => $name)
                                        <option value="{{ $slug }}" {{ old('role') === $slug ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="p-3 rounded-4 bg-light border mt-4 mb-4">
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox"
                                       name="status"
                                       class="form-check-input"
                                       id="status"
                                       value="1"
                                       {{ old('status', true) ? 'checked' : '' }}>

                                <label class="form-check-label fw-semibold" for="status">
                                    Kích hoạt tài khoản
                                </label>
                            </div>

                            <div class="text-muted small mt-1">
                                Tài khoản được kích hoạt sẽ có thể đăng nhập vào hệ thống.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                                Hủy
                            </a>

                            <button class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>
                                Tạo người dùng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection