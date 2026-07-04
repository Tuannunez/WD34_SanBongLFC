<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SanBongLFC')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: Arial, sans-serif;
            color: #111827;
        }

        .navbar-user {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 12px rgba(15, 23, 42, .06);
        }

        .navbar-brand img {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .navbar-brand span {
            color: #16a34a;
            font-weight: 700;
        }

        .hero-section {
            position: relative;
            min-height: 520px;
            background:
                linear-gradient(135deg, rgba(21, 128, 61, .92), rgba(22, 101, 52, .82)),
                url('{{ asset('images/banner1.png') }}') center/cover no-repeat;
            color: #ffffff;
            overflow: hidden;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            top: -100px;
            right: -80px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 90px 0 70px;
        }

        .hero-title {
            font-size: 48px;
            line-height: 1.1;
            font-weight: 800;
        }

        .hero-title span {
            color: #bbf7d0;
        }

        .hero-search-card {
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 24px;
            backdrop-filter: blur(12px);
            padding: 22px;
        }

        .hero-stat-card {
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 20px;
            padding: 22px;
            text-align: center;
        }

        .hero-stat-card h3 {
            font-weight: 800;
            margin-bottom: 4px;
        }

        .section-title {
            font-weight: 800;
            color: #111827;
        }

        .stadium-card {
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
            transition: .25s;
            height: 100%;
        }

        .stadium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 42px rgba(15, 23, 42, .12);
        }

        .stadium-img {
            height: 220px;
            object-fit: cover;
        }

        .price-text {
            color: #16a34a;
            font-weight: 800;
        }

        .footer-main {
            background: #0f172a;
            color: #cbd5e1;
        }

        .footer-main h5 {
            color: #ffffff;
            font-weight: 700;
        }

        .footer-main a {
            color: #cbd5e1;
            text-decoration: none;
        }

        .footer-main a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            background: #020617;
            color: #94a3b8;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }

            .hero-content {
                padding: 55px 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-user sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="SanBongLFC">
            <span>SanBongLFC</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="userNavbar">
            <form action="{{ route('home') }}" method="GET" class="d-flex mx-lg-auto my-3 my-lg-0" style="max-width: 420px; width: 100%;">
                <input type="text"
                       name="keyword"
                       value="{{ request('keyword') }}"
                       class="form-control rounded-3"
                       placeholder="Tìm kiếm sân theo tên, địa điểm...">
            </form>

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                @auth
                    <li class="nav-item">
                        <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-success rounded-3">
                            <i class="bi bi-calendar-check me-1"></i>
                            Đơn của tôi
                        </a>
                    </li>

                    @if(Auth::user()->role === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary rounded-3">
                                <i class="bi bi-speedometer2 me-1"></i>
                                Quản trị
                            </a>
                        </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a class="btn btn-light border rounded-3 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('user.bookings.index') }}" class="dropdown-item">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    Đơn đặt sân
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Đăng nhập
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('register') }}" class="btn btn-success rounded-3">
                            <i class="bi bi-person-plus me-1"></i>
                            Đăng ký
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

@if(request()->routeIs('home'))
    <section class="hero-section">
        <div class="container hero-content">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h1 class="hero-title mb-3">
                        Đặt sân bóng <span>nhanh chóng</span> & dễ dàng
                    </h1>

                    <p class="lead mb-4">
                        Tìm sân, chọn giờ và gửi đơn đặt sân chỉ trong vài phút.
                        Trải nghiệm đặt sân tiện lợi cùng SanBongLFC.
                    </p>

                    <div class="hero-search-card">
                        <form action="{{ route('home') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Tỉnh/Thành phố</label>
                                    <select name="city" class="form-select rounded-3">
                                        <option value="">Tất cả thành phố</option>
                                        <option value="ha-noi" @selected(request('city') === 'ha-noi')>Hà Nội</option>
                                        <option value="tp-hcm" @selected(request('city') === 'tp-hcm')>TP.HCM</option>
                                        <option value="da-nang" @selected(request('city') === 'da-nang')>Đà Nẵng</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Loại sân</label>
                                    <select name="field_type" class="form-select rounded-3">
                                        <option value="">Tất cả loại sân</option>
                                        <option value="5" @selected(request('field_type') === '5')>Sân 5</option>
                                        <option value="7" @selected(request('field_type') === '7')>Sân 7</option>
                                        <option value="11" @selected(request('field_type') === '11')>Sân 11</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Ngày đặt</label>
                                    <input type="date"
                                           name="booking_date"
                                           value="{{ request('booking_date') }}"
                                           class="form-control rounded-3">
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-success rounded-3 w-100 py-2 fw-bold">
                                        <i class="bi bi-search me-1"></i>
                                        Tìm sân ngay
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="hero-stat-card">
                                <h3>500+</h3>
                                <div>Sân bóng</div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="hero-stat-card">
                                <h3>10K+</h3>
                                <div>Lượt đặt</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="hero-stat-card">
                                <h3>63</h3>
                                <div>Tỉnh thành hỗ trợ</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<main>
    @yield('content')
</main>

<footer class="footer-main pt-5">
    <div class="container pb-4">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="SanBongLFC" style="width: 42px; height: 42px; object-fit: contain;">
                    <h5 class="mb-0 text-success">SanBongLFC</h5>
                </div>

                <p>
                    SanBongLFC là nền tảng đặt sân bóng nhanh chóng,
                    tiện lợi và an toàn cho người dùng.
                </p>
            </div>

            <div class="col-lg-2 col-md-4">
                <h5>Liên kết</h5>
                <ul class="list-unstyled d-grid gap-2 mt-3">
                    <li><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li><a href="{{ route('home') }}">Sân bóng</a></li>
                    @auth
                        <li><a href="{{ route('user.bookings.index') }}">Đơn của tôi</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Đặt sân</a></li>
                    @endauth
                    <li><a href="#">Liên hệ</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-4">
                <h5>Thông tin liên hệ</h5>
                <ul class="list-unstyled d-grid gap-2 mt-3">
                    <li><i class="bi bi-geo-alt text-success me-2"></i> Hoài Đức, Hà Nội</li>
                    <li><i class="bi bi-telephone text-success me-2"></i> 1900 1234</li>
                    <li><i class="bi bi-envelope text-success me-2"></i> support@sanbonglfc.vn</li>
                    <li><i class="bi bi-clock text-success me-2"></i> 07:00 - 22:00</li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-4">
                <h5>Mạng xã hội</h5>
                <div class="d-grid gap-2 mt-3">
                    <a href="#" class="btn btn-dark border text-start rounded-3">Facebook</a>
                    <a href="#" class="btn btn-dark border text-start rounded-3">Zalo</a>
                    <a href="#" class="btn btn-dark border text-start rounded-3">TikTok</a>
                    <a href="#" class="btn btn-dark border text-start rounded-3">YouTube</a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom py-3 text-center">
        © {{ date('Y') }} SanBongLFC - Website đặt sân bóng tiện lợi
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>