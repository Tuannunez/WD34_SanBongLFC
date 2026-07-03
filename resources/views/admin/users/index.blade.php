@extends('admin.layouts.app')

@section('title', 'Người dùng')
@section('page-title', 'Quản lý người dùng')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý người dùng</h4>
            <div class="text-muted">Danh sách tài khoản đang sử dụng trong hệ thống SanBongLFC</div>
        </div>

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-person-plus me-1"></i>
            Tạo người dùng mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $userCollection = collect(method_exists($users, 'items') ? $users->items() : $users);
        $activeCount = $userCollection->where('status', 1)->count();
        $lockedCount = $userCollection->where('status', 0)->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Tổng người dùng</div>
                        <h4 class="fw-bold mb-0">
                            {{ method_exists($users, 'total') ? $users->total() : $users->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-person-check"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Đang hoạt động</div>
                        <h4 class="fw-bold mb-0">{{ $activeCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-person-lock"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Đang bị khóa</div>
                        <h4 class="fw-bold mb-0">{{ $lockedCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-card">
        <div class="card-header bg-white border-0 px-4 py-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-list-ul text-primary me-1"></i>
                        Danh sách người dùng
                    </h5>
                    <div class="text-muted small">Quản lý thông tin, vai trò và trạng thái tài khoản</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Người dùng</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-end" style="width: 220px;">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="fw-bold text-muted">
                                #{{ $user->id }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold"
                                         style="width: 44px; height: 44px;">
                                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                    </div>

                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <small class="text-muted">Tài khoản hệ thống</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="text-muted">{{ $user->email }}</span>
                            </td>

                            <td>
                                @if($user->phone)
                                    <span>{{ $user->phone }}</span>
                                @else
                                    <span class="text-muted">Chưa cập nhật</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    <i class="bi bi-person-badge me-1"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            <td>
                                @if($user->status)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Kích hoạt
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-lock me-1"></i>
                                        Đã khóa
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="text-muted">
                                    {{ $user->created_at?->format('d/m/Y H:i') }}
                                </span>
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Sửa người dùng">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <a href="{{ route('admin.users.toggle-status', $user) }}"
                                   class="btn btn-sm {{ $user->status ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                   title="{{ $user->status ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                    @if($user->status)
                                        <i class="bi bi-lock"></i>
                                    @else
                                        <i class="bi bi-unlock"></i>
                                    @endif
                                </a>

                                <form action="{{ route('admin.users.destroy', $user) }}"
                                      method="POST"
                                      class="d-inline-block"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa người dùng này không?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Xóa người dùng">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Chưa có người dùng nào trong hệ thống
                                </div>

                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Tạo người dùng đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($users, 'links'))
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection