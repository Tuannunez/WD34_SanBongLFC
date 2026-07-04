@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Quản lý khuyến mãi</h3>
            <p class="text-muted mb-0">Danh sách các mã khuyến mãi và chương trình giảm giá</p>
        </div>

        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary rounded-3 px-4">
            <i class="bi bi-plus-circle me-1"></i> Thêm khuyến mãi
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
                    <i class="bi bi-tag text-primary me-2"></i>
                    Danh sách khuyến mãi
                </h5>

                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                    Tổng: {{ method_exists($promotions, 'total') ? $promotions->total() : count($promotions) }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Mã khuyến mãi</th>
                            <th>Tên</th>
                            <th>Loại giảm</th>
                            <th>Giá trị</th>
                            <th>Lượt sử dụng</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($promotions as $promotion)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $promotion->id }}</td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 42px; height: 42px;">
                                            <i class="bi bi-tag-fill"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold font-monospace">{{ $promotion->code }}</div>
                                            <small class="text-muted">{{ $promotion->name }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $promotion->name }}</td>

                                <td>
                                    @if($promotion->discount_type == 'percent')
                                        <span class="badge bg-info-subtle text-info px-3 py-2">
                                            <i class="bi bi-percent me-1"></i> Phần trăm
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                            <i class="bi bi-cash-coin me-1"></i> Tiền cố định
                                        </span>
                                    @endif
                                </td>

                                <td class="fw-semibold">
                                    @if($promotion->discount_type == 'percent')
                                        {{ $promotion->discount_value }}%
                                    @else
                                        {{ number_format($promotion->discount_value, 0, ',', '.') }}đ
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $promotion->quantity ?? 'Không giới hạn' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $promotion->start_date ? $promotion->start_date->format('d/m/Y') : '---' }}
                                </td>

                                <td>
                                    {{ $promotion->end_date ? $promotion->end_date->format('d/m/Y') : '---' }}
                                </td>

                                <td>
                                    @if($promotion->status == 1)
                                        <span class="badge bg-success-subtle text-success px-3 py-2">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">Tạm ẩn</span>
                                    @endif
                                </td>

                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.promotions.show', $promotion->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-3">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('admin.promotions.destroy', $promotion->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-3"
                                                onclick="return confirm('Bạn có chắc muốn xóa khuyến mãi này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có khuyến mãi nào.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($promotions, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
