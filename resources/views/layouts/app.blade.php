<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SanBongLFC')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            color: #111827;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .user-header {
            background: #fff;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 10px rgba(15, 23, 42, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            gap: 16px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #16a34a;
            white-space: nowrap;
        }

        .logo img {
            height: 44px;
            object-fit: contain;
            display: block;
        }

        .logo span {
            font-size: 18px;
            font-weight: 700;
            color: #16a34a;
        }

        .search {
            flex: 1;
            max-width: 500px;
        }

        .search input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            outline: none;
        }

        .search input:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            white-space: nowrap;
        }

        .custom-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid transparent;
            padding: 10px 16px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .custom-btn-primary {
            background: #16a34a;
            color: #fff;
            border-color: #16a34a;
        }

        .custom-btn-primary:hover {
            background: #15803d;
            color: #fff;
        }

        .custom-btn-secondary {
            background: #fff;
            color: #111827;
            border-color: #d1d5db;
        }

        .custom-btn-secondary:hover {
            background: #f1f5f9;
            color: #111827;
        }

        .account-menu {
            position: relative;
        }

        .account-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #fff;
            cursor: pointer;
            font-weight: 600;
        }

        .dropdown-account {
            display: none;
            position: absolute;
            right: 0;
            top: 54px;
            width: 230px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.12);
            padding: 10px;
            z-index: 1100;
        }

        .dropdown-account.active {
            display: block;
        }

        .dropdown-account a,
        .dropdown-account button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 10px 12px;
            border-radius: 12px;
            color: #111827;
            transition: background 0.2s;
            border: none;
            background: transparent;
            font-size: 14px;
        }

        .dropdown-account a:hover,
        .dropdown-account button:hover {
            background: #f1f5f9;
        }

        .dropdown-user-name {
            padding: 10px 12px;
            color: #111827;
            font-weight: 700;
            border-bottom: 1px solid #eef2f7;
            margin-bottom: 6px;
        }

        .banner {
            background:
                linear-gradient(135deg, rgba(22, 101, 52, 0.82), rgba(21, 128, 61, 0.82)),
                url('{{ asset('images/banner1.png') }}') no-repeat center/cover;
            color: #fff;
            padding: 84px 24px 56px;
            overflow: hidden;
            position: relative;
        }

        .banner::after {
            content: '';
            position: absolute;
            right: -120px;
            top: -30px;
            width: 520px;
            height: 520px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
        }

        .hero {
            max-width: 1180px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: clamp(3rem, 4vw, 4.5rem);
            line-height: 1.05;
            margin-bottom: 16px;
            font-weight: 800;
        }

        .hero-title .highlight {
            color: #a7f3d0;
        }

        .hero-text {
            max-width: 540px;
            margin-bottom: 32px;
            font-size: 16px;
            opacity: 0.92;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 24px;
            width: min(640px, 100%);
            backdrop-filter: blur(18px);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .form-grid .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-grid label {
            font-weight: 600;
            font-size: 14px;
        }

        .form-grid input,
        .form-grid select {
            width: 100%;
            border: none;
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.92);
            font-size: 14px;
        }

        .btn-search {
            width: 100%;
            background: #16a34a;
            color: #fff;
            padding: 14px 18px;
            border-radius: 16px;
            border: none;
            font-size: 15px;
            font-weight: 700;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 40px;
        }

        .stat {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 18px;
            padding: 24px;
            text-align: center;
        }

        .stat strong {
            display: block;
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .main-content {
            min-height: 420px;
        }

        .content {
            max-width: 1180px;
            margin: 0 auto;
            padding: 40px 24px 80px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            gap: 12px;
        }

        .section-head h2 {
            margin: 0;
            font-size: 28px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .pitch-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s;
        }

        .pitch-card:hover {
            transform: translateY(-4px);
        }

        .pitch-thumb {
            height: 180px;
            background: linear-gradient(135deg, #d1fae5 0%, #86efac 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .pitch-body {
            padding: 20px;
        }

        .pitch-title {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .pitch-meta {
            color: #475569;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .pitch-footer {
            display: flex;
            justify-content: space-between;
            color: #475569;
            font-size: 13px;
        }

        footer {
            margin-top: 40px;
        }

        .footer-top {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 32px;
            background: #0f172a;
            color: #e2e8f0;
            padding: 60px 24px 40px;
        }

        .footer-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .footer-col h3 {
            margin: 0;
            font-size: 18px;
            color: #fff;
        }

        .footer-col p,
        .footer-col a,
        .footer-col .contact-item {
            font-size: 14px;
            line-height: 1.7;
            color: #cbd5e1;
        }

        .footer-col a:hover {
            color: #ffffff;
        }

        .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #22c55e;
            font-size: 18px;
        }

        .footer-logo img {
            height: 44px;
            object-fit: contain;
            display: block;
        }

        .footer-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 10px;
        }

        .footer-contact {
            display: grid;
            gap: 10px;
        }

        .footer-contact .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .footer-contact .contact-item span {
            min-width: 22px;
            display: inline-flex;
            justify-content: center;
            color: #22c55e;
        }

        .footer-social {
            display: grid;
            gap: 10px;
        }

        .footer-social a {
            display: inline-block;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
        }

        .footer-bottom {
            background: #020617;
            color: #94a3b8;
            text-align: center;
            padding: 18px 24px;
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .user-header {
                flex-wrap: wrap;
            }

            .search {
                order: 3;
                max-width: 100%;
                flex-basis: 100%;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .footer-top {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .user-header {
                padding: 14px 16px;
            }

            .header-actions {
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .banner {
                padding: 40px 18px 40px;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .footer-top {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<header class="user-header">
    <a href="{{ route('home') }}" class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="SanBongLFC">
        <span>SanBongLFC</span>
    </a>

    <form action="{{ route('home') }}" method="GET" class="search">
        <input type="text"
               name="keyword"
               value="{{ request('keyword') }}"
               placeholder="Tìm kiếm sân theo tên, địa điểm...">
    </form>

    <div class="header-actions">
        @auth
            <a href="{{ route('user.bookings.index') }}" class="custom-btn custom-btn-secondary">
                <i class="bi bi-calendar-check me-1"></i>
                Đơn của tôi
            </a>
        @endauth

        <button type="button" class="custom-btn custom-btn-secondary">
            🔔
        </button>

        <div class="account-menu">
            <div class="account-button" onclick="toggleAccountMenu()">
                <span>💼</span>
                <span>Tài khoản</span>
            </div>

            <div id="accountDropdown" class="dropdown-account">
                @auth
                    <div class="dropdown-user-name">
                        {{ Auth::user()->name }}
                    </div>

                    <a href="{{ route('user.bookings.index') }}">
                        <i class="bi bi-calendar-check me-2"></i>
                        Đơn đặt sân của tôi
                    </a>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Trang quản trị
                        </a>
                    @endif

                    <form id="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Đăng nhập
                    </a>

                    <a href="{{ route('register') }}">
                        <i class="bi bi-person-plus me-2"></i>
                        Đăng ký
                    </a>
                @endauth
            </div>
        </div>

        @auth
            <a href="{{ route('home') }}" class="custom-btn custom-btn-primary">
                Đặt sân ngay
            </a>
        @else
            <a href="{{ route('register') }}" class="custom-btn custom-btn-primary">
                Đăng ký
            </a>
        @endauth
    </div>
</header>

@if(request()->routeIs('home'))
    <section class="banner">
        <div class="hero">
            <h1 class="hero-title">
                Đặt sân bóng <span class="highlight">nhanh chóng</span> & dễ dàng
            </h1>

            <p class="hero-text">
                Tìm sân, chọn giờ và thanh toán chỉ trong vài phút.
                Trải nghiệm đặt sân thông minh cùng SanBongLFC.
            </p>

            <form action="{{ route('home') }}" method="GET" class="hero-card">
                <div class="form-grid">
                    <div class="field">
                        <label>Tỉnh/Thành phố</label>
                        <select name="city">
                            <option value="">Chọn tỉnh thành</option>
                            <option value="ha-noi" @selected(request('city') === 'ha-noi')>Hà Nội</option>
                            <option value="tp-hcm" @selected(request('city') === 'tp-hcm')>TP.HCM</option>
                            <option value="da-nang" @selected(request('city') === 'da-nang')>Đà Nẵng</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Loại sân</label>
                        <select name="field_type">
                            <option value="">Tất cả loại sân</option>
                            <option value="5" @selected(request('field_type') === '5')>Sân 5</option>
                            <option value="7" @selected(request('field_type') === '7')>Sân 7</option>
                            <option value="11" @selected(request('field_type') === '11')>Sân 11</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Ngày đặt</label>
                        <input type="date" name="booking_date" value="{{ request('booking_date') }}">
                    </div>
                </div>

                <button type="submit" class="btn-search">
                    🔍 Tìm sân ngay
                </button>
            </form>

            <div class="stats">
                <div class="stat">
                    <strong>500+</strong>
                    Sân bóng
                </div>

                <div class="stat">
                    <strong>10K+</strong>
                    Lượt đặt
                </div>

                <div class="stat">
                    <strong>63</strong>
                    Tỉnh thành
                </div>
            </div>
        </div>
    </section>
@endif

<main class="main-content">
    @yield('content')
</main>

<footer>
    <div class="footer-top">
        <div class="footer-col">
            <div class="footer-logo">
                <img src="{{ asset('images/logo.png') }}" alt="SanBongLFC">
                SanBongLFC
            </div>

            <p>
                SanBongLFC là nền tảng đặt sân bóng nhanh chóng,
                tiện lợi và an toàn cho người dùng trên toàn quốc.
            </p>
        </div>

        <div class="footer-col">
            <h3>Liên kết nhanh</h3>

            <ul class="footer-list">
                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                <li><a href="{{ route('home') }}">Sân bóng</a></li>

                @auth
                    <li><a href="{{ route('user.bookings.index') }}">Đơn của tôi</a></li>
                @else
                    <li><a href="{{ route('login') }}">Đặt sân</a></li>
                @endauth

                <li><a href="#">Tin tức</a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Thông tin liên hệ</h3>

            <div class="footer-contact">
                <div class="contact-item">
                    <span>📍</span>
                    <div>Địa chỉ: 123 Đường Sân Bóng, Hoài Đức, BTL, Hà Nội</div>
                </div>

                <div class="contact-item">
                    <span>📞</span>
                    <div>Hotline: 1900 1234</div>
                </div>

                <div class="contact-item">
                    <span>📧</span>
                    <div>Email: support@sanbonglfc.vn</div>
                </div>

                <div class="contact-item">
                    <span>🕒</span>
                    <div>Giờ mở cửa: 07:00 - 22:00</div>
                </div>
            </div>
        </div>

        <div class="footer-col">
            <h3>Mạng xã hội</h3>

            <div class="footer-social">
                <a href="#">Facebook</a>
                <a href="#">Zalo</a>
                <a href="#">TikTok</a>
                <a href="#">YouTube</a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        © 2026 LFC Football Website đặt sân tiện lợi
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleAccountMenu() {
        const dropdown = document.getElementById('accountDropdown');

        if (dropdown) {
            dropdown.classList.toggle('active');
        }
    }

    document.addEventListener('click', function(event) {
        const menu = document.getElementById('accountDropdown');
        const button = document.querySelector('.account-button');

        if (menu && button && !button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.remove('active');
        }
    });
</script>

@stack('scripts')

</body>
</html>