@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Dịch vụ đặt sân</h3>
            <p class="text-muted mb-0">Quản lý các dịch vụ khách đã chọn trong đơn đặt sân</p>
        </div>

        <a href="{{ route('admin.booking-services.create') }}" class="btn btn-primary rounded-3 px-4">
            <i class="bi bi-plus-circle me-1"></i> Thêm dịch vụ đặt sân
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng bản ghi</p>
                    <h4 class="fw-bold mb-0">
                        {{ method_exists($bookingServices, 'total') ? $bookingServices->total() : count($bookingServices) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng số lượng</p>
                    <h4 class="fw-bold mb-0">
                        {{ $bookingServices->sum('quantity') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng tiền dịch vụ</p>
                    <h4 class="fw-bold mb-0 text-success">
                        {{ number_format($bookingServices->sum('total'), 0, ',', '.') }}đ
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-basket text-primary me-2"></i>
                Danh sách dịch vụ đặt sân
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Dịch vụ</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Thành tiền</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bookingServices as $item)
                            <tr>
                                <td class="ps-4 fw-semibold">{{ $item->id }}</td>

                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                        {{ $item->booking_code ?? 'Đơn #' . $item->booking_id }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->customer_name ?? 'Chưa có thông tin' }}
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-cup-straw"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $item->service_name ?? 'Không có dịch vụ' }}
                                            </div>
                                            <small class="text-muted">Dịch vụ đi kèm</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        {{ $item->quantity }}
                                    </span>
                                </td>

                                <td class="fw-semibold">
                                    {{ number_format($item->price, 0, ',', '.') }}đ
                                </td>

                                <td class="fw-bold text-success">
                                    {{ number_format($item->total, 0, ',', '.') }}đ
                                </td>

                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.booking-services.show', $item->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.booking-services.edit', $item->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-3">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('admin.booking-services.destroy', $item->id) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-3"
                                                onclick="return confirm('Bạn có chắc muốn xóa dịch vụ đặt sân này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có dịch vụ đặt sân nào.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($bookingServices, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $bookingServices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection