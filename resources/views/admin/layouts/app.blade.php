<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - SanBongLFC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 270px;
            --primary: #2563eb;
            --dark: #0f172a;
            --muted: #64748b;
            --border: #e5e7eb;
            --body-bg: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--body-bg);
            font-family: Arial, sans-serif;
            color: #0f172a;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #0f172a 0%, #111827 55%, #020617 100%);
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 10px 0 30px rgba(15, 23, 42, .12);
        }

        .admin-main {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
        }

        .admin-header {
            min-height: 72px;
            background: rgba(255, 255, 255, .92);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 900;
            backdrop-filter: blur(10px);
        }

        .content {
            padding: 26px;
        }

        .sidebar-brand {
            min-height: 72px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 22px;
            font-size: 20px;
            font-weight: 800;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-logo {
            width: 38px;
            height: 38px;
            object-fit: contain;
            border-radius: 12px;
            background: #fff;
            padding: 4px;
        }

        .sidebar-menu {
            padding: 18px 14px 24px;
        }

        .sidebar-title {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin: 22px 12px 9px;
            font-weight: 700;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cbd5e1;
            text-decoration: none;
            padding: 11px 13px;
            border-radius: 14px;
            margin-bottom: 5px;
            font-size: 15px;
            transition: all .18s ease;
        }

        .sidebar-link i {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,.09);
            color: #fff;
            transform: translateX(3px);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            box-shadow: 0 10px 24px rgba(37, 99, 235, .28);
        }

        .page-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
        }

        .stat-card {
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
            transition: all .18s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 36px rgba(15, 23, 42, .1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: var(--primary);
            font-size: 24px;
        }

        .admin-search .input-group-text,
        .admin-search .form-control {
            border-color: var(--border);
            background: #f8fafc;
        }

        .admin-search .input-group-text {
            border-radius: 999px 0 0 999px;
        }

        .admin-search .form-control {
            border-radius: 0 999px 999px 0;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
        }

        .table {
            vertical-align: middle;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .03em;
            border-bottom: 1px solid var(--border);
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .btn {
            border-radius: 12px;
        }

        .badge {
            border-radius: 999px;
            padding: 7px 10px;
        }

        @media (max-width: 991px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: .25s;
            }

            .admin-sidebar.show {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
                width: 100%;
            }

            .content {
                padding: 18px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="admin-wrapper">
    @include('admin.layouts.sidebar')

    <main class="admin-main">
        @include('admin.layouts.header')

        <div class="content">
            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminSidebar = document.getElementById('adminSidebar');

    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function () {
            adminSidebar.classList.toggle('show');
        });
    }
</script>

@stack('scripts')
</body>
</html>