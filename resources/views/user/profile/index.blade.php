@extends('layouts.app')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="container py-5">

    <div class="row g-4">

        {{-- CỘT TRÁI --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body text-center p-4">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 82px; height: 82px;">
                        <i class="bi bi-person-circle fs-1"></i>
                    </div>

                    <h5 class="fw-bold mb-1">
                        {{ $user->name ?? 'Khách hàng' }}
                    </h5>

                    <p class="text-muted small mb-0">
                        {{ $user->email ?? 'Chưa có email' }}
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-grid me-2 text-success"></i>
                        Tài khoản của tôi
                    </h6>
                </div>

                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.profile.index') }}"
                           class="btn btn-success rounded-3 text-start py-3">
                            <i class="bi bi-person me-2"></i>
                            Hồ sơ cá nhân
                        </a>

                        <a href="{{ route('user.bookings.index') }}"
                           class="btn btn-light border rounded-3 text-start py-3">
                            <i class="bi bi-clock-history me-2 text-primary"></i>
                            Lịch sử đặt sân
                        </a>

                        @if(Auth::check() && Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="btn btn-light border rounded-3 text-start py-3">
                                <i class="bi bi-speedometer2 me-2 text-warning"></i>
                                Trang quản trị
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn đăng xuất không?')">
                            @csrf

                            <button type="submit"
                                    class="btn btn-outline-danger rounded-3 text-start py-3 w-100">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI --}}
        <div class="col-lg-9">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Hồ sơ cá nhân</h3>
                    <p class="text-muted mb-0">
                        Quản lý thông tin tài khoản và mật khẩu của bạn
                    </p>
                </div>

                <a href="{{ route('home') }}" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-house-door me-1"></i>
                    Trang chủ
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ session('success') }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                    @foreach($errors->all() as $error)
                        <div>
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- THỐNG KÊ --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Tổng đơn</p>
                            <h4 class="fw-bold mb-0">{{ $totalBookings }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Chờ xác nhận</p>
                            <h4 class="fw-bold text-warning mb-0">{{ $pendingBookings }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Đã xác nhận</p>
                            <h4 class="fw-bold text-success mb-0">{{ $confirmedBookings }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Tổng chi tiêu</p>
                            <h5 class="fw-bold text-success mb-0">
                                {{ number_format((float) $totalSpent, 0, ',', '.') }}đ
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM CẬP NHẬT HỒ SƠ --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        Thông tin cá nhân
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('user.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ tên</label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $user->name ?? '') }}"
                                       class="form-control rounded-3 @error('name') is-invalid @enderror"
                                       placeholder="Nhập họ tên">

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email', $user->email ?? '') }}"
                                       class="form-control rounded-3 @error('email') is-invalid @enderror"
                                       placeholder="Nhập email">

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text"
                                    name="phone"
                                    value="{{ old('phone', $user->phone ?? '') }}"
                                    class="form-control rounded-3 @error('phone') is-invalid @enderror"
                                    placeholder="Nhập số điện thoại">

                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vai trò</label>
                                <input type="text"
                                       value="{{ $user->role ?? 'user' }}"
                                       class="form-control rounded-3"
                                       disabled>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary rounded-3 px-4">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Cập nhật hồ sơ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- FORM ĐỔI MẬT KHẨU --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-shield-lock text-danger me-2"></i>
                        Đổi mật khẩu
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('user.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                                <input type="password"
                                       name="current_password"
                                       class="form-control rounded-3 @error('current_password') is-invalid @enderror"
                                       placeholder="Nhập mật khẩu hiện tại">

                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Mật khẩu mới</label>
                                <input type="password"
                                       name="password"
                                       class="form-control rounded-3 @error('password') is-invalid @enderror"
                                       placeholder="Nhập mật khẩu mới">

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control rounded-3"
                                       placeholder="Nhập lại mật khẩu mới">
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-danger rounded-3 px-4">
                                    <i class="bi bi-key me-1"></i>
                                    Đổi mật khẩu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection