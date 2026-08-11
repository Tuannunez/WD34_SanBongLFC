<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SanBongLFC</title>

</head>
<body>
    <header>
        <div class="logo">
            <img src="/images/logo.png" alt="SanBongLFC" style="height:44px; object-fit:contain; display:block;" />
            <span>SanBongLFC</span>
        </div>
        <nav class="header-menu">
            <a class="nav-link active" href="#">Trang chủ</a>
            <a class="nav-link" href="#about">Giới thiệu</a>
            <a class="nav-link" href="#news">Tin tức</a>
            <a class="nav-link" href="#services">Dịch vụ</a>
            <a class="nav-link" href="#contact">Liên hệ</a>
        </nav>
        <div class="header-actions">
            <button class="icon-btn">
                🔔
                <span class="badge">3</span>
            </button>
            <a href="#" class="btn btn-secondary">Đơn của tôi</a>
            <a href="#" class="btn btn-outline-blue">Quản trị</a>
            <button class="btn btn-white">Admin</button>
        </div>
    </header>

    <section class="banner">
        <div class="hero">
            <span class="hero-badge">Đặt sân trực tuyến #1</span>
            <div class="hero-info">
                <div class="info-item">Đặt sân 5 · 7 · 11 người</div>
                <div class="info-item">Kiểm tra lịch trống theo thời gian thực</div>
                <div class="info-item">Thanh toán an toàn · Xác nhận tức thì</div>
            </div>
            <div class="hero-features">
                <div class="feature-card">Sân đạt chuẩn</div>
                <div class="feature-card">Đặt trong 30 giây</div>
                <div class="feature-card">4.9/5 đánh giá</div>
                <div class="feature-card">Thanh toán an toàn</div>
            </div>
        </div>
    </section>

    <main class="container">
    @yield('content')
</main>

    <footer>
        <div class="footer-top">
            <div class="footer-col">
                <div class="footer-logo">
                    <div class="footer-logo-icon"> <img src="/images/logo.png" alt="SanBongLFC" style="height:44px; object-fit:contain; display:block;" /></div>
                    SanBongLFC
                </div>
                <p>SanBongLFC là nền tảng đặt sân bóng nhanh chóng, tiện lợi và an toàn cho người dùng trên toàn quốc.</p>
            </div>
            <div class="footer-col">
                <h3>Liên kết nhanh</h3>
                <ul class="footer-list">
                    <li><a href="/">Trang chủ</a></li>
                    <li><a href="#">Sân bóng</a></li>
                    <li><a href="#">Đặt sân</a></li>
                    <li><a href="#">Tin tức</a></li>
                    <li><a href="#">Liên hệ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Thông tin liên hệ</h3>
                <div class="footer-contact">
                    <div class="contact-item"><span>📍</span><div>Địa chỉ: 123 Đường Sân Bóng, Hoài Đức, BTL, Hà Nội</div></div>
                    <div class="contact-item"><span>📞</span><div>Hotline: 1900 1234</div></div>
                    <div class="contact-item"><span>📧</span><div>Email: support@sanbonglfc.vn</div></div>
                    <div class="contact-item"><span>🕒</span><div>Giờ mở cửa: 07:00 - 23:00</div></div>
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
        <div class="footer-bottom">© 2026 LFC Football Webstite đặt sân tiện lợi</div>
    </footer>

    <script>
        function toggleAccountMenu() {
            const dropdown = document.getElementById('accountDropdown');
            dropdown.classList.toggle('active');
        }
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('accountDropdown');
            const button = document.querySelector('.account-button');
            if (!button.contains(event.target) && !menu.contains(event.target)) {
                menu.classList.remove('active');
            }
        });
    </script>
