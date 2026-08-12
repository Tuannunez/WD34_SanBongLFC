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
            background: linear-gradient(180deg, #ebfdf2 0%, #f8fff5 100%);
            font-family: Inter, Arial, sans-serif;
            color: #0f172a;
        }

        .navbar-user {
            background: rgba(8, 78, 31, 0.92);
            border-bottom: 1px solid rgba(255,255,255,0.10);
            box-shadow: 0 18px 45px rgba(4, 75, 35, .18);
            backdrop-filter: blur(14px);
        }

        .navbar-brand img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .navbar-brand span {
            color: #d8ff5d;
            font-weight: 900;
            letter-spacing: .03em;
        }

        .header-menu {
            gap: 0.5rem;
        }

        .header-menu .nav-link {
            color: rgba(255,255,255,0.92);
            font-weight: 600;
            padding: 0.65rem 1rem;
            border-radius: 999px;
            transition: all .2s ease;
        }

        .header-menu .nav-link:hover,
        .header-menu .nav-link.active {
            background: rgba(255,255,255,0.14);
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0, 0, 0, .15);
        }

        .hero-section {
            position: relative;
            min-height: 420px;
            height: 420px;
            color: #0f172a;
            overflow: hidden;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .hero-slides {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 0;
            background: transparent;
        }

        .hero-slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 1.4s ease-in-out;
            will-change: opacity, transform;
            filter: brightness(1.04) contrast(1.08) saturate(1.08);
            transform: scale(1.06);
            transform-origin: center center;
            background-attachment: scroll;
            backface-visibility: hidden;
            z-index: 0;
        }

        .hero-slide.active {
            opacity: 1;
        }

        .hero-slide.slide1 {
            background-image: url('/images/banner1.png');
        }

        .hero-slide.slide2 {
            background-image: url('/images/banner2.png');
        }

        .hero-slide.slide3 {
            background-image: url('/images/banner3.png');
        }

        .hero-section::after {
            content: "";
            position: absolute;
            inset: 0;
            background: none;
            pointer-events: none;
        }

        .hero-text-wrap {
            background: transparent;
            box-shadow: none;
            border: none;
            padding: 0;
        }

        .about-banner,
        .news-hero {
            min-height: 380px;
            background: linear-gradient(180deg, rgba(5, 75, 34, .72), rgba(5, 75, 34, .48)), url('/images/banner1.png') center/cover no-repeat;
            background-size: cover;
            background-blend-mode: multiply;
        }

        .about-hero {
            min-height: 420px;
            height: 420px;
            position: relative;
            background: linear-gradient(90deg, rgba(5, 37, 25, 0.72), rgba(5, 75, 34, 0.35)), url('/images/banner1.png') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 30px;
            overflow: hidden;
        }

        .about-hero .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-hero .hero-content {
            max-width: 760px;
            text-align: left;
            color: #ffffff;
            padding: 0;
            margin: 0;
        }

        .about-hero .hero-text-wrap {
            display: block;
            max-width: 760px;
        }

        .about-hero .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 999px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.18);
            color: #e7f7ee;
            font-size: 13px;
            margin-bottom: 26px;
        }

        .about-hero .hero-title {
            color: #ffffff;
            text-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            margin-bottom: 18px;
            font-size: clamp(2.6rem, 4vw, 4.2rem);
            line-height: 1.05;
            font-weight: 900;
        }

        .about-hero .lead {
            color: rgba(255,255,255,0.92) !important;
            font-size: 1.08rem;
            line-height: 1.8;
            max-width: 620px;
            margin-bottom: 28px !important;
        }

        .about-hero .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 18px;
        }

        .about-hero .hero-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: rgba(255,255,255,0.92);
            font-size: 15px;
        }

        .about-hero .hero-meta-item::before {
            content: '•';
            color: #22c55e;
            font-size: 20px;
            line-height: 1;
        }

        .news-hero {
            min-height: 420px;
            padding-top: 100px;
            padding-bottom: 90px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 840px;
            padding: 0 40px;
            display: grid;
            gap: 10px;
            margin: 0;
        }

        .hero-text-wrap {
            display: block;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, .94);
            border: 1px solid rgba(16, 185, 129, .22);
            border-radius: 999px;
            padding: 8px 16px;
            margin-bottom: 18px;
            color: #064e3b;
            box-shadow: 0 10px 25px rgba(10, 60, 30, .05);
        }

        .hero-title {
            font-size: clamp(2.2rem, 3.5vw, 3rem);
            line-height: 1.05;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: #064e3b;
            text-shadow: 0 6px 16px rgba(255, 255, 255, .6);
            margin-bottom: 0.4rem;
        }

        .hero-title span {
            color: #047637;
            display: block;
            font-size: 0.85em;
            line-height: 1.2;
            margin-top: 0.35rem;
        }

        .hero-description {
            display: none;
        }

        .hero-actions {
            display: none;
        }

        .hero-actions .btn {
            min-width: 150px;
            padding: 12px 20px;
            font-size: .95rem;
            font-weight: 700;
            border-radius: 999px;
        }

        .hero-actions .btn-success {
            background-color: #d9f99d;
            border-color: #d9f99d;
            color: #0f172a;
            box-shadow: 0 18px 35px rgba(16, 185, 129, .18);
        }

        .hero-actions .btn-success:hover {
            background-color: #bef264;
            border-color: #bef264;
            color: #0f172a;
        }

        .hero-actions .btn-outline-light {
            border-color: rgba(255,255,255,0.55);
            color: #ffffff;
            min-width: 170px;
            padding: 14px 24px;
            font-weight: 700;
            border-radius: 999px;
        }

        .hero-divider {
            height: 1px;
            background: rgba(255,255,255,0.18);
            margin: 34px 0 0;
            width: 100%;
            max-width: 660px;
        }

        .hero-top-logo {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.5rem;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 999px;
            padding: 12px 18px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .08);
        }

        .hero-top-logo img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, .25);
        }

        .hero-top-logo span {
            color: #f8ffde;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .home-page-grid {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 28px;
            align-items: start;
            margin-top: 40px;
        }

        .home-sidebar {
            display: grid;
            gap: 12px;
            align-items: flex-start;
            max-width: 100%;
        }

        @media (max-width: 1140px) {
            .home-page-grid {
                grid-template-columns: 1fr;
            }
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 30px;
        }

        .hero-stat-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
            background: rgba(255, 255, 255, .22);
            border: 1px solid rgba(199, 255, 79, .26);
            border-radius: 18px;
            padding: 16px 18px;
            color: #0f172a;
            font-size: 0.95rem;
            box-shadow: 0 18px 38px rgba(0, 0, 0, .08);
        }

        .hero-stat-icon {
            font-size: 1.1rem;
        }

        .news-hero .hero-text-wrap {
            max-width: 860px;
            margin: 0 auto;
            text-align: center;
        }

        .hero-feature-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 50px;
        }

        .hero-feature-card {
            background: rgba(255, 255, 255, .12);
            border-radius: 24px;
            padding: 24px 20px;
            border: 1px solid rgba(199, 255, 79, .16);
            box-shadow: 0 20px 40px rgba(0, 0, 0, .12);
            color: #f8fafc;
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
        }

        .hero-feature-card .feature-icon {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: rgba(199, 255, 79, .18);
            color: #0f172a;
            font-size: 1.25rem;
        }

        .hero-feature-card h3 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
        }

        .hero-feature-card p {
            margin: 0;
            color: rgba(255, 255, 255, .78);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .home-sidebar {
            display: grid;
            gap: 20px;
        }

        .home-sidebar h2 {
            margin-bottom: 0.85rem;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        .home-sidebar p,
        .home-sidebar li {
            color: #334155;
            line-height: 1.8;
            font-size: 0.95rem;
        }

        .home-sidebar ul {
            padding-left: 0;
            margin: 0 0 1.2rem;
            list-style: none;
        }

        .home-sidebar li {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            margin-bottom: 1rem;
        }

        .home-sidebar li::before {
            content: "";
            width: 10px;
            height: 10px;
            margin-top: 0.55rem;
            flex-shrink: 0;
            border-radius: 50%;
            background: #047637;
        }

        .home-sidebar ul li a {
            color: #065f46;
            font-weight: 700;
            text-decoration: none;
        }

        .home-sidebar ul li a:hover {
            color: #0f766e;
            text-decoration: underline;
        }

        .home-sidebar .sidebar-card {
            background: rgba(237, 253, 245, .95);
            border: 1px solid rgba(16, 185, 129, .18);
            border-radius: 20px;
            padding: 16px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .home-sidebar .sidebar-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(16, 185, 129, .1);
        }

        .home-sidebar .sidebar-card h3 {
            margin-top: 0;
            margin-bottom: 0.55rem;
            font-size: 1rem;
            color: #0f5132;
        }

        .home-sidebar .sidebar-card p {
            margin-bottom: 0;
            color: #475569;
            font-size: .95rem;
            line-height: 1.6;
        }

        .home-sidebar .sidebar-card .card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: #d9f7e3;
            color: #065f46;
            font-size: 1.1rem;
            box-shadow: inset 0 0 0 1px rgba(16, 185, 129, .12);
        }

        .home-sidebar .sidebar-stats {
            display: grid;
            gap: 14px;
            margin-top: 10px;
        }

        .home-sidebar .sidebar-stat {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ecfdf5;
            border-radius: 18px;
            border: 1px solid rgba(16, 185, 129, .15);
            padding: 16px;
        }

        .home-sidebar .sidebar-stat strong {
            font-size: 1rem;
            color: #0f5132;
            display: block;
        }

        .home-sidebar .sidebar-stat span {
            font-size: 0.94rem;
            color: #475569;
        }

        .home-sidebar .sidebar-cta {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 0;
        }

        .home-sidebar .sidebar-cta .btn {
            width: 100%;
            padding: 10px 16px;
            font-weight: 700;
            border-radius: 14px;
            min-height: auto;
            font-size: .95rem;
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
            background: #047637;
            color: #ffffff;
            border-color: #047637;
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
            background: #E8F6EE;
            border-color: #047637;
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

        .news-highlight-card {
            background: rgba(237, 253, 245, .95);
            border: 1px solid rgba(16, 185, 129, .18);
            border-radius: 24px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .news-highlight-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 35px rgba(16, 185, 129, .14);
        }

        .news-highlight-card .card-body {
            padding: 0.9rem 0.95rem 0.9rem 0.95rem;
        }

        .news-highlight-card .news-thumb {
            width: 72px;
            height: 72px;
            flex-shrink: 0;
            overflow: hidden;
            background: #f8faf7;
        }

        .news-highlight-card .news-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .news-highlight-card .news-icon {
            min-width: 40px;
            width: 40px;
            height: 40px;
            border-radius: 16px;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .news-highlight-card .news-card-meta {
            color: #065f46;
            font-size: .82rem;
            font-weight: 700;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .news-highlight-card .news-card-title {
            font-size: 1.03rem;
            font-weight: 800;
            margin-bottom: .55rem;
            color: #0f172a;
            letter-spacing: -.02em;
        }

        .news-highlight-card .news-card-text {
            margin-bottom: .75rem;
            color: #475569;
            line-height: 1.5;
        }

        .news-highlight-card a {
            font-size: .95rem;
        }

        .contact-line {
            gap: 0.9rem;
        }

        .contact-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .contact-card .card-body {
            padding: 1rem 1rem 1rem 1rem;
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

        .schedule-card-header h2 {
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
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
            color: #047637;
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
            border: 1px solid rgba(34, 197, 94, .16);
            border-radius: 24px;
            padding: 1.5rem;
            background: #ffffff;
            transition: transform .2s ease, box-shadow .2s ease;
            min-height: 210px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%;
            min-width: 0;
            text-align: left;
            white-space: normal;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, .12);
        }

        .feature-card h5 {
            margin-bottom: 0.75rem;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            writing-mode: horizontal-tb;
            text-orientation: mixed;
        }

        .feature-card p {
            margin-bottom: 0;
            white-space: normal;
            overflow-wrap: break-word;
            word-break: break-word;
            writing-mode: horizontal-tb;
            text-orientation: mixed;
        }

        .feature-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 14px;
            width: 100%;
            min-width: 0;
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
            border: 1px solid rgba(34, 197, 94, .16);
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
            background: #c7ff4f;
            color: #0f172a;
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
            .footer-links {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 32px;
            }

            .hero-description {
                font-size: .95rem;
            }

            .hero-actions {
                gap: 10px;
            }

            .hero-content {
                padding: 35px 0;
            }
        }

        /* Bootstrap Success Color Override */
        .btn-success { background-color: #047637; border-color: #047637; }
        .btn-success:hover { background-color: #035F2C; border-color: #035F2C; }
        .btn-success.disabled, .btn-success:disabled { background-color: #047637; border-color: #047637; }
        .btn-success:focus, .btn-success.focus { background-color: #035F2C; border-color: #035F2C; box-shadow: 0 0 0 0.25rem rgba(4, 118, 55, 0.5); }
        .btn-success:active, .btn-success.active, .btn-success.show { background-color: #035F2C; border-color: #035F2C; }
        
        .btn-outline-success { color: #fff; background-color: rgba(4, 118, 55, 0.15); border-color: rgba(4, 118, 55, 0.35); }
        .btn-outline-success:hover { color: #fff; background-color: #047637; border-color: #047637; }
        .btn-outline-success:focus, .btn-outline-success.focus { box-shadow: 0 0 0 0.25rem rgba(4, 118, 55, 0.5); }
        .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.show { color: #fff; background-color: #047637; border-color: #047637; }
        
        .btn-outline-primary { color: #fff; background-color: rgba(15, 23, 42, 0.14); border-color: rgba(255, 255, 255, 0.25); }
        .btn-outline-primary:hover { color: #fff; background-color: #065f46; border-color: #065f46; }
        .btn-outline-primary:focus, .btn-outline-primary.focus { box-shadow: 0 0 0 0.25rem rgba(4, 118, 55, 0.5); }
        .btn-outline-primary:active, .btn-outline-primary.active, .btn-outline-primary.show { color: #fff; background-color: #065f46; border-color: #065f46; }
        
        .bg-success { background-color: #047637 !important; }
        .bg-success-subtle { background-color: #E8F6EE !important; }
        
        .text-success { --bs-text-opacity: 1; color: rgba(4, 118, 55, var(--bs-text-opacity)) !important; }
        .text-bg-success { background-color: #047637 !important; color: #fff !important; }
        
        .badge.bg-success { background-color: #047637 !important; }
        .badge.text-success { color: #047637 !important; }
        .badge.text-bg-success { background-color: #047637 !important; color: #fff !important; }
        .badge.bg-success-subtle { background-color: #E8F6EE !important; }
        .badge.text-bg-success-subtle { background-color: #E8F6EE !important; color: #047637 !important; }
        
        .alert-success { color: #047637; background-color: #E8F6EE; border-color: #D4F0E7; }
        .alert-success hr { border-top-color: #C4E8DD; }
        .alert-success .alert-link { color: #035F2C; }
        
        .progress-bar.bg-success { background-color: #047637 !important; }

        /* Update navbar brand color */
        .navbar-brand span { color: #d8ff5d; font-weight: 900; }
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
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}" href="{{ route('news.index') }}">Tin tức</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? '' : '' }}" href="{{ route('home') }}#contact">Liên hệ</a></li>
            </ul>

            @php
                $clientNotifications = [];
                $dbNotifications = collect();
                $unreadCount = 0;

                if(Auth::check()) {
                    $dbNotifications = \Illuminate\Support\Facades\DB::table('notifications')
                        ->where(function ($query) {
                            $query->where('user_id', Auth::id())
                                ->orWhereNull('user_id');
                        })
                        ->orderByDesc('created_at')
                        ->limit(5)
                        ->get();

                    $unreadCount = \Illuminate\Support\Facades\DB::table('notifications')
                        ->where(function ($query) {
                            $query->where('user_id', Auth::id())
                                ->orWhereNull('user_id');
                        })
                        ->where('is_read', false)
                        ->count();
                }

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
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2" style="min-width: 360px;">
                        <li class="dropdown-header">Thông báo</li>

                        @if(isset($dbNotifications) && $dbNotifications->isNotEmpty())
                            @foreach($dbNotifications as $n)
                                <li>
                                    <a class="dropdown-item d-flex align-items-start gap-3" href="{{ route('user.notifications.show', $n->id) }}">
                                        <span class="badge rounded-pill p-2 bg-success text-white">
                                            <i class="bi bi-envelope-open"></i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ $n->title }}</div>
                                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($n->content, 80) }}</div>
                                        </div>
                                        @if(!$n->is_read)
                                            <span class="badge bg-danger ms-2">Mới</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        @endif

                        @if(count($clientNotifications) > 0)
                            <li><hr class="dropdown-divider"></li>
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
                        @endif

                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center" href="{{ route('user.notifications.index') }}">Xem tất cả</a>
                        </li>
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
        <div class="hero-slides">
            <div class="hero-slide slide1 active"></div>
            <div class="hero-slide slide2"></div>
            <div class="hero-slide slide3"></div>
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
        <div class="container">
            <div class="hero-content">
                <div class="hero-text-wrap">
                    <span class="hero-badge">Đặt sân trực tuyến #1</span>
                    <h1 class="hero-title">Giới thiệu cơ sở sân bóng LFC</h1>
                    <p class="lead mb-4">
                        Tìm hiểu về LFC, dịch vụ đặt sân chuyên nghiệp và tiện ích tại cơ sở sân bóng của chúng tôi ở Hoài Đức, Hà Nội.
                    </p>
                    <div class="hero-meta">
                        <span class="hero-meta-item">Sân cỏ chất lượng cao</span>
                        <span class="hero-meta-item">Đặt sân nhanh chóng</span>
                        <span class="hero-meta-item">Hỗ trợ chuyên nghiệp</span>
                    </div>
                </div>
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
                    <li><i class="bi bi-clock text-success me-2"></i> 07:00 - 23:00</li>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slides = Array.from(document.querySelectorAll('.hero-slide'));
        if (!slides.length) return;

        let activeIndex = slides.findIndex(slide => slide.classList.contains('active'));
        if (activeIndex < 0) {
            activeIndex = 0;
            slides[0].classList.add('active');
        }

        setInterval(() => {
            slides[activeIndex].classList.remove('active');
            activeIndex = (activeIndex + 1) % slides.length;
            slides[activeIndex].classList.add('active');
        }, 6000);
    });
</script>

@stack('scripts')

</body>
</html>