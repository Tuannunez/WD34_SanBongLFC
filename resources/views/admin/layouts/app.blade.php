<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - SanBongLFC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: Arial, sans-serif;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 260px;
            background: #111827;
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            overflow-y: auto;
            z-index: 1000;
        }

        .admin-main {
            margin-left: 260px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        .admin-header {
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .content {
            padding: 24px;
        }

        .sidebar-brand {
            height: 64px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-logo {
            width: 32px;
            height: 32px;
            object-fit: contain;
            display: block;
        }

        .sidebar-menu {
            padding: 16px 12px;
        }

        .sidebar-title {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            margin: 18px 12px 8px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #d1d5db;
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 4px;
            font-size: 15px;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #2563eb;
            color: #fff;
        }

        .stat-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #2563eb;
            font-size: 24px;
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
        }
    </style>
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

</body>
</html>