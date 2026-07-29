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

        .hero-section,
        .about-banner,
        .news-hero {
            position: relative;
            min-height: 520px;
            background:
                linear-gradient(135deg, rgba(21, 128, 61, .92), rgba(22, 101, 52, .82)),
                url('{{ asset('images/banner1.png') }}') center/cover no-repeat;
            color: #ffffff;
            overflow: hidden;
        }

        .about-banner {
            min-height: 380px;
        }

        .news-hero {
            min-height: 420px;
            padding-top: 100px;
            padding-bottom: 90px;
        }

        .hero-section::after,
        .about-banner::after,
        .news-hero::after {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            top: -100px;
            right: -80px;
        }

        .home-page-grid {
            display: grid;
            grid-template-columns: minmax(260px, 320px) 1fr;
            gap: 28px;
            align-items: start;
            margin: 0 auto;
            max-width: 1180px;
            padding: 36px 0 28px;
        }

        .home-sidebar {
            background: linear-gradient(180deg, #f4fef8 0%, #ffffff 100%);
            border-radius: 28px;
            padding: 26px;
            box-shadow: 0 28px 60px rgba(16, 185, 129, .16);
            border: 1px solid rgba(16, 185, 129, .18);
            display: flex;
            flex-direction: column;
            gap: 22px;
            position: sticky;
            top: 100px;
            align-self: start;
        }

        .home-sidebar h2 {
            font-size: 1.45rem;
            margin-bottom: 0.9rem;
            color: #115e3d;
            letter-spacing: 0.01em;
        }

        .home-sidebar p,
        .home-sidebar li {
            color: #374151;
            line-height: 1.9;
        }

        .home-sidebar ul {
            padding-left: 1rem;
            margin: 0 0 1rem;
        }

        .home-sidebar li {
            margin-bottom: 0.9rem;
            position: relative;
        }

        .home-sidebar li::before {
            content: "";
            position: absolute;
            left: -1rem;
            top: 0.55rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a34a;
        }

        .home-sidebar ul li a {
            color: #065f46;
            font-weight: 600;
            text-decoration: none;
        }

        .home-sidebar ul li a:hover {
            color: #0f766e;
            text-decoration: underline;
        }

        .home-sidebar .sidebar-card {
            background: #ffffff;
            border: 1px solid rgba(16, 185, 129, .15);
            border-radius: 22px;
            padding: 20px 18px;
            margin-top: 0;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .home-sidebar .sidebar-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 35px rgba(16, 185, 129, .14);
        }

        .home-sidebar .sidebar-card h3 {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 0;
            margin-bottom: 0.85rem;
            font-size: 1.03rem;
            color: #0f5132;
        }

        .home-sidebar .sidebar-card p {
            margin-bottom: 0;
            color: #475569;
        }

        .home-sidebar .sidebar-card .card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 14px;
            background: #d9f7e3;
            color: #0f5132;
            font-size: 1rem;
            box-shadow: inset 0 3px 6px rgba(16, 185, 129, .12);
        }

        .home-sidebar .sidebar-stats {
            display: grid;
            gap: 14px;
            margin-top: 10px;
        }

        .home-sidebar .sidebar-stat {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(22, 163, 74, .08);
            border-radius: 18px;
            border: 1px solid rgba(22, 163, 74, .18);
            padding: 16px;
        }

        .home-sidebar .sidebar-stat strong {
            font-size: 1rem;
            color: #0f5132;
            display: block;
        }

        .home-sidebar .sidebar-stat span {
            font-size: 0.93rem;
            color: #475569;
        }

        .home-sidebar .sidebar-cta {
            display: grid;
            gap: 12px;
            margin-top: 0;
        }

        .home-sidebar .sidebar-cta .btn {
            width: 100%;
            padding: 14px 18px;
            font-weight: 700;
            border-radius: 16px;
        }

        .home-sidebar .sidebar-cta .btn-outline-success {
            background: rgba(16, 185, 129, .1);
            border-color: #10b981;
            color: #065f46;
        }

        .home-sidebar .sidebar-card:last-child {
            margin-bottom: 0;
        }

        .news-hero .hero-text-wrap {
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
        }

        .news-hero h1,
        .news-hero p {
            text-shadow: 0 18px 40px rgba(0, 0, 0, .25);
        }

        .news-banner-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            background: rgba(255, 255, 255, .12);
            border-radius: 999px;
            color: #d1fae5;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 24px;
        }

        .news-card {
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 24px 50px rgba(15, 23, 42, .12);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .hero-schedule-section {
            margin-top: -45px;
            z-index: 5;
            position: relative;
        }

        .hero-schedule-section .card {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }

        .hero-schedule-section .card-body {
            padding: 1rem 1rem 1.25rem;
        }

        .hero-schedule-header {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .hero-schedule-controls {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .hero-schedule-range {
            font-weight: 700;
            padding: 0.6rem 0.9rem;
            border-radius: 999px;
            background: #f8fafc;
            font-size: 0.9rem;
        }

        .hero-day-tabs {
            padding-bottom: 0.65rem;
            margin-bottom: 0.65rem;
            border-bottom: 1px solid #eff2f7;
        }

        .hero-day-tabs .hero-day-tab {
            min-width: 84px;
            border-radius: 14px;
            padding: 0.55rem 0.75rem;
            text-align: center;
            background: #f8fafc;
            border-color: #e5e7eb;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.2;
        }

        .hero-day-tabs .hero-day-tab.active {
            background: #16a34a;
            color: #ffffff;
            border-color: #16a34a;
        }

        .field-schedule-row {
            display: grid;
            grid-template-columns: minmax(220px, 260px) 1fr;
            gap: 0.75rem;
            align-items: center;
            padding: 0.55rem;
            background: #ffffff;
            border-radius: 15px;
            border: 1px solid #f1f5f9;
        }

        .field-schedule-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0.85rem;
            background: #ffffff;
            border-radius: 15px;
        }

        .field-icon {
            width: 44px;
            height: 44px;
        }

        .field-schedule-day-wrapper {
            overflow-x: auto;
            padding: 0.65rem 0.5rem 0.65rem 0;
            background: #f8fafc;
            border-radius: 15px;
        }

        .field-schedule-day {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(60px, 1fr);
            gap: 0.45rem;
            align-items: center;
        }

        .schedule-slot {
            border-radius: 999px;
            padding: 6px 8px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #0f172a;
            border: 1px solid transparent;
            min-width: 60px;
            text-align: center;
            line-height: 1.1;
            white-space: nowrap;
        }

        .schedule-slot.available {
            background: #dcfce7;
            border-color: #22c55e;
        }

        .schedule-slot.booked {
            background: #fee2e2;
            border-color: #dc2626;
        }

        .schedule-slot.played {
            background: #fef3c7;
            border-color: #d97706;
        }

        .schedule-slot.locked {
            background: #e2e8f0;
            border-color: #475569;
            color: #475569;
        }

        .schedule-slot .slot-time {
            display: block;
        }

        .hero-schedule-header {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .hero-schedule-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .hero-schedule-range {
            font-weight: 700;
        }

        .hero-schedule-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            font-size: 12px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: #475569;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 1px solid transparent;
            display: inline-block;
        }

        .hero-status-legend {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 10px;
        }

        .schedule-slot.available {
            background: #d1fae5;
            border-color: #10b981;
        }

        .schedule-slot.booked {
            background: #fee2e2;
            border-color: #dc2626;
        }

        .schedule-slot.played {
            background: #fef3c7;
            border-color: #d97706;
        }

        .schedule-slot.locked {
            background: #e2e8f0;
            border-color: #475569;
            color: #475569;
        }

        .schedule-slot .slot-time {
            display: block;
        }

        .hero-status-legend {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
        }

        .legend-dot {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 1px solid transparent;
        }

        .legend-available { background: #d1fae5; border-color: #10b981; }
        .legend-booked { background: #fee2e2; border-color: #dc2626; }
        .legend-played { background: #fef3c7; border-color: #d97706; }
        .legend-locked { background: #e2e8f0; border-color: #475569; }

        .news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 28px 60px rgba(15, 23, 42, .16);
        }

        .news-card img {
            height: 240px;
            object-fit: cover;
        }

        .news-featured-card {
            overflow: hidden;
            border-radius: 28px;
        }

        .news-featured-image {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
        }

        .news-sidebar-thumb {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 16px;
        }

        .news-card.small-card img {
            height: 180px;
        }

        .news-list-item {
            display: grid;
            grid-template-columns: minmax(200px, 280px) 1fr;
            gap: 0;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .news-list-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, .12);
        }

        .news-list-image {
            min-width: 200px;
            max-width: 280px;
            overflow: hidden;
        }

        .news-list-image img {
            width: 100%;
            height: 100%;
            min-height: 180px;
            object-fit: cover;
            display: block;
        }

        .news-list-content {
            padding: 24px;
        }

        .news-detail-figure {
            overflow: hidden;
            border-radius: 28px;
            max-width: 720px;
            margin: 0 auto 1.5rem;
        }

        .news-detail-figure-image {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            display: block;
        }

        .news-detail-link {
            position: relative;
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .news-detail-link-label {
            position: absolute;
            right: 16px;
            bottom: 16px;
            background: rgba(15, 23, 42, .8);
            color: #fff;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: .9rem;
            font-weight: 600;
            backdrop-filter: blur(6px);
        }

        .news-detail-modal-image {
            max-height: 100vh;
            object-fit: contain;
            display: block;
        }

        .news-detail-link:hover .news-detail-link-label {
            background: rgba(15, 23, 42, .95);
        }

        .news-detail-card {
            border-radius: 28px;
        }

        .news-detail-card .card-body {
            padding: 24px;
        }

        .news-list-content {
            padding: 24px;
        }

        .news-detail-header .card-body {
            padding: 24px;
        }

        .news-card-body {
            padding: 24px;
        }

        .news-card-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .news-card-meta {
            color: #6b7280;
            font-size: .94rem;
            margin-bottom: 14px;
        }

        .news-card-text {
            color: #374151;
            margin-bottom: 20px;
        }

        .news-article {
            line-height: 1.85;
            color: #1f2937;
        }

        .news-article h2,
        .news-article h3,
        .news-article h4 {
            margin-top: 1.8rem;
            margin-bottom: 1rem;
        }

        .news-article p {
            margin-bottom: 1rem;
        }

        .news-article img {
            max-width: 100%;
            border-radius: 16px;
            margin: 30px 0;
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

        .schedule-card-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }

        .schedule-card-header p {
            margin-bottom: 0;
            color: #6b7280;
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

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 0.95rem;
        }

        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #475569;
            font-size: 0.98rem;
        }

        .feature-list li i {
            margin-top: 2px;
            min-width: 22px;
        }

        .feature-card {
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 1.5rem;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, .1);
        }

        .feature-icon {
            width: 54px;
            height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .process-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
        }

        .process-card {
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 1.75rem;
            background: #ffffff;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .process-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, .08);
        }

        .process-step {
            display: inline-flex;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .contact-card {
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            background: #ffffff;
        }

        .contact-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            min-width: 44px;
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
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('stadiums.index') ? 'active' : '' }}" href="{{ route('stadiums.index') }}">Sân bóng</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">Tin tức</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? '' : '' }}" href="{{ route('home') }}#contact">Liên hệ</a></li>
            </ul>

            @php
                $clientNotifications = [];
                if(session('success')) {
                    $clientNotifications[] = [
                        'type' => 'success',
                        'title' => 'Thành công',
                        'message' => session('success'),
                        'icon' => 'bi-check-circle-fill',
                        'bg' => 'bg-success text-white',
                    ];
                }
                if(session('error')) {
                    $clientNotifications[] = [
                        'type' => 'error',
                        'title' => 'Lỗi',
                        'message' => session('error'),
                        'icon' => 'bi-x-circle-fill',
                        'bg' => 'bg-danger text-white',
                    ];
                }
                if(session('warning')) {
                    $clientNotifications[] = [
                        'type' => 'warning',
                        'title' => 'Chú ý',
                        'message' => session('warning'),
                        'icon' => 'bi-exclamation-triangle-fill',
                        'bg' => 'bg-warning text-dark',
                    ];
                }
            @endphp

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item dropdown">
                    <button class="btn btn-light border rounded-3 position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        @if(count($clientNotifications) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ count($clientNotifications) }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2" style="min-width: 320px;">
                        <li class="dropdown-header">Thông báo</li>
                        @if(count($clientNotifications) > 0)
                            @foreach($clientNotifications as $notification)
                                <li>
                                    <a class="dropdown-item d-flex align-items-start gap-3" href="#">
                                        <span class="badge rounded-pill p-2 {{ $notification['bg'] }}">
                                            <i class="bi {{ $notification['icon'] }}"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $notification['title'] }}</div>
                                            <div class="small text-muted">{{ $notification['message'] }}</div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <span class="dropdown-item-text text-muted small">Chưa có thông báo mới.</span>
                            </li>
                        @endif
                    </ul>
                </li>

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

    @yield('hero-bottom')

    <main>
        <div class="container">
            <div class="home-page-grid">
                <aside class="home-sidebar">
                    <h2>Thông tin nhanh</h2>
                    <p>Trang chủ SanBongLFC hiện gồm các phần chính:</p>
                    <ul>
                        <li><a href="#about">Giới thiệu</a></li>
                        <li><a href="#amenities">Tiện ích</a></li>
                        <li><a href="#booking-process">Quy trình đặt sân</a></li>
                        <li><a href="#news">Tin tức</a></li>
                        <li><a href="#contact">Liên hệ hỗ trợ</a></li>
                    </ul>

                    <div class="sidebar-card">
                        <h3><span class="card-icon">📰</span> Tin tức mới</h3>
                        <p>Cập nhật nhanh tin tức bóng đá và các bài viết liên quan đến SanBongLFC.</p>
                    </div>

                    <div class="sidebar-card">
                        <h3><span class="card-icon">📞</span> Hỗ trợ khách hàng</h3>
                        <p>Hotline: <strong>1900 1234</strong> · Email: <strong>support@sanbonglfc.vn</strong></p>
                    </div>

                    <div class="sidebar-card">
                        <h3><span class="card-icon">⚽</span> Đặt sân dễ dàng</h3>
                        <p>Chọn sân, chọn giờ, xác nhận thanh toán và nhận thông tin ngay trên trang chủ.</p>
                    </div>

                    <div class="sidebar-cta">
                        <a href="{{ route('home') }}#news" class="btn btn-success rounded-3">Xem tin tức</a>
                        <a href="{{ route('home') }}#contact" class="btn btn-outline-success rounded-3">Liên hệ ngay</a>
                    </div>
                </aside>

                <div class="home-page-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
