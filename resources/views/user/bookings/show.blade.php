@extends('layouts.app')

@section('title', 'Chi tiết đơn đặt sân')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết đơn đặt sân #{{ $booking->id }}</h3>
            <p class="text-muted mb-0">Thông tin sân, khung giờ và dịch vụ đi kèm</p>
        </div>

        <a href="{{ route('user.bookings.index') }}" class="btn btn-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

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

        $bookingDate = $booking->booking_date
            ?? $booking->date
            ?? null;

        $customerName = $booking->customer_name
            ?? $booking->name
            ?? Auth::user()->name
            ?? 'Khách hàng';

        $customerEmail = $booking->customer_email
            ?? $booking->email
            ?? Auth::user()->email
            ?? '-';

        $customerPhone = $booking->customer_phone
            ?? $booking->phone
            ?? '-';
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

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-person-circle text-primary me-2"></i>
                Thông tin khách hàng
            </h5>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="text-muted mb-1">Họ tên</label>
                    <div class="fw-semibold">{{ $customerName }}</div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Email</label>
                    <div class="fw-semibold">{{ $customerEmail }}</div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Số điện thoại</label>
                    <div class="fw-semibold">{{ $customerPhone }}</div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Ngày tạo đơn</label>
                    <div class="fw-semibold">
                        {{ !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Ngày đặt</label>
                    <div class="fw-semibold">
                        @if($bookingDate)
                            {{ \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Ghi chú</label>
                    <div class="fw-semibold">
                        {{ $booking->note ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-dribbble text-success me-2"></i>
                Thông tin sân đã đặt
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Sân</th>
                            <th>Ngày đặt</th>
                            <th>Khung giờ</th>
                            <th>Giá sân</th>
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
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-dribbble"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $detail->field_name ?? 'Sân chưa xác định' }}
                                            </div>
                                            <small class="text-muted">Sân đã đặt</small>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có chi tiết sân.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-basket text-primary me-2"></i>
                Dịch vụ đi kèm
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Dịch vụ</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($bookingServices ?? collect()) as $bookingService)
                            @php
                                $servicePrice = $bookingService->price ?? 0;
                                $serviceTotal = $bookingService->total
                                    ?? $bookingService->total_price
                                    ?? (($bookingService->quantity ?? 0) * $servicePrice);
                            @endphp

                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-cup-straw"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $bookingService->service_name ?? 'Không có dịch vụ' }}
                                            </div>
                                            <small class="text-muted">Dịch vụ đi kèm</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        {{ $bookingService->quantity ?? 0 }}
                                    </span>
                                </td>

                                <td class="fw-semibold">
                                    {{ number_format((float) $servicePrice, 0, ',', '.') }}đ
                                </td>

                                <td class="fw-bold text-success">
                                    {{ number_format((float) $serviceTotal, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Đơn này chưa chọn dịch vụ đi kèm.</span>
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