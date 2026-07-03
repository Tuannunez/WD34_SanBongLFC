@extends('admin.layouts.app')

@section('title', 'Sửa người dùng')
@section('page-title', 'Sửa thông tin người dùng')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Sửa người dùng</h4>
            <div class="text-muted">Cập nhật thông tin tài khoản người dùng</div>
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
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold mb-3"
                     style="width: 72px; height: 72px; font-size: 30px;">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>

                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>

                <div class="text-muted mb-3">
                    {{ $user->email }}
                </div>

                <div class="mb-3">
                    <span class="badge bg-info-subtle text-info">
                        <i class="bi bi-person-badge me-1"></i>
                        {{ ucfirst($user->role) }}
                    </span>

                    @if($user->status)
                        <span class="badge bg-success-subtle text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Kích hoạt
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger">
                            <i class="bi bi-lock me-1"></i>
                            Đã khóa
                        </span>
                    @endif
                </div>

                <p class="text-muted">
                    Bạn đang chỉnh sửa thông tin tài khoản này. Mật khẩu chỉ thay đổi khi bạn nhập mật khẩu mới.
                </p>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ID</span>
                        <span class="fw-bold">#{{ $user->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Điện thoại</span>
                        <span class="fw-bold">{{ $user->phone ?? 'Chưa có' }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Ngày tạo</span>
                        <span class="fw-bold">{{ $user->created_at?->format('d/m/Y') }}</span>
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
                    <div class="text-muted small">Chỉnh sửa thông tin người dùng và bấm lưu</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tên người dùng <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
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
                                       value="{{ old('email', $user->email) }}"
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
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="Nhập số điện thoại">

                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu mới</label>

                                <input type="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Bỏ trống nếu không đổi">

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    Chỉ nhập khi bạn muốn thay đổi mật khẩu.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Vai trò <span class="text-danger">*</span>
                                </label>

                                <select name="role"
                                        class="form-select @error('role') is-invalid @enderror"
                                        required>
                                    @foreach($roles as $slug => $name)
                                        <option value="{{ $slug }}" {{ old('role', $user->role) === $slug ? 'selected' : '' }}>
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
                                       {{ old('status', $user->status) ? 'checked' : '' }}>

                                <label class="form-check-label fw-semibold" for="status">
                                    Kích hoạt tài khoản
                                </label>
                            </div>

                            <div class="text-muted small mt-1">
                                Nếu tắt trạng thái này, người dùng có thể bị hạn chế đăng nhập hoặc sử dụng hệ thống.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                                Hủy
                            </a>

                            <button class="btn btn-primary px-4">
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