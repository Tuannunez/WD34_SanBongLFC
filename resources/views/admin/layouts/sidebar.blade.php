<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="SanBongLFC" class="sidebar-logo">
        <span>SanBongLFC</span>
    </div>

    <div class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}"
            class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <div class="sidebar-title">
            Quản lý hệ thống
        </div>

        <a class="sidebar-link" data-bs-toggle="collapse" href="#systemMenu" role="button">

            <i class="bi bi-gear-fill"></i>

            <span class="flex-grow-1">
                Hệ thống
            </span>

            <i class="bi bi-chevron-down small"></i>

        </a>

        <div class="collapse show" id="systemMenu">

            <a href="{{ url('/admin/roles') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/roles*') ? 'active' : '' }}">

                <i class="bi bi-shield-lock"></i>

                Vai trò

            </a>

            <a href="{{ url('/admin/users') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/users*') ? 'active' : '' }}">

                <i class="bi bi-people"></i>

                Người dùng

            </a>

        </div>

        <div class="sidebar-title">

            Quản lý sân

        </div>

        <a class="sidebar-link" data-bs-toggle="collapse" href="#fieldMenu">

            <i class="bi bi-building"></i>

            <span class="flex-grow-1">

                Sân bóng

            </span>

            <i class="bi bi-chevron-down"></i>

        </a>

        <div class="collapse show" id="fieldMenu">

            <a href="{{ route('admin.stadiums.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.stadiums.*') ? 'active':'' }}">

                <i class="bi bi-building"></i>

                Cơ sở

            </a>

            <a href="{{ url('/admin/field-types') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/field-types*') ? 'active':'' }}">

                <i class="bi bi-grid"></i>

                Loại sân

            </a>

            <a href="{{ url('/admin/fields') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/fields*') ? 'active':'' }}">

                <i class="bi bi-map"></i>

                Sân bóng

            </a>

            <a href="{{ url('/admin/field-images') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/field-images*') ? 'active':'' }}">

                <i class="bi bi-images"></i>

                Hình ảnh

            </a>

            <a href="{{ url('/admin/time-slots') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/time-slots*') ? 'active':'' }}">

                <i class="bi bi-clock"></i>

                Khung giờ

            </a>

        </div>

        <div class="sidebar-title">
            Thanh toán
        </div>

        <a class="sidebar-link" data-bs-toggle="collapse" href="#paymentMenu">

            <i class="bi bi-credit-card"></i>

            <span class="flex-grow-1">
                Thanh toán
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>

        <div class="collapse show" id="paymentMenu">

            <a href="{{ url('/admin/payment-methods') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/payment-methods*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                Phương thức
            </a>

            <a href="{{ url('/admin/payments') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/payments*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i>
                Lịch sử thanh toán
            </a>

        </div>

        <div class="sidebar-title">
            Thanh toán
        </div>

        <a class="sidebar-link" data-bs-toggle="collapse" href="#paymentMenu">

            <i class="bi bi-credit-card"></i>

            <span class="flex-grow-1">
                Thanh toán
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>

        <div class="collapse show" id="paymentMenu">

            <a href="{{ url('/admin/payment-methods') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/payment-methods*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                Phương thức
            </a>

            <a href="{{ url('/admin/payments') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/payments*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i>
                Lịch sử thanh toán
            </a>

        </div>


        <div class="sidebar-title">
            Marketing
        </div>

        <a class="sidebar-link" data-bs-toggle="collapse" href="#marketingMenu">

            <i class="bi bi-megaphone"></i>

            <span class="flex-grow-1">
                Marketing
            </span>

            <i class="bi bi-chevron-down"></i>

        </a>

        <div class="collapse show" id="marketingMenu">

            <a href="{{ url('/admin/promotions') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/promotions*') ? 'active' : '' }}">
                <i class="bi bi-ticket-perforated"></i>
                Khuyến mãi
            </a>

            @if(Auth::check() && Auth::user()->role==='admin')

            <a href="{{ url('/admin/news') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/news*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i>
                Tin tức
            </a>

            @endif

            <a href="{{ url('/admin/reviews') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/reviews*') ? 'active' : '' }}">
                <i class="bi bi-star"></i>
                Đánh giá
            </a>

            <a href="{{ url('/admin/notifications') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/notifications*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i>
                Thông báo
            </a>

        </div>
    </div>
</aside>