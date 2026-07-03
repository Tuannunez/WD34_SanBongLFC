@extends('admin.layouts.app')

@section('title', 'Vai trò')
@section('page-title', 'Quản lý vai trò')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý vai trò</h4>
            <div class="text-muted">Danh sách vai trò sử dụng trong hệ thống SanBongLFC</div>
        </div>

        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-circle me-1"></i>
            Tạo vai trò mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Tổng vai trò</div>
                        <h4 class="fw-bold mb-0">
                            {{ method_exists($roles, 'total') ? $roles->total() : $roles->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        @php
            $roleCollection = collect(method_exists($roles, 'items') ? $roles->items() : $roles);
            $activeCount = $roleCollection->where('status', 1)->count();
            $inactiveCount = $roleCollection->where('status', 0)->count();
        @endphp

        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check-circle"></i>
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
                    <div class="stat-icon bg-secondary-subtle text-secondary">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Không hoạt động</div>
                        <h4 class="fw-bold mb-0">{{ $inactiveCount }}</h4>
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
                        <i class="bi bi-list-check text-primary me-1"></i>
                        Danh sách vai trò
                    </h5>
                    <div class="text-muted small">Quản lý tên vai trò, slug và trạng thái sử dụng</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Vai trò</th>
                        <th>Slug</th>
                        <th>Mô tả</th>
                        <th style="width: 170px;">Trạng thái</th>
                        <th class="text-end" style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="fw-bold text-muted">
                                #{{ $role->id }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                         style="width: 42px; height: 42px;">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </div>

                                    <div>
                                        <div class="fw-bold">{{ $role->name }}</div>
                                        <small class="text-muted">Vai trò hệ thống</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $role->slug }}
                                </span>
                            </td>

                            <td class="text-muted">
                                {{ $role->description ?: 'Chưa có mô tả' }}
                            </td>

                            <td>
                                @if($role->status)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-pause-circle me-1"></i>
                                        Không hoạt động
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.roles.edit', $role) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.roles.destroy', $role) }}"
                                      method="POST"
                                      class="d-inline-block"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa vai trò này không?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Chưa có vai trò nào trong hệ thống
                                </div>

                                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Tạo vai trò đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($roles, 'links'))
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
@endsection