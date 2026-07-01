@extends('layouts.app')

@section('title', 'Đơn đặt sân của tôi')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Đơn đặt sân của tôi</h3>
            <p class="text-muted mb-0">Theo dõi trạng thái các đơn đặt sân bạn đã gửi</p>
        </div>

        <a href="{{ route('home') }}" class="btn btn-primary rounded-3">
            <i class="bi bi-plus-circle me-1"></i>
            Đặt sân tiếp
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
                            <th class="ps-4">Mã đơn</th>
                            <th>Sân</th>
                            <th>Ngày đặt</th>
                            <th>Khung giờ</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
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
                                    ?? $booking->detail_price
                                    ?? $booking->field_price_per_hour
                                    ?? 0;

                                $bookingDate = $booking->detail_booking_date
                                    ?? $booking->booking_date
                                    ?? $booking->date
                                    ?? null;
                            @endphp

                            <tr>
                                <td class="ps-4 fw-semibold">
                                    #{{ $booking->id }}
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-dribbble"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $booking->field_name ?? 'Sân chưa xác định' }}
                                            </div>
                                            <small class="text-muted">Sân đã đặt</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($bookingDate)
                                        {{ \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        @if($booking->slot_start_time || $booking->slot_end_time)
                                            {{ $booking->slot_start_time ?? '' }}
                                            -
                                            {{ $booking->slot_end_time ?? '' }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>

                                <td class="fw-bold text-success">
                                    {{ number_format((float) $totalMoney, 0, ',', '.') }}đ
                                </td>

                                <td>
                                    <span class="badge {{ $statusClass }} px-3 py-2">
                                        {{ $statusText }}
                                    </span>
                                </td>

                                <td class="text-end pe-4">
                                    <a href="{{ route('user.bookings.show', $booking->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Bạn chưa có đơn đặt sân nào.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($bookings, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection