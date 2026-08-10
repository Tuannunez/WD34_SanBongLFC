@php
    $bookingOpen = request()->routeIs(
        'admin.bookings.*',
        'admin.booking-services.*',
        'admin.booking-details.*'
    );

    $systemOpen = request()->routeIs(
        'admin.roles.*',
        'admin.users.*'
    );

    $fieldOpen = request()->routeIs(
        'admin.stadiums.*',
        'admin.field-types.*',
        'admin.fields.*',
        'admin.time-slots.*'
    );

    $paymentOpen = request()->is(
        'admin/payment-methods*',
        'admin/payments*'
    );

    $marketingOpen = request()->routeIs(
        'admin.promotions.*',
        'admin.news.*',
        'admin.reviews.*',
        'admin.notifications.*'
    );
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand text-decoration-none">
        <img
            src="{{ asset('images/logo.png') }}"
            alt="SanBongLFC"
            class="sidebar-logo"
        >

        <span>SanBongLFC</span>
    </a>

    <div class="sidebar-menu">
        <div class="sidebar-title">Tổng quan</div>

        <a
            href="{{ route('admin.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        >
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <div class="sidebar-title">Quản lý đơn đặt sân</div>

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

            <a href="{{ url('/admin/time-slots/4') }}"
                class="sidebar-link ps-5 {{ request()->is('admin/time-slots/4*') ? 'active':'' }}">
                <i class="bi bi-clock"></i>
                Khung giờ
            </a>

            <a href="{{ route('admin.bookings.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.bookings.*') ? 'active':'' }}">
                <i class="bi bi-calendar-check"></i>
                Đơn đặt sân
            </a>
        </div>

        <div class="sidebar-title">
            Dịch vụ
        </div>

        <a class="sidebar-link" data-bs-toggle="collapse" href="#serviceMenu">
            <i class="bi bi-box-seam"></i>
            <span class="flex-grow-1">
                Quản lý dịch vụ
            </span>
            <i class="bi bi-chevron-down"></i>
        </a>

        <div class="collapse show" id="serviceMenu">
            <a href="{{ route('admin.services.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                Quản lý dịch vụ
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

        <div class="sidebar-title">Quản lý hệ thống</div>

        <button
            type="button"
            class="sidebar-link w-100 border-0 {{ $systemOpen ? 'active' : '' }}"
            data-bs-toggle="collapse"
            data-bs-target="#systemMenu"
            aria-expanded="{{ $systemOpen ? 'true' : 'false' }}"
            aria-controls="systemMenu"
        >
            <i class="bi bi-gear-fill"></i>

            <span class="flex-grow-1 text-start">Hệ thống</span>

            <i class="bi bi-chevron-down small"></i>
        </button>

        <div
            class="collapse {{ $systemOpen ? 'show' : '' }}"
            id="systemMenu"
        >
            <a
                href="{{ route('admin.roles.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
            >
                <i class="bi bi-shield-lock"></i>
                <span>Vai trò</span>
            </a>

            <a
                href="{{ route('admin.users.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            >
                <i class="bi bi-people"></i>
                <span>Người dùng</span>
            </a>
        </div>

        <div class="sidebar-title">Quản lý sân</div>

        <button
            type="button"
            class="sidebar-link w-100 border-0 {{ $fieldOpen ? 'active' : '' }}"
            data-bs-toggle="collapse"
            data-bs-target="#fieldMenu"
            aria-expanded="{{ $fieldOpen ? 'true' : 'false' }}"
            aria-controls="fieldMenu"
        >
            <i class="bi bi-building"></i>

            <span class="flex-grow-1 text-start">Sân bóng</span>

            <i class="bi bi-chevron-down small"></i>
        </button>

        <div
            class="collapse {{ $fieldOpen ? 'show' : '' }}"
            id="fieldMenu"
        >
            <a
                href="{{ route('admin.stadiums.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.stadiums.*') ? 'active' : '' }}"
            >
                <i class="bi bi-building"></i>
                <span>Cơ sở sân</span>
            </a>

            <a
                href="{{ route('admin.field-types.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.field-types.*') ? 'active' : '' }}"
            >
                <i class="bi bi-grid"></i>
                <span>Loại sân</span>
            </a>

            <a
                href="{{ route('admin.fields.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.fields.*') ? 'active' : '' }}"
            >
                <i class="bi bi-map"></i>
                <span>Sân bóng</span>
            </a>

            @if(\Illuminate\Support\Facades\Route::has('admin.field-images.index'))
                <a
                    href="{{ route('admin.field-images.index') }}"
                    class="sidebar-link ps-5 {{ request()->routeIs('admin.field-images.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-images"></i>
                    <span>Hình ảnh sân</span>
                </a>
            @endif
        </div>

        <div class="sidebar-title">Dịch vụ</div>

        <a
            href="{{ route('admin.services.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}"
        >
            <i class="bi bi-box-seam"></i>
            <span>Quản lý dịch vụ</span>
        </a>

        @if(
            \Illuminate\Support\Facades\Route::has('admin.payment-methods.index')
            || \Illuminate\Support\Facades\Route::has('admin.payments.index')
        )
            <div class="sidebar-title">Thanh toán</div>

            <button
                type="button"
                class="sidebar-link w-100 border-0 {{ $paymentOpen ? 'active' : '' }}"
                data-bs-toggle="collapse"
                data-bs-target="#paymentMenu"
                aria-expanded="{{ $paymentOpen ? 'true' : 'false' }}"
                aria-controls="paymentMenu"
            >
                <i class="bi bi-credit-card"></i>

                <span class="flex-grow-1 text-start">Thanh toán</span>

                <i class="bi bi-chevron-down small"></i>
            </button>

            <div
                class="collapse {{ $paymentOpen ? 'show' : '' }}"
                id="paymentMenu"
            >
                @if(\Illuminate\Support\Facades\Route::has('admin.payment-methods.index'))
                    <a
                        href="{{ route('admin.payment-methods.index') }}"
                        class="sidebar-link ps-5 {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}"
                    >
                        <i class="bi bi-wallet2"></i>
                        <span>Phương thức</span>
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('admin.payments.index'))
                    <a
                        href="{{ route('admin.payments.index') }}"
                        class="sidebar-link ps-5 {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"
                    >
                        <i class="bi bi-cash-coin"></i>
                        <span>Lịch sử thanh toán</span>
                    </a>
                @endif
            </div>
        @endif

        <div class="sidebar-title">Marketing</div>

        <button
            type="button"
            class="sidebar-link w-100 border-0 {{ $marketingOpen ? 'active' : '' }}"
            data-bs-toggle="collapse"
            data-bs-target="#marketingMenu"
            aria-expanded="{{ $marketingOpen ? 'true' : 'false' }}"
            aria-controls="marketingMenu"
        >
            <i class="bi bi-megaphone"></i>

            <span class="flex-grow-1 text-start">Marketing</span>

            <i class="bi bi-chevron-down small"></i>
        </button>

        <div
            class="collapse {{ $marketingOpen ? 'show' : '' }}"
            id="marketingMenu"
        >
            <a
                href="{{ route('admin.promotions.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}"
            >
                <i class="bi bi-ticket-perforated"></i>
                <span>Khuyến mãi</span>
            </a>

            @if(Auth::check() && Auth::user()->role === 'admin')
                <a
                    href="{{ route('admin.news.index') }}"
                    class="sidebar-link ps-5 {{ request()->routeIs('admin.news.*') ? 'active' : '' }}"
                >
                    <i class="bi bi-newspaper"></i>
                    <span>Tin tức</span>
                </a>
            @endif

            <a
                href="{{ route('admin.reviews.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"
            >
                <i class="bi bi-star"></i>
                <span>Đánh giá</span>
            </a>

            <a
                href="{{ route('admin.notifications.index') }}"
                class="sidebar-link ps-5 {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
            >
                <i class="bi bi-bell"></i>
                <span>Thông báo</span>
            </a>
        </div>

        <div class="sidebar-title">Điều hướng</div>

        <a
            href="{{ route('home') }}"
            class="sidebar-link"
            target="_blank"
            rel="noopener"
        >
            <i class="bi bi-box-arrow-up-right"></i>
            <span>Xem trang khách</span>
        </a>
    </div>
</aside>