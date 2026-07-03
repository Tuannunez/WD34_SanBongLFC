@extends('admin.layouts.app')

@section('title', 'Loại sân')
@section('page-title', 'Quản lý loại sân')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý loại sân</h4>
            <div class="text-muted">Danh sách loại sân đang sử dụng trong hệ thống SanBongLFC</div>
        </div>

        <a href="{{ route('admin.field-types.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-circle me-1"></i>
            Thêm loại sân
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $fieldTypeCollection = collect(method_exists($fieldTypes, 'items') ? $fieldTypes->items() : $fieldTypes);
        $activeCount = $fieldTypeCollection->where('status', 1)->count();
        $inactiveCount = $fieldTypeCollection->where('status', 0)->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Tổng loại sân</div>
                        <h4 class="fw-bold mb-0">
                            {{ method_exists($fieldTypes, 'total') ? $fieldTypes->total() : $fieldTypes->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

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
                        <div class="text-muted small">Vô hiệu</div>
                        <h4 class="fw-bold mb-0">{{ $inactiveCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-card">
        <div class="card-header bg-white border-0 px-4 py-4">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-list-ul text-primary me-1"></i>
                Danh sách loại sân
            </h5>
            <div class="text-muted small">Quản lý tên loại sân, số người chơi, mô tả và trạng thái</div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>Tên loại sân</th>
                        <th style="width: 140px;">Số người</th>
                        <th>Mô tả</th>
                        <th style="width: 170px;">Trạng thái</th>
                        <th class="text-end" style="width: 170px;">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($fieldTypes as $key => $fieldType)
                        <tr>
                            <td class="fw-bold text-muted">
                                #{{ method_exists($fieldTypes, 'firstItem') ? $fieldTypes->firstItem() + $key : $key + 1 }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                                         style="width: 44px; height: 44px;">
                                        <i class="bi bi-grid-3x3-gap-fill"></i>
                                    </div>

                                    <div>
                                        <div class="fw-bold">{{ $fieldType->name }}</div>
                                        <small class="text-muted">Loại sân bóng</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($fieldType->number_of_players)
                                    <span class="badge bg-info-subtle text-info">
                                        <i class="bi bi-people me-1"></i>
                                        {{ $fieldType->number_of_players }} người
                                    </span>
                                @else
                                    <span class="text-muted">Chưa cập nhật</span>
                                @endif
                            </td>

                            <td class="text-muted">
                                {{ \Illuminate\Support\Str::limit($fieldType->description, 80) ?: 'Chưa có mô tả' }}
                            </td>

                            <td>
                                @if($fieldType->status)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        <i class="bi bi-pause-circle me-1"></i>
                                        Vô hiệu
                                    </span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.field-types.edit', $fieldType) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Sửa loại sân">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.field-types.destroy', $fieldType) }}"
                                      method="POST"
                                      class="d-inline-block"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa loại sân này?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Xóa loại sân">
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
                                    Chưa có loại sân nào trong hệ thống
                                </div>

                                <a href="{{ route('admin.field-types.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Thêm loại sân đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($fieldTypes, 'links'))
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $fieldTypes->links() }}
            </div>
        @endif
    </div>
@endsection