</body>
</html>
        a { text-decoration: none; color: inherit; }
        header { background: rgba(0,0,0,0.60); padding: 20px 30px; display: flex; align-items: center; justify-content: space-between; gap: 20px; box-shadow: 0 18px 50px rgba(0,0,0,0.22); backdrop-filter: blur(18px); position: sticky; top: 0; z-index: 20; }
        .logo { display: flex; align-items: center; gap: 12px; font-weight: 800; color: #fff; }
        .logo span { color: #47d47f; font-size: 20px; }
        .header-menu { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; }
        .header-menu .nav-link { color: rgba(255,255,255,0.80); font-weight: 600; padding: 8px 10px; border-radius: 999px; transition: color .2s ease; }
        .header-menu .nav-link.active { color: #fff; position: relative; }
        .header-menu .nav-link.active::after { content: ''; position: absolute; left: 0; right: 0; bottom: -8px; height: 3px; border-radius: 999px; background: #22c55e; }
        .header-menu .nav-link:hover { color: #fff; }
        .header-actions { display: flex; align-items: center; gap: 12px; }
        .header-actions .btn { color: #fff; }
        .icon-btn { width: 48px; height: 48px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.18); background: rgba(255,255,255,0.07); color: #fff; display: inline-flex; align-items: center; justify-content: center; position: relative; cursor: pointer; }
        .icon-btn .badge { position: absolute; top: 8px; right: 8px; min-width: 18px; height: 18px; border-radius: 999px; background: #22c55e; color: #000; font-size: 11px; display: inline-flex; align-items: center; justify-content: center; padding: 0 4px; font-weight: 700; }
        .btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; border: 1px solid transparent; padding: 12px 20px; font-weight: 700; cursor: pointer; transition: transform .2s ease, box-shadow .2s ease; text-decoration: none; }
        .btn-primary { background: #047637; color: #fff; border-color: #047637; }
        .btn-secondary { background: #22c55e; color: #fff; border-color: #22c55e; box-shadow: 0 12px 30px rgba(34, 197, 94, 0.18); }
        .btn-secondary:hover { background: #16a34a; color: #fff; }
        .btn-outline-blue { background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(255,255,255,0.25); }
        .btn-outline-blue:hover { background: rgba(255,255,255,0.18); color: #fff; }
        .btn-white { background: #fff; color: #111827; border-color: transparent; }
        .banner { background: url('/images/banner1.png') no-repeat center/cover; filter: brightness(1.05); color: #fff; padding: 96px 30px 64px; overflow: hidden; position: relative; }
        .banner::after { display: none; }
        .hero { max-width: 760px; margin: 0; position: relative; z-index: 1; text-align: left; }
        .hero-badge { display: inline-flex; align-items: center; gap: 10px; padding: 10px 18px; border-radius: 999px; background: rgba(255,255,255,0.10); border: 1px solid rgba(255,255,255,0.18); color: #e7f7ee; font-size: 13px; margin-bottom: 26px; }
        .hero-title { font-size: clamp(3rem, 4vw, 4.5rem); line-height: 1.02; margin-bottom: 24px; font-weight: 900; }
        .hero-title .highlight { color: #22c55e; }
        .hero-info { display: grid; gap: 16px; margin-bottom: 40px; }
        .info-item { display: inline-flex; align-items: center; gap: 14px; color: rgba(255,255,255,0.92); font-size: 15px; }
        .info-item::before { content: '•'; display:inline-block; color:#22c55e; font-size:20px; line-height:1; width:14px; }
        .hero-features { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .feature-card { padding: 18px 20px; border-radius: 18px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.14); color: #fff; text-align:center; font-weight:700; min-height: 96px; display:flex; align-items:center; justify-content:center; }
        .feature-card:nth-child(2) { background: rgba(16, 185, 129, 0.16); }
        .feature-card:nth-child(3) { background: rgba(250, 204, 21, 0.18); color:#fff; }
        .feature-card:hover { transform: translateY(-2px); }
        .card { background: rgba(3,16,22,0.65); border: 1px solid rgba(255,255,255,0.25); border-radius: 24px; padding: 24px; width: min(640px,100%); backdrop-filter: blur(18px); }
        .form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .form-grid .field { display: flex; flex-direction: column; gap: 8px; }
        .form-grid input, .form-grid select { width: 100%; border: none; border-radius: 16px; padding: 14px 16px; background: rgba(255,255,255,0.92); font-size: 14px; }
        .btn-search { width: 100%; background: #047637; color: #fff; padding: 14px 18px; border-radius: 16px; border: none; font-size: 15px; }
        .stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 40px; }
        .stat { background: rgba(3,16,22,0.3); border: 1px solid rgba(255,255,255,0.14); border-radius: 18px; padding: 24px; text-align: center; }
        .stat strong { display: block; font-size: 2rem; margin-bottom: 8px; }
        .content { max-width: 1180px; margin: 0 auto; padding: 40px 24px 80px; }
        .section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; gap: 12px; }
        .section-head h2 { margin: 0; font-size: 28px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
        .pitch-card { background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 12px 30px rgba(15,23,42,0.08); transition: transform 0.2s; }
        .pitch-card:hover { transform: translateY(-4px); }
        .pitch-thumb { height: 180px; background: linear-gradient(135deg, #E8F6EE 0%, #A7F3D0 100%); display: flex; align-items: center; justify-content: center; font-size: 40px; }
        .pitch-body { padding: 20px; }
        .pitch-title { font-weight: 700; margin-bottom: 8px; }
        .pitch-meta { color: #667085; font-size: 13px; margin-bottom: 16px; }
        .pitch-footer { display: flex; justify-content: space-between; color: #667085; font-size: 13px; }
        .footer-top { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px; background: #031016; color: #e2e8f0; padding: 60px 24px 40px; }
        .footer-col { display: flex; flex-direction: column; gap: 16px; }
        .footer-col h3 { margin: 0; font-size: 18px; color: #fff; }
        .footer-col p, .footer-col a, .footer-col .contact-item { font-size: 14px; line-height: 1.7; color: #cbd5e1; }
        .footer-col a { color: #cbd5e1; }
        .footer-col a:hover { color: #ffffff; }
        .footer-logo { display: inline-flex; align-items: center; gap: 10px; font-weight: 700; color: #009E4F; font-size: 18px; }
        .footer-logo-icon { display: inline-flex; align-items: center; justify-content: center; }
        .footer-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
       
        .footer-list li::marker { color: #047637; }
        .footer-contact { display: grid; gap: 10px; }
        .footer-contact .contact-item { display: flex; align-items: flex-start; gap: 10px; }
        .footer-contact .contact-item span { min-width: 22px; display: inline-flex; justify-content: center; color: #009E4F; }
        .footer-social { display: grid; gap: 10px; }
        .footer-social a { display: inline-block; padding: 10px 12px; border-radius: 12px; background: rgba(255,255,255,0.05); }
        .footer-bottom { background: #020617; color: #94a3b8; text-align: center; padding: 18px 24px; font-size: 14px; }
        @media (max-width: 900px) { .search { margin: 16px 0; } .header-actions { gap: 8px; } .form-grid { grid-template-columns: 1fr; } .footer-top { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { header { flex-wrap: wrap; gap: 12px; } .banner { padding: 40px 18px 40px; } .hero-title { font-size: 2.5rem; } .footer-top { grid-template-columns: 1fr; } }
        
        /* Bootstrap Success Color Override */
        .btn-success { background-color: #047637; border-color: #047637; }
        .btn-success:hover { background-color: #035F2C; border-color: #035F2C; }
        .btn-success.disabled, .btn-success:disabled { background-color: #047637; border-color: #047637; }
        .btn-success:focus, .btn-success.focus { background-color: #035F2C; border-color: #035F2C; box-shadow: 0 0 0 0.25rem rgba(4, 118, 55, 0.5); }
        .btn-success:active, .btn-success.active, .btn-success.show { background-color: #035F2C; border-color: #035F2C; }
        
        .btn-outline-success { color: #047637; border-color: #047637; }
        .btn-outline-success:hover { color: #fff; background-color: #047637; border-color: #047637; }
        .btn-outline-success:focus, .btn-outline-success.focus { box-shadow: 0 0 0 0.25rem rgba(4, 118, 55, 0.5); }
        .btn-outline-success:active, .btn-outline-success.active, .btn-outline-success.show { color: #fff; background-color: #047637; border-color: #047637; }
        
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
    </style>