@elseif(request()->routeIs('about'))
    <section class="hero-section about-hero">
        <div class="container hero-content text-center">
            <div class="hero-text-wrap mx-auto" style="max-width: 780px;">
                <h1 class="hero-title mb-3">Giới thiệu cơ sở sân bóng LFC</h1>
                <p class="lead mb-4">
                    Tìm hiểu về LFC, dịch vụ đặt sân chuyên nghiệp và tiện ích tại cơ sở sân bóng của chúng tôi ở Hoài Đức, Hà Nội.
                </p>
                <img src="{{ asset('images/logo.png') }}" alt="SanBongLFC Logo" class="img-fluid mx-auto" style="max-width: 180px; object-fit: contain;" />
            </div>
        </div>
    </section>
@elseif(request()->routeIs('news.*'))
    <section class="news-hero">
        <div class="container">
            <div class="hero-text-wrap">
                <div class="news-banner-label">Tin tức bóng đá</div>
                <h1 class="hero-title mb-3">Cập nhật nhanh nhất mọi tin tức bóng đá LFC</h1>
                <p class="lead mb-4">
                    Tin tức mới nhất, nhận định chuyên sâu và các sự kiện nóng hổi từ sân cỏ LFC. Theo dõi ngay để không bỏ lỡ mọi trận đấu và bản tin đáng chú ý.
                </p>
            </div>
        </div>
    </section>
@endif

@unless(request()->routeIs('home'))
    <main>
        <div class="container py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                        <div>
                            <strong>Thành công!</strong>
                            <div class="small">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                        <div>
                            <strong>Lỗi!</strong>
                            <div class="small">{{ session('error') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div>
                            <strong>Chú ý!</strong>
                            <div class="small">{{ session('warning') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>
@endunless

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
                <h5>Vị trí sân bóng</h5>
                <div class="ratio ratio-16x9 rounded-4 overflow-hidden mt-3">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.661110353743!2d105.63632191532473!3d21.043361792646775!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135aac4d8db92fd%3A0x8d9d33fc5d27e946!2zU-G7kW5nIGJhbmggTGFpIMSQw6Bu!5e0!3m2!1svi!2s!4v1701100000000!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="mt-3">
                    <a href="https://maps.app.goo.gl/yT4RrvdM8o8RotQj7" target="_blank" rel="noreferrer" class="btn btn-success btn-sm">Xem bản đồ</a>
                </div>
                <p class="text-white small mt-2 mb-0" style="opacity: .88;">Sân bóng Lai Xá, Thôn Lai Xá, Hoài Đức, Hà Nội.</p>
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