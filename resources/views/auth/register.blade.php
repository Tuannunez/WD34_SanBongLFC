<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký - SanBongLFC</title>

    <link href="/css/app.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --green: #16a34a;
            --green-dark: #15803d;
            --blue: #2563eb;
            --dark: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --soft: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, .18), transparent 32%),
                radial-gradient(circle at bottom left, rgba(34, 197, 94, .20), transparent 34%),
                linear-gradient(135deg, #eff6ff 0%, #f8fafc 45%, #ecfdf5 100%);
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-box {
            width: 100%;
            max-width: 560px;
            background: #fff;
            border-radius: 34px;
            padding: 44px 38px 36px;
            position: relative;
            border: 1px solid rgba(229, 231, 235, .9);
            box-shadow: 0 30px 80px rgba(15, 23, 42, .14);
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 18px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 0;
            background: #f3f4f6;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 20px;
            transition: .2s;
        }

        .close-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .auth-logo {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            background: linear-gradient(135deg, #2563eb, #16a34a);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            font-size: 32px;
            box-shadow: 0 16px 32px rgba(37, 99, 235, .22);
        }

        .auth-title {
            text-align: center;
            font-size: 34px;
            line-height: 1.2;
            font-weight: 900;
            color: var(--dark);
            margin: 0 0 8px;
        }

        .auth-subtitle {
            text-align: center;
            color: #374151;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 20px;
            z-index: 3;
        }

        .auth-input {
            width: 100%;
            height: 60px;
            border-radius: 17px;
            border: 1px solid var(--border);
            background: var(--soft);
            padding: 0 52px;
            font-size: 17px;
            color: var(--dark);
            outline: none;
            transition: .2s;
        }

        .auth-input::placeholder {
            color: #9ca3af;
        }

        .auth-input:focus {
            background: #fff;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #9ca3af;
            font-size: 18px;
            z-index: 4;
        }

        .btn-auth {
            width: 100%;
            height: 60px;
            border: 0;
            border-radius: 17px;
            background: linear-gradient(135deg, #2563eb, #16a34a);
            color: #fff;
            font-weight: 900;
            font-size: 19px;
            margin-top: 4px;
            transition: .2s;
            box-shadow: 0 16px 30px rgba(37, 99, 235, .20);
        }

        .btn-auth:hover {
            opacity: .95;
            transform: translateY(-1px);
        }

        .bottom-text {
            margin-top: 26px;
            text-align: center;
            color: #374151;
            font-size: 17px;
        }

        .bottom-text a {
            color: var(--blue);
            font-weight: 900;
            text-decoration: none;
        }

        .alert {
            border-radius: 16px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .rule-text {
            text-align: center;
            color: #6b7280;
            font-size: 13px;
            margin-top: 16px;
            line-height: 1.5;
        }

        @media (max-width: 576px) {
            .auth-page {
                padding: 14px;
            }

            .auth-box {
                border-radius: 28px;
                padding: 40px 22px 30px;
            }

            .auth-title {
                font-size: 30px;
            }

            .auth-subtitle {
                font-size: 16px;
            }

            .auth-input {
                height: 56px;
            }
        }
    </style>
</head>

<body>
<div class="auth-page">
    <div class="auth-box">
        <a href="{{ url('/') }}" class="close-btn" title="Quay về trang chủ">
            <i class="bi bi-x-lg"></i>
        </a>

        <div class="auth-logo">
            <i class="bi bi-person-plus-fill"></i>
        </div>

        <h1 class="auth-title">Đăng ký</h1>
        <p class="auth-subtitle">Tạo tài khoản để đặt sân dễ dàng hơn</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-bold mb-1">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Thông tin đăng ký chưa hợp lệ
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <div class="form-group">
                <div class="input-wrap">
                    <i class="bi bi-person input-icon"></i>
                    <input id="name"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="auth-input @error('name') is-invalid @enderror"
                           placeholder="Họ và tên"
                           required
                           autofocus>
                </div>
                @error('name')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="auth-input @error('email') is-invalid @enderror"
                           placeholder="Email"
                           required>
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            </div>

            <div class="form-group">
                <div class="input-wrap">
                    <i class="bi bi-shield-lock-fill input-icon"></i>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           class="auth-input @error('password_confirmation') is-invalid @enderror"
                           placeholder="Xác nhận mật khẩu"
                           required>

                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-auth">
                <i class="bi bi-check-circle me-2"></i>
                Đăng ký ngay
            </button>
        </form>

        <div class="rule-text">
            Bằng việc đăng ký, bạn có thể đặt sân và theo dõi lịch sử đặt sân tại SanBongLFC.
        </div>

        <div class="bottom-text">
            Đã có tài khoản?
            <a href="{{ route('login') }}">Đăng nhập</a>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
</body>
</html>