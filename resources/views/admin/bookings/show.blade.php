@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết đơn đặt sân #{{ $booking->id }}</h3>
            <p class="text-muted mb-0">Thông tin khách hàng, sân đặt và trạng thái đơn</p>
        </div>

        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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

        $bookingDate = $booking->booking_date
            ?? $booking->date
            ?? null;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Mã đơn</p>
                    <h4 class="fw-bold mb-0">#{{ $booking->id }}</h4>

                    @if(!empty($booking->booking_code))
                        <small class="text-muted">{{ $booking->booking_code }}</small>
                    @elseif(!empty($booking->code))
                        <small class="text-muted">{{ $booking->code }}</small>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Trạng thái</p>
                    <span class="badge {{ $statusClass }} px-3 py-2">
                        {{ $statusText }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng tiền</p>
                    <h4 class="fw-bold mb-0 text-success">
                        {{ number_format((float) $totalMoney, 0, ',', '.') }}đ
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-person-circle text-primary me-2"></i>
                        Thông tin khách hàng
                    </h5>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted mb-1">Họ tên</label>
                        <div class="fw-semibold">{{ $customerName }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Email</label>
                        <div class="fw-semibold">{{ $customerEmail }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Số điện thoại</label>
                        <div class="fw-semibold">{{ $customerPhone }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted mb-1">Ngày tạo đơn</label>
                        <div class="fw-semibold">
                            {{ !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>

                    <div>
                        <label class="text-muted mb-1">Ngày đặt</label>
                        <div class="fw-semibold">
                            @if($bookingDate)
                                {{ \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-pencil-square text-primary me-2"></i>
                        Cập nhật trạng thái
                    </h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Trạng thái đơn</label>

                            <select name="status" class="form-select rounded-3 @error('status') is-invalid @enderror">
                                <option value="pending" @selected($status === 'pending')>
                                    Chờ xác nhận
                                </option>

                                <option value="confirmed" @selected($status === 'confirmed')>
                                    Đã xác nhận
                                </option>

                                <option value="completed" @selected($status === 'completed')>
                                    Hoàn thành
                                </option>

                                <option value="cancelled" @selected($status === 'cancelled')>
                                    Đã hủy
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary rounded-3 px-4">
                            <i class="bi bi-check-circle me-1"></i>
                            Cập nhật
                        </button>
                    </form>

                    <hr>

                    <p class="text-muted mb-0">
                        Khi admin cập nhật trạng thái, user sẽ thấy trạng thái mới trong mục
                        <strong>Đơn của tôi</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-dribbble text-success me-2"></i>
                Danh sách chi tiết đặt sân
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Sân</th>
                            <th>Ngày đặt</th>
                            <th>Khung giờ</th>
                            <th>Giá</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($bookingDetails ?? collect()) as $detail)
                            @php
                                $detailPrice = $detail->price
                                    ?? $detail->field_price
                                    ?? $detail->total_price
                                    ?? $detail->total
                                    ?? $detail->amount
                                    ?? $detail->field_price_per_hour
                                    ?? 0;

                                $detailDate = $detail->booking_date
                                    ?? $detail->date
                                    ?? $bookingDate
                                    ?? null;

                                $startTime = $detail->slot_start_time
                                    ?? $detail->start_time
                                    ?? null;

                                $endTime = $detail->slot_end_time
                                    ?? $detail->end_time
                                    ?? null;
                            @endphp

                            <tr>
                                <td class="ps-4 fw-semibold">
                                    #{{ $detail->id }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-dribbble"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $detail->field_name ?? 'Không có sân' }}
                                            </div>
                                            <small class="text-muted">Sân đặt</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($detailDate)
                                        {{ \Carbon\Carbon::parse($detailDate)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        @if($startTime || $endTime)
                                            {{ $startTime ?? '' }} - {{ $endTime ?? '' }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>

                                <td class="fw-bold text-success">
                                    {{ number_format((float) $detailPrice, 0, ',', '.') }}đ
                                </td>

                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.booking-details.show', $detail->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Đơn này chưa có chi tiết đặt sân.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection