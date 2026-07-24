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

        .header-menu {
            gap: 0.25rem;
        }

        .header-menu .nav-link {
            color: #0f172a;
            font-weight: 600;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
        }

        .header-menu .nav-link:hover,
        .header-menu .nav-link.active {
            background: #ecfdf5;
            color: #15803d;
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

        .hero-text-wrap {
            max-width: 760px;
            margin: 0 auto;
            text-align: center;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
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
            <ul class="navbar-nav header-menu align-items-lg-center me-lg-3">
                <li class="nav-item"><a class="nav-link active" href="{{ route('home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#about">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#news">Tin tức</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#services">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#contact">Liên hệ</a></li>
            </ul>

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
            <div class="hero-text-wrap">
                <h1 class="hero-title mb-3">
                    Đặt sân bóng <span>chuyên nghiệp</span> trong vài phút
                </h1>

                <p class="lead mb-4">
                    Từ sân 5, sân 7 đến sân 11 chất lượng, SanBongLFC mang đến trải nghiệm đặt sân nhanh, an toàn và tiện lợi cho mọi nhu cầu của bạn.
                </p>

                <div class="hero-actions">
                    <a href="{{ route('home') }}#stadiums" class="btn btn-success rounded-3 px-4 py-2 fw-bold">
                        <i class="bi bi-calendar-check me-1"></i>
                        Xem sân ngay
                    </a>
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