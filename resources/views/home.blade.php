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
            <span style="font-size:18px; font-weight:700; margin-left:10px; color:#16a34a;">SanBongLFC</span>
        </div>
        <div class="search">
            <input type="text" placeholder="Tìm kiếm sân theo tên, địa điểm...">
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary">🔔</button>
            <div class="account-menu">
                <div class="account-button" onclick="toggleAccountMenu()">
                    <span>💼</span>
                    <span>Tài khoản</span>
                </div>
                <div id="accountDropdown" class="dropdown">
                    @auth
                        <div style="padding: 10px 12px; color: #111827; font-weight: 600; border-bottom: 1px solid #eef2f7;">{{ Auth::user()->name }}</div>
                        <a href="#" onclick="document.getElementById('logout-form').submit(); return false;">Đăng xuất</a>
                        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
                    @else
                        <a href="{{ route('login') }}">Đăng nhập</a>
                        <a href="{{ route('register') }}">Đăng ký</a>
                    @endauth
                </div>
            </div>
            @auth
                <a href="{{ route('register') }}" class="btn btn-primary">Đặt sân ngay</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary">Đăng ký</a>
            @endauth
        </div>
    </header>

    <section class="banner">
        <div class="hero">
            <h1 class="hero-title">Đặt sân bóng <span class="highlight">nhanh chóng</span> & dễ dàng</h1>
            <p class="hero-text">Tìm sân, chọn giờ và thanh toán chỉ trong vài phút. Trải nghiệm đặt sân thông minh cùng SanBongLFC.</p>
            <div class="card">
                <div class="form-grid">
                    <div class="field">
                        <label>Tỉnh/Thành phố</label>
                        <select>
                            <option>Chọn tỉnh thành</option>
                            <option>Hà Nội</option>
                            <option>TP.HCM</option>
                            <option>Đà Nẵng</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Loại sân</label>
                        <select>
                            <option>Tất cả loại sân</option>
                            <option>Sân 5</option>
                            <option>Sân 7</option>
                            <option>Sân 11</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Ngày đặt</label>
                        <input type="date">
                    </div>
                </div>
                <button class="btn-search">🔍 Tìm sân ngay</button>
            </div>
            <div class="stats">
                <div class="stat"><strong>500+</strong> Sân bóng</div>
                <div class="stat"><strong>10K+</strong> Lượt đặt</div>
                <div class="stat"><strong>63</strong> Tỉnh thành</div>
            </div>
        </div>
    </section>

    <main class="content">
        <div class="section-head">
            <h2>Sân nổi bật</h2>
            <a class="see-more" href="#">Xem tất cả →</a>
        </div>
        <div class="cards">
            @for ($i = 1; $i <= 6; $i++)
                <div class="pitch-card">
                    <div class="pitch-thumb">⚽</div>
                    <div class="pitch-body">
                        <div class="pitch-title">Sân bóng {{ $i }}</div>
                        <div class="pitch-meta">Địa chỉ: Quận {{ chr(64 + $i) }}, TP.HCM</div>
                        <div class="pitch-footer">
                            <span>⭐ 4.{{ $i }}</span>
                            <span>{{ 5 + $i }} km</span>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
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
                    <div class="contact-item"><span>🕒</span><div>Giờ mở cửa: 07:00 - 22:00</div></div>
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
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; color: #111827; }
        a { text-decoration: none; color: inherit; }
        header { background: #fff; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 10px rgba(15, 23, 42, 0.08); position: sticky; top: 0; z-index: 10; }
        .logo { display: flex; align-items: center; gap: 10px; font-weight: 700; color: #16a34a; }
        .logo-icon { width: 36px; height: 36px; border-radius: 12px; background: linear-gradient(135deg, #16a34a, #22c55e); display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; }
        .search { flex: 1; max-width: 500px; margin: 0 24px; }
        .search input { width: 100%; border: 1px solid #d1d5db; border-radius: 12px; padding: 12px 16px; font-size: 14px; }
        .header-actions { display: flex; align-items: center; gap: 12px; position: relative; }
        .btn { display: inline-flex; align-items: center; justify-content: center; border-radius: 12px; border: 1px solid transparent; padding: 10px 16px; font-weight: 600; cursor: pointer; }
        .btn-primary { background: #16a34a; color: #fff; border-color: #16a34a; }
        .btn-secondary { background: #fff; color: #111827; border-color: #d1d5db; }
        .account-menu { position: relative; }
        .account-button { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 12px; border: 1px solid #d1d5db; background: #fff; cursor: pointer; }
        .dropdown { display: none; position: absolute; right: 0; top: 54px; width: 200px; background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; box-shadow: 0 12px 36px rgba(15,23,42,0.12); padding: 14px; }
        .dropdown.active { display: block; }
        .dropdown a { display: block; padding: 10px 12px; border-radius: 12px; color: #111827; transition: background 0.2s; }
        .dropdown a:hover { background: #f1f5f9; }
        .banner { background: linear-gradient(135deg, rgba(22,101,52,0.82), rgba(21,128,61,0.82)), url('/images/banner1.png') no-repeat center/cover; color: #fff; padding: 84px 24px 56px; overflow: hidden; position: relative; }
        .banner::after { content: ''; position: absolute; right: -120px; top: -30px; width: 520px; height: 520px; background: rgba(255,255,255,0.06); border-radius: 50%; }
        .hero { max-width: 1180px; margin: 0 auto; position: relative; z-index: 1; }
        .hero-title { font-size: clamp(3rem, 4vw, 4.5rem); line-height: 1.05; margin-bottom: 16px; font-weight: 800; }
        .hero-title .highlight { color: #a7f3d0; }
        .hero-text { max-width: 540px; margin-bottom: 32px; font-size: 16px; opacity: 0.92; }
        .card { background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 24px; width: min(640px,100%); backdrop-filter: blur(18px); }
        .form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .form-grid .field { display: flex; flex-direction: column; gap: 8px; }
        .form-grid input, .form-grid select { width: 100%; border: none; border-radius: 16px; padding: 14px 16px; background: rgba(255,255,255,0.92); font-size: 14px; }
        .btn-search { width: 100%; background: #16a34a; color: #fff; padding: 14px 18px; border-radius: 16px; border: none; font-size: 15px; }
        .stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-top: 40px; }
        .stat { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14); border-radius: 18px; padding: 24px; text-align: center; }
        .stat strong { display: block; font-size: 2rem; margin-bottom: 8px; }
        .content { max-width: 1180px; margin: 0 auto; padding: 40px 24px 80px; }
        .section-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; gap: 12px; }
        .section-head h2 { margin: 0; font-size: 28px; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
        .pitch-card { background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 12px 30px rgba(15,23,42,0.08); transition: transform 0.2s; }
        .pitch-card:hover { transform: translateY(-4px); }
        .pitch-thumb { height: 180px; background: linear-gradient(135deg, #d1fae5 0%, #86efac 100%); display: flex; align-items: center; justify-content: center; font-size: 40px; }
        .pitch-body { padding: 20px; }
        .pitch-title { font-weight: 700; margin-bottom: 8px; }
        .pitch-meta { color: #475569; font-size: 13px; margin-bottom: 16px; }
        .pitch-footer { display: flex; justify-content: space-between; color: #475569; font-size: 13px; }
        .footer-top { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px; background: #0f172a; color: #e2e8f0; padding: 60px 24px 40px; }
        .footer-col { display: flex; flex-direction: column; gap: 16px; }
        .footer-col h3 { margin: 0; font-size: 18px; color: #fff; }
        .footer-col p, .footer-col a, .footer-col .contact-item { font-size: 14px; line-height: 1.7; color: #cbd5e1; }
        .footer-col a { color: #cbd5e1; }
        .footer-col a:hover { color: #ffffff; }
        .footer-logo { display: inline-flex; align-items: center; gap: 10px; font-weight: 700; color: #22c55e; font-size: 18px; }
        .footer-logo-icon { display: inline-flex; align-items: center; justify-content: center; }
        .footer-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
       
        .footer-list li::marker { color: #22c55e; }
        .footer-contact { display: grid; gap: 10px; }
        .footer-contact .contact-item { display: flex; align-items: flex-start; gap: 10px; }
        .footer-contact .contact-item span { min-width: 22px; display: inline-flex; justify-content: center; color: #22c55e; }
        .footer-social { display: grid; gap: 10px; }
        .footer-social a { display: inline-block; padding: 10px 12px; border-radius: 12px; background: rgba(255,255,255,0.05); }
        .footer-bottom { background: #020617; color: #94a3b8; text-align: center; padding: 18px 24px; font-size: 14px; }
        @media (max-width: 900px) { .search { margin: 16px 0; } .header-actions { gap: 8px; } .form-grid { grid-template-columns: 1fr; } .footer-top { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { header { flex-wrap: wrap; gap: 12px; } .banner { padding: 40px 18px 40px; } .hero-title { font-size: 2.5rem; } .footer-top { grid-template-columns: 1fr; } }
    </style>