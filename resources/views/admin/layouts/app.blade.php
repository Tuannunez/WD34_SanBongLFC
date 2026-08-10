<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title','Admin') - SanBongLFC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <style>
    :root {

        --sidebar: 270px;

        --primary: #047637;

        --success: #047637;

        --danger: #d63939;

        --warning: #F59E0B;

        --info: #4299e1;

        --bg: #FAFAFB;

        --dark: #031016;

        --border: #E5E7EB;

        --shadow: 0 12px 30px rgba(15, 23, 42, .06);

    }

    * {

        margin: 0;

        padding: 0;

        box-sizing: border-box;

    }

    html {

        scroll-behavior: smooth;

    }

    body {

        font-family: 'Inter', sans-serif;

        background: var(--bg);

        color: var(--dark);

        overflow-x: hidden;

    }

    .admin-wrapper {

        display: flex;

        min-height: 100vh;

    }

    .admin-sidebar {

        width: var(--sidebar);

        position: fixed;

        left: 0;

        top: 0;

        bottom: 0;

        background: linear-gradient(180deg, #111827, #0f172a);

        overflow-y: auto;

        z-index: 999;

        box-shadow: 6px 0 30px rgba(0, 0, 0, .18);

    }

    .admin-main {

        margin-left: var(--sidebar);

        width: calc(100% - var(--sidebar));

        display: flex;

        flex-direction: column;

        min-height: 100vh;

    }

    .admin-header {

        height: 76px;

        background: white;

        display: flex;

        align-items: center;

        justify-content: space-between;

        padding: 0 30px;

        border-bottom: 1px solid var(--border);

        position: sticky;

        top: 0;

        z-index: 100;

        backdrop-filter: blur(10px);

    }

    .content {

        padding: 30px;

    }

    .sidebar-brand {

        height: 76px;

        display: flex;

        align-items: center;

        padding: 0 24px;

        font-size: 22px;

        font-weight: 800;

        color: white;

        gap: 12px;

        border-bottom: 1px solid rgba(255, 255, 255, .08);

    }

    .sidebar-logo {

        width: 42px;

        height: 42px;

        border-radius: 12px;

        background: white;

        padding: 4px;

    }

    .sidebar-title {

        margin: 22px 20px 10px;

        font-size: 11px;

        letter-spacing: .08em;

        color: #94a3b8;

        text-transform: uppercase;

        font-weight: 700;

    }

    .sidebar-menu {

        padding: 16px;

    }

    .sidebar-link {

        display: flex;

        align-items: center;

        gap: 12px;

        padding: 13px 16px;

        color: #cbd5e1;

        text-decoration: none;

        border-radius: 14px;

        margin-bottom: 6px;

        transition: .25s;

        font-size: 15px;

        font-weight: 500;

    }

    .sidebar-link i {

        font-size: 18px;

        width: 24px;

        text-align: center;

    }

    .sidebar-link:hover {

        background: rgba(255, 255, 255, .08);

        color: white;

        transform: translateX(4px);

    }

    .sidebar-link.active {

        background: linear-gradient(135deg, #047637, #035F2C);

        color: white;

        box-shadow: 0 10px 25px rgba(4, 118, 55, .35);

    }

    .page-card {

        background: white;

        border-radius: 20px;

        border: none;

        box-shadow: var(--shadow);

    }

    .stat-card {

        border: none;

        border-radius: 22px;

        overflow: hidden;

        box-shadow: var(--shadow);

        transition: .3s;

    }

    .stat-card:hover {

        transform: translateY(-8px);

        box-shadow: 0 22px 45px rgba(0, 0, 0, .12);

    }

    .stat-icon {

        width: 68px;

        height: 68px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 20px;

        background: rgba(255, 255, 255, .18);

        color: white;

        font-size: 30px;

    }

    .stat-primary {

        background: linear-gradient(135deg, #047637, #035F2C);

        color: white;

    }

    .stat-success {

        background: linear-gradient(135deg, #047637, #009E4F);

        color: white;

    }

    .stat-warning {

        background: linear-gradient(135deg, #f59f00, #fcc419);

        color: white;

    }

    .stat-danger {

        background: linear-gradient(135deg, #d63939, #fa5252);

        color: white;

    }

    .stat-info {

        background: linear-gradient(135deg, #1098ad, #22b8cf);

        color: white;

    }

    .stat-card h2 {

        font-size: 34px;

        font-weight: 800;

        margin: 10px 0;

    }

    .stat-card .text-muted {

        color: rgba(255, 255, 255, .75) !important;

    }

    .stat-card small {

        opacity: .9;

    }

    .table {

        margin-bottom: 0;

    }

    .table thead th {

        background: #f8fafc;

        border-bottom: none;

        padding: 16px;

        font-size: 13px;

        color: #64748b;

        text-transform: uppercase;

    }

    .table tbody td {

        padding: 16px;

        vertical-align: middle;

    }

    .table tbody tr {

        transition: .25s;

    }

    .table tbody tr:hover {

        background: #f8fbff;

    }

    .badge {

        padding: 8px 14px;

        border-radius: 50px;

        font-weight: 600;

    }

    .admin-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #047637, #035F2C);
        color: #fff;
        font-weight: 700;
    }

    .sidebar-link[data-bs-toggle] {
        font-weight: 700;
    }

    .collapse {
        transition: all .3s ease;
    }

    .collapse .sidebar-link {
        font-size: 14px;
        padding-left: 52px !important;
        opacity: .92;
    }

    .collapse .sidebar-link:hover {
        padding-left: 60px !important;
    }

    .sidebar-link .bi-chevron-down {
        font-size: 12px;
        transition: transform .25s ease;
    }

    .sidebar-link[aria-expanded="true"] .bi-chevron-down {
        transform: rotate(180deg);
    }

    .ps-5 {
        padding-left: 3.2rem !important;
    }

    .sidebar-link[data-bs-toggle] {
        font-weight: 700;
    }

    .collapse .sidebar-link {
        font-size: 14px;
        padding-left: 52px !important;
    }

    .collapse .sidebar-link:hover {
        padding-left: 60px !important;
    }

    .collapse {
        transition: all .3s ease;
    }

    .sidebar-link .bi-chevron-down {
        transition: transform .25s ease;
    }

    .sidebar-link[aria-expanded="true"] .bi-chevron-down {
        transform: rotate(180deg);
    }

    .dashboard-title {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .dashboard-subtitle {
        color: #64748b;
        margin-bottom: 24px;
    }

    .card-header {
        background: #fff;
        border: none;
        padding: 20px 24px;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .card-body {
        padding: 24px;
    }

    .table thead th {
        background: #f8fafc;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }

    .table tbody td {
        font-size: 14px;
    }

    .status-card .item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #edf2f7;
    }

    .status-card .item:last-child {
        border: none;
    }

    .status-card .number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-weight: 700;
    }

    .dashboard-chart {
        height: 320px;
        border-radius: 18px;
        background: linear-gradient(135deg, #ffffff, #f8fbff);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        font-size: 20px;
        font-weight: 600;
    }

    .dashboard-table {
        border-radius: 18px;
        overflow: hidden;
    }

    .quick-action {
        border-radius: 16px;
        border: none;
        transition: .25s;
    }

    .quick-action:hover {
        transform: translateY(-4px);
    }

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
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('show');
        });
    }
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        const sidebar = document.getElementById('adminSidebar');

        if (!sidebar) return;

        // Khôi phục vị trí cuộn
        const savedScroll = localStorage.getItem('sidebarScroll');

        if (savedScroll !== null) {
            sidebar.scrollTop = parseInt(savedScroll);
        }

        // Lưu vị trí cuộn
        sidebar.addEventListener('scroll', function() {
            localStorage.setItem('sidebarScroll', sidebar.scrollTop);
        });

    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

     @stack('scripts')
</body>

</html>