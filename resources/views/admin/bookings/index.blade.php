@extends('admin.layouts.app')

@section('content')

<style>
    .custom-pagination {
        padding: 4px 0;
    }

    .custom-pagination .page-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #374151;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .06);
    }

    .custom-pagination .page-btn:hover {
        background: #ecfdf5;
        border-color: #16a34a;
        color: #15803d;
        transform: translateY(-1px);
    }

    .custom-pagination .page-btn.active {
        background: #16a34a;
        border-color: #16a34a;
        color: #ffffff;
        box-shadow: 0 8px 18px rgba(22, 163, 74, .25);
    }

    .custom-pagination .page-btn.disabled {
        background: #f1f5f9;
        color: #94a3b8;
        border-color: #e5e7eb;
        box-shadow: none;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Đơn đặt sân</h3>
            <p class="text-muted mb-0">Quản lý danh sách đơn đặt sân của khách hàng</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $bookingCollection = method_exists($bookings, 'getCollection')
            ? $bookings->getCollection()
            : collect($bookings);

        $totalMoneyOnPage = $bookingCollection->sum(function ($booking) {
            return $booking->total_price
                ?? $booking->total_amount
                ?? $booking->total
                ?? $booking->amount
                ?? 0;
        });

        $pendingCount = $bookingCollection->where('status', 'pending')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng đơn đặt sân</p>
                    <h4 class="fw-bold mb-0">
                        {{ method_exists($bookings, 'total') ? $bookings->total() : count($bookings) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Đơn chờ xác nhận</p>
                    <h4 class="fw-bold mb-0 text-warning">
                        {{ $pendingCount }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng tiền hiển thị</p>
                    <h4 class="fw-bold mb-0 text-success">
                        {{ number_format((float) $totalMoneyOnPage, 0, ',', '.') }}đ
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.bookings.index') }}" class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text"
                           name="keyword"
                           value="{{ request('keyword') }}"
                           class="form-control rounded-3"
                           placeholder="Tìm mã đơn, tên khách, email, số điện thoại...">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select rounded-3">
                        <option value="">Tất cả trạng thái</option>

                        <option value="pending" @selected(request('status') === 'pending')>
                            Chờ xác nhận
                        </option>

                        <option value="confirmed" @selected(request('status') === 'confirmed')>
                            Đã xác nhận
                        </option>

                        <option value="completed" @selected(request('status') === 'completed')>
                            Hoàn thành
                        </option>

                        <option value="cancelled" @selected(request('status') === 'cancelled')>
                            Đã hủy
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date"
                           name="booking_date"
                           value="{{ request('booking_date') }}"
                           class="form-control rounded-3">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 w-100">
                        <i class="bi bi-search me-1"></i>
                        Tìm
                    </button>

                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary rounded-3">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-calendar-check text-primary me-2"></i>
                Danh sách đơn đặt sân
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Khách hàng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $status = $booking->status ?? 'pending';

                                $statusText = match ($status) {
                                    'confirmed' => 'Đã xác nhận',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                    default => 'Chờ xác nhận',
                                };

                                $statusClass = match ($status) {
                                    'confirmed' => 'bg-success-subtle text-success',
                                    'completed' => 'bg-primary-subtle text-primary',
                                    'cancelled' => 'bg-danger-subtle text-danger',
                                    default => 'bg-warning-subtle text-warning',
                                };

                                $totalMoney = $booking->total_price
                                    ?? $booking->total_amount
                                    ?? $booking->total
                                    ?? $booking->amount
                                    ?? 0;

                                $customerName = $booking->user_name
                                    ?? $booking->customer_name
                                    ?? $booking->name
                                    ?? 'Chưa có thông tin';

                                $customerEmail = $booking->user_email
                                    ?? $booking->customer_email
                                    ?? $booking->email
                                    ?? '-';

                                $customerPhone = $booking->customer_phone
                                    ?? $booking->phone
                                    ?? '-';
                            @endphp

                            <tr>
                                <td class="ps-4 fw-semibold">
                                    #{{ $booking->id }}

                                    @if(!empty($booking->booking_code))
                                        <div>
                                            <small class="text-muted">{{ $booking->booking_code }}</small>
                                        </div>
                                    @elseif(!empty($booking->code))
                                        <div>
                                            <small class="text-muted">{{ $booking->code }}</small>
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-person"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $customerName }}
                                            </div>
                                            <small class="text-muted">Khách đặt sân</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    {{ $customerEmail }}
                                </td>

                                <td>
                                    {{ $customerPhone }}
                                </td>

                                <td class="fw-bold text-success">
                                    {{ number_format((float) $totalMoney, 0, ',', '.') }}đ
                                </td>

                                <td>
                                    <span class="badge {{ $statusClass }} px-3 py-2">
                                        {{ $statusText }}
                                    </span>
                                </td>

                                <td>
                                    {{ !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : '-' }}
                                </td>

                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-3">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    @if(($booking->status ?? '') !== 'cancelled')
                                        <form action="{{ route('admin.bookings.destroy', $booking->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-3"
                                                    onclick="return confirm('Bạn có chắc muốn hủy đơn đặt sân này?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có đơn đặt sân nào.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


                </div>
            </div>
        @endif
    </div>
</div>
@endsection
