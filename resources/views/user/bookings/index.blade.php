@extends('layouts.app')

@section('title', 'Đơn đặt sân của tôi')

@section('content')
<div class="container py-5">

    <div class="row g-4">

        {{-- CỘT TRÁI: TÀI KHOẢN --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body text-center p-4">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 78px; height: 78px;">
                        <i class="bi bi-person-circle fs-1"></i>
                    </div>

                    <h5 class="fw-bold mb-1">
                        {{ Auth::user()->name ?? 'Khách hàng' }}
                    </h5>

                    <p class="text-muted small mb-0">
                        {{ Auth::user()->email ?? 'Chưa có email' }}
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-grid me-2 text-success"></i>
                        Tài khoản của tôi
                    </h6>
                </div>

                <div class="card-body p-3">
                    <div class="d-grid gap-2">

                        <a href="{{ route('user.profile.index') }}"
                           class="btn btn-light border rounded-3 text-start py-3">
                            <i class="bi bi-person me-2 text-primary"></i>
                            Hồ sơ cá nhân
                        </a>

                        <a href="{{ route('user.bookings.index') }}"
                           class="btn btn-success rounded-3 text-start py-3">
                            <i class="bi bi-clock-history me-2"></i>
                            Lịch sử đặt sân
                        </a>

                        @if(Auth::check() && Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                               class="btn btn-light border rounded-3 text-start py-3">
                                <i class="bi bi-speedometer2 me-2 text-warning"></i>
                                Trang quản trị
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn đăng xuất không?')">
                            @csrf

                            <button type="submit"
                                    class="btn btn-outline-danger rounded-3 text-start py-3 w-100">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: NỘI DUNG --}}
        <div class="col-lg-9">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Đơn đặt sân của tôi</h3>
                    <p class="text-muted mb-0">
                        Theo dõi trạng thái các đơn đặt sân bạn đã gửi
                    </p>
                </div>

                <a href="{{ route('home') }}" class="btn btn-primary rounded-3 px-4">
                    <i class="bi bi-plus-circle me-1"></i>
                    Đặt sân tiếp
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ session('success') }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                    @foreach($errors->all() as $error)
                        <div>
                            <i class="bi bi-exclamation-circle me-1"></i>
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- HỒ SƠ CÁ NHÂN --}}
            <div id="profile-info" class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-semibold mb-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        Hồ sơ cá nhân
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <p class="text-muted small mb-1">Họ tên</p>
                                <div class="fw-semibold">
                                    {{ Auth::user()->name ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <p class="text-muted small mb-1">Email</p>
                                <div class="fw-semibold">
                                    {{ Auth::user()->email ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <p class="text-muted small mb-1">Số điện thoại</p>
                                <div class="fw-semibold">
                                    {{ Auth::user()->phone ?? 'Chưa cập nhật' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- THỐNG KÊ NHANH --}}
            @php
                $bookingCollection = method_exists($bookings, 'getCollection')
                    ? $bookings->getCollection()
                    : collect($bookings);

                $totalBookings = method_exists($bookings, 'total')
                    ? $bookings->total()
                    : $bookingCollection->count();

                $pendingCount = $bookingCollection->where('status', 'pending')->count();

                $confirmedCount = $bookingCollection->where('status', 'confirmed')->count();

                $totalMoney = $bookingCollection->sum(function ($booking) {
                    return $booking->total_price
                        ?? $booking->total_amount
                        ?? $booking->total
                        ?? $booking->amount
                        ?? $booking->detail_price
                        ?? $booking->field_price_per_hour
                        ?? 0;
                });
            @endphp

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Tổng đơn</p>
                            <h4 class="fw-bold mb-0">{{ $totalBookings }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Chờ xác nhận</p>
                            <h4 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Đã xác nhận</p>
                            <h4 class="fw-bold text-success mb-0">{{ $confirmedCount }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Tổng tiền</p>
                            <h5 class="fw-bold text-success mb-0">
                                {{ number_format((float) $totalMoney, 0, ',', '.') }}đ
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DANH SÁCH ĐƠN --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-calendar-check text-primary me-2"></i>
                            Lịch sử đặt sân
                        </h5>

                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                            Tổng: {{ $totalBookings }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 25%;">Mã đơn</th>
                                    <th>Sân</th>
                                    <th>Ngày đặt</th>
                                    <th>Khung giờ</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end pe-4" style="width: 25%;">Thao tác</th>
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

                                        $totalMoneyRow = $booking->total_price
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

                                        $startTime = $booking->slot_start_time
                                            ?? $booking->start_time
                                            ?? null;

                                        $endTime = $booking->slot_end_time
                                            ?? $booking->end_time
                                            ?? null;

                                        // Thiết lập biến cọc 30% đề phòng DB cũ chưa kịp có giá trị
                                        $depositAmount = $booking->deposit_amount ?? ($totalMoneyRow * 0.3);
                                        $isDepositPaid = $booking->is_deposit_paid ?? false;
                                    @endphp

                                    <tr>
                                        {{-- 1. CỘT MÃ ĐƠN & HIỂN THỊ THÔNG TIN ĐÃ CỌC/CHƯA CỌC --}}
                                        <td class="ps-4">
                                            @if($status === 'pending')
                                                <a href="{{ route('user.payment.show', $booking->id) }}" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-hash small"></i>{{ $booking->id }} 
                                                    <span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 0.65rem;">Cần cọc 30%</span>
                                                </a>
                                            @else
                                                <div class="fw-bold text-dark d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-hash small text-muted"></i>{{ $booking->id }}
                                                </div>
                                            @endif

                                            @if(!empty($booking->booking_code))
                                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-qr-code me-1"></i>{{ $booking->booking_code }}
                                                </small>
                                            @endif

                                            {{-- HIỂN THỊ NHÃN TRẠNG THÁI TIỀN CỌC --}}
                                            @if($isDepositPaid)
                                                <small class="text-success d-block mt-1 fw-medium" style="font-size: 0.72rem;">
                                                    <i class="bi bi-shield-check me-1"></i>Đã cọc: {{ number_format((float) $depositAmount, 0, ',', '.') }}đ
                                                </small>
                                            @else
                                                <small class="text-danger d-block mt-1 fw-medium" style="font-size: 0.72rem;">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Chưa cọc: {{ number_format((float) $depositAmount, 0, ',', '.') }}đ
                                                </small>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                                     style="width: 42px; height: 42px;">
                                                    <i class="bi bi-dribbble"></i>
                                                </div>

                                                <div>
                                                    <div class="fw-semibold text-dark">
                                                        {{ $booking->field_name ?? 'Sân chưa xác định' }}
                                                    </div>
                                                    <small class="text-muted" style="font-size: 0.75rem;">Sân đã đặt</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center text-secondary">
                                                <i class="bi bi-calendar3 me-2 text-muted"></i>
                                                @if($bookingDate)
                                                    {{ \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </td>

                                        <td>
                                            <span class="badge bg-light text-dark border px-3 py-2 d-inline-flex align-items-center shadow-sm rounded-3">
                                                <i class="bi bi-clock me-1.5 text-muted"></i>
                                                @if($startTime || $endTime)
                                                    {{ $startTime ?? '' }} - {{ $endTime ?? '' }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </td>

                                        <td class="fw-bold text-success fs-6">
                                            {{ number_format((float) $totalMoneyRow, 0, ',', '.') }}đ
                                        </td>

                                        <td>
                                            <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill fw-semibold">
                                                @if($status === 'pending') <i class="bi bi-hourglass-split me-1"></i> @endif
                                                @if($status === 'confirmed') <i class="bi bi-check-circle-fill me-1"></i> @endif
                                                @if($status === 'completed') <i class="bi bi-patch-check-fill me-1"></i> @endif
                                                @if($status === 'cancelled') <i class="bi bi-x-circle-fill me-1"></i> @endif
                                                {{ $statusText }}
                                            </span>
                                        </td>

                                        {{-- 2. CỘT THAO TÁC (NÚT BẤM CÓ ICON & HIỂN THỊ RÕ SỐ TIỀN CỌC) --}}
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-1">
                                                @if($status === 'pending')
                                                    <a href="{{ route('user.payment.show', $booking->id) }}" 
                                                       class="btn btn-sm btn-success rounded-3 d-inline-flex align-items-center gap-1 py-1.5 shadow-sm fw-medium" 
                                                       title="Thanh toán cọc {{ number_format((float) $depositAmount, 0, ',', '.') }}đ">
                                                        <i class="bi bi-credit-card-2-back-fill"></i> 
                                                    </a>
                                                @endif

                                                <a href="{{ route('user.bookings.show', $booking->id) }}"
                                                   class="btn btn-sm btn-outline-info rounded-3 d-inline-flex align-items-center gap-1 py-1.5"
                                                   title="Xem chi tiết đơn">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>

                                                @if($status === 'pending')
                                                    <form action="{{ route('user.bookings.destroy', $booking->id) }}"
                                                          method="POST"
                                                          class="d-inline mb-0"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa đơn đặt sân này không?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger rounded-3 d-inline-flex align-items-center py-1.5"
                                                                title="Xóa đơn">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                            <h6 class="fw-semibold">Bạn chưa có đơn đặt sân nào</h6>
                                            <p class="text-muted mb-3">
                                                Hãy chọn sân phù hợp và gửi đơn đặt sân đầu tiên của bạn.
                                            </p>

                                            <a href="{{ route('home') }}" class="btn btn-primary rounded-3">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                Đặt sân ngay
                                            </a>
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
    </div>
</div>
@endsection