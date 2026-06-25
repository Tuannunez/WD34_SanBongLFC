<header class="admin-header d-flex align-items-center justify-content-between px-4">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>

        <div>
            <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            <small class="text-muted">Trang quản trị hệ thống sân bóng</small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="input-group d-none d-md-flex" style="width: 280px;">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>
                Admin
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
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
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="#">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>