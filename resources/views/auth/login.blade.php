<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập</title>
    <link href="/css/app.css" rel="stylesheet">
</head>
<body style="background:#f8fafc;">
    <div style="max-width:520px;margin:40px auto;padding:24px;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 10px 30px rgba(15,23,42,0.08);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <div>
                <div style="font-size:14px;color:#10b981;font-weight:700;margin-bottom:6px;display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:8px;height:8px;background:#10b981;border-radius:9999px;display:inline-block;"></span>
                    NGƯỜI DÙNG
                </div>
                <h1 style="margin:0;font-size:28px;color:#111827;">Đăng nhập tài khoản</h1>
            </div>
        </div>

        <div style="display:grid;gap:12px;margin-bottom:20px;">
            <button type="button" style="border:1px solid #d1d5db;border-radius:12px;padding:12px 16px;background:#fff;color:#111827;text-align:left;display:flex;gap:12px;align-items:center;cursor:pointer;">
                <span style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;background:#ffffff;border:1px solid #d1d5db;border-radius:8px;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.5 12.26c0-.82-.07-1.62-.21-2.4H12.24v4.55h5.97c-.26 1.4-1.03 2.59-2.2 3.38v2.8h3.55c2.08-1.92 3.29-4.73 3.29-8.33z" fill="#4285F4"/>
                        <path d="M12.24 23.99c2.97 0 5.47-.98 7.29-2.65l-3.55-2.8c-.98.66-2.23 1.05-3.74 1.05-2.87 0-5.3-1.94-6.17-4.55H2.37v2.87c1.8 3.56 5.55 6.08 9.87 6.08z" fill="#34A853"/>
                        <path d="M6.07 14.05a7.45 7.45 0 010-4.1V7.08H2.37a11.99 11.99 0 000 9.84l3.7-2.87z" fill="#FBBC05"/>
                        <path d="M12.24 4.47c1.62 0 3.08.56 4.22 1.66l3.16-3.16C17.7 1.17 15.2 0 12.24 0 7.92 0 4.17 2.52 2.37 6.08l3.7 2.87c.86-2.61 3.3-4.48 6.17-4.48z" fill="#EA4335"/>
                    </svg>
                </span>
                Đăng nhập bằng Google
            </button>
            <button type="button" style="border:1px solid #d1d5db;border-radius:12px;padding:12px 16px;background:#fff;color:#111827;text-align:left;display:flex;gap:12px;align-items:center;cursor:pointer;">
                <span style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;background:#1877f2;border-radius:8px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.99 3.65 9.13 8.44 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99C18.35 21.13 22 16.99 22 12z" fill="#fff"/>
                    </svg>
                </span>
                Đăng nhập bằng Facebook
            </button>
        </div>

        <div style="text-align:center;color:#6b7280;font-size:14px;margin-bottom:20px;">Hoặc đăng nhập bằng email</div>

        @if (session('success'))
            <div style="background:#f0fdf4;color:#15803d;padding:14px;border:1px solid #86efac;border-radius:12px;margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background:#fef2f2;color:#991b1b;padding:14px;border:1px solid #fca5a5;border-radius:12px;margin-bottom:20px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div style="margin-bottom:16px;">
                <label for="email" style="display:block;font-weight:600;color:#111827;margin-bottom:6px;">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:12px;background:#f8fafc;" placeholder="Nhập email">
            </div>

            <div style="margin-bottom:24px;">
                <label for="password" style="display:block;font-weight:600;color:#111827;margin-bottom:6px;">Mật khẩu</label>
                <input id="password" type="password" name="password" required style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:12px;background:#f8fafc;" placeholder="Nhập mật khẩu">
            </div>

            <button type="submit" style="width:100%;background:#2563eb;color:#fff;padding:14px;border-radius:14px;border:none;font-weight:700;">Đăng nhập</button>
        </form>

        <div style="text-align:center;color:#6b7280;font-size:13px;margin-top:20px;">Chưa có tài khoản? <a href="{{ route('register') }}" style="color:#2563eb;text-decoration:none;">Đăng ký</a></div>
    </div>
</body>
</html>
