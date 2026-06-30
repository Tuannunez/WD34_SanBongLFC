@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Quản lý dịch vụ</h3>
            <p class="text-muted mb-0">Danh sách các dịch vụ đi kèm khi khách đặt sân</p>
        </div>

        <a href="{{ route('admin.services.create') }}" class="btn btn-primary rounded-3 px-4">
            <i class="bi bi-plus-circle me-1"></i> Thêm dịch vụ
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-cup-straw text-primary me-2"></i>
                    Danh sách dịch vụ
                </h5>

                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                    Tổng: {{ method_exists($services, 'total') ? $services->total() : count($services) }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Tên dịch vụ</th>
                            <th>Giá</th>
                            <th>Đơn vị</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $service->id }}</td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 42px; height: 42px;">
                                            <i class="bi bi-bag-check"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">{{ $service->name }}</div>
                                            <small class="text-muted">Dịch vụ sân bóng</small>
                                        </div>
                                    </div>
                                </td>

                                <td class="fw-semibold text-success">
                                    {{ number_format($service->price, 0, ',', '.') }}đ
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $service->unit ?? '---' }}
                                    </span>
                                </td>

                                <td style="max-width: 300px;">
                                    <span class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($service->description, 70) }}
                                    </span>
                                </td>

                                <td>
                                    @if($service->status == 1)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">Tạm ẩn</span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.services.show', $service->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.services.edit', $service->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-3">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('admin.services.destroy', $service->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-3"
                                                onclick="return confirm('Bạn có chắc muốn xóa dịch vụ này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có dịch vụ nào.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($services, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $services->links() }}
            </div>
        @endif
    </div>
</div>
@endsection