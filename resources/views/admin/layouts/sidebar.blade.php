<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="SanBongLFC" class="sidebar-logo" />
        SanBongLFC
    </div>

    <div class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>

        <div class="sidebar-title">Quản lý hệ thống</div>

        <a href="{{ url('/admin/roles') }}" class="sidebar-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock"></i>
            Vai trò
        </a>

        <a href="{{ url('/admin/users') }}" class="sidebar-link {{ request()->is('admin/users*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            Người dùng
        </a>

        <div class="sidebar-title">Quản lý sân bóng</div>

        <a href="{{ url('admin/stadiums') }}" class="sidebar-link {{ request()->is('admin/stadiums*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            Cơ sở sân bóng
        </a>

        <a href="{{ url('/admin/field-types') }}" class="sidebar-link {{ request()->is('admin/field-types*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i>
            Loại sân
        </a>

        <a href="{{ url('/admin/fields') }}" class="sidebar-link {{ request()->is('admin/fields*') ? 'active' : '' }}">
            <i class="bi bi-map"></i>
            Sân bóng
        </a>

        <a href="{{ url('/admin/field-images') }}" class="sidebar-link {{ request()->is('admin/field-images*') ? 'active' : '' }}">
            <i class="bi bi-images"></i>
            Hình ảnh sân
        </a>

        <a href="{{ url('/admin/time-slots') }}" class="sidebar-link {{ request()->is('admin/time-slots*') ? 'active' : '' }}">
            <i class="bi bi-clock"></i>
            Khung giờ
        </a>

        <div class="sidebar-title">Đặt sân</div>

        <a href="{{ url('/admin/bookings') }}" class="sidebar-link {{ request()->is('admin/bookings*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i>
            Đơn đặt sân
        </a>

        <a href="{{ url('/admin/booking-details') }}" class="sidebar-link {{ request()->is('admin/booking-details*') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i>
            Chi tiết đặt sân
        </a>

        <a href="{{ url('/admin/services') }}" class="sidebar-link {{ request()->is('admin/services*') ? 'active' : '' }}">
            <i class="bi bi-cup-straw"></i>
            Dịch vụ
        </a>

        <a href="{{ url('/admin/booking-services') }}" class="sidebar-link {{ request()->is('admin/booking-services*') ? 'active' : '' }}">
            <i class="bi bi-bag-check"></i>
            Dịch vụ đặt sân
        </a>

        <div class="sidebar-title">Thanh toán</div>

        <a href="{{ url('/admin/payment-methods') }}" class="sidebar-link {{ request()->is('admin/payment-methods*') ? 'active' : '' }}">
            <i class="bi bi-credit-card"></i>
            Phương thức thanh toán
        </a>

        <a href="{{ url('/admin/payments') }}" class="sidebar-link {{ request()->is('admin/payments*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i>
            Thanh toán
        </a>

        <div class="sidebar-title">Marketing</div>

        <a href="{{ url('/admin/promotions') }}" class="sidebar-link {{ request()->is('admin/promotions*') ? 'active' : '' }}">
            <i class="bi bi-ticket-perforated"></i>
            Khuyến mãi
        </a>

        <a href="{{ url('/admin/reviews') }}" class="sidebar-link {{ request()->is('admin/reviews*') ? 'active' : '' }}">
            <i class="bi bi-star"></i>
            Đánh giá
        </a>

        <a href="{{ url('/admin/notifications') }}" class="sidebar-link {{ request()->is('admin/notifications*') ? 'active' : '' }}">
            <i class="bi bi-bell"></i>
            Thông báo
        </a>
    </div>
</aside>