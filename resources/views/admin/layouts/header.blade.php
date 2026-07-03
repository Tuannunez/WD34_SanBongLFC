<header class="admin-header d-flex align-items-center justify-content-between px-4">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div>
            <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
            <small class="text-muted">Quản trị hệ thống đặt sân SanBongLFC</small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="input-group admin-search d-none d-md-flex" style="width: 280px;">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Tìm kiếm nhanh...">
        </div>

        <a href="{{ url('/') }}" class="btn btn-outline-success d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-house-door-fill"></i>
            Về trang user
        </a>

        <button type="button" class="btn btn-light position-relative">
            <i class="bi bi-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                3
            </span>
        </button>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <span class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </span>
                <span class="d-none d-md-inline text-start">
                    <span class="d-block fw-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <small class="text-muted">Quản trị viên</small>
                </span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-person me-2"></i>
                        Tài khoản
                    </a>
                </li>

                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-gear me-2"></i>
                        Cài đặt
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-sm-none" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-2"></i>
                        Về trang user
                    </a>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a class="dropdown-item text-danger" href="#"
                       onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<form id="admin-logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
</form>