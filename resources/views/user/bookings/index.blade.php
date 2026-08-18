@extends('layouts.app')

@section('title', 'Đơn đặt sân của tôi')

@push('styles')
<style>
    .booking-history-page {
        --booking-primary: #2563eb;
        --booking-success: #198754;
        --booking-border: #e8edf3;
        --booking-shadow: 0 10px 28px rgba(15, 23, 42, .065);
    }

    .booking-history-page .account-panel,
    .booking-history-page .booking-stat-card,
    .booking-history-page .booking-list-card {
        border: 1px solid rgba(226, 232, 240, .75) !important;
        box-shadow: var(--booking-shadow) !important;
    }

    .booking-history-page .account-avatar {
        background: linear-gradient(145deg, #dcfce7, #f0fdf4) !important;
        box-shadow: inset 0 0 0 1px rgba(22, 163, 74, .12);
    }

    .booking-history-page .booking-stat-card {
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .booking-history-page .booking-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, .09) !important;
    }

    .booking-history-page .booking-list-card {
        overflow: hidden;
    }

    .booking-history-page .booking-history-table thead th {
        padding-top: 14px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--booking-border);
        color: #475569;
        font-size: .78rem;
        letter-spacing: .025em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .booking-history-page .booking-history-table tbody tr {
        transition: background-color .15s ease;
    }

    .booking-history-page .booking-history-table tbody tr:hover {
        background: #f8fbff;
    }

    .booking-history-page .booking-history-table td {
        border-color: #eef2f6;
    }

    .booking-history-page .booking-action-group {
        padding: 4px;
        border: 1px solid #e7ecf2;
        border-radius: 12px;
        background: #f8fafc;
    }

    .booking-history-page .booking-action-group .btn {
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding-inline: 10px !important;
        border-radius: 9px !important;
    }

    .booking-history-page .invoice-action {
        border-color: #334155;
        color: #334155;
        background: #fff;
    }

    .booking-history-page .invoice-action:hover {
        border-color: #0f172a;
        color: #fff;
        background: #0f172a;
    }

    .booking-history-page .modal-content {
        border: 1px solid rgba(226, 232, 240, .9) !important;
    }

    @media (max-width: 991.98px) {
        .booking-history-page .booking-action-group {
            flex-wrap: wrap;
            justify-content: flex-end;
        }
    }
</style>
@endpush

@section('content')
<div class="container py-5 booking-history-page">
    <div class="row g-4">

        {{-- CỘT TRÁI: TÀI KHOẢN --}}
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 mb-4 account-panel">
                <div class="card-body text-center p-4">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 account-avatar"
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

            <div class="card border-0 shadow-sm rounded-4 account-panel">
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

            {{-- THÔNG BÁO THÀNH CÔNG --}}
           @if(session('success'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: "Thành công!",
                        text: "Đơn hủy đã được gửi đi thành công! Số tiền hoàn của bạn sẽ được xử lý và chuyển khoản lại trong 24h tới.",
                        icon: "success",
                        confirmButtonText: "Đã hiểu",
                        confirmButtonColor: "#065f46",
                        backdrop: true,
                        allowOutsideClick: false
                    });
                });
            </script>
            @endif

            {{-- THÔNG BÁO LỖI --}}
            @if($errors->any())
                <div class="alert border-0 shadow-sm rounded-4 mb-4 p-0 overflow-hidden" 
                     style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); border-left: 5px solid #c62828 !important;">
                    <div class="p-3.5 px-4 d-flex align-items-start justify-content-between">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0 mt-0.5" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #b71c1c;">Có lỗi xảy ra!</h6>
                                <ul class="list-unstyled mb-0 small fw-medium">
                                    @foreach($errors->all() as $error)
                                        <li class="d-flex align-items-center gap-1.5 py-0.5" style="color: #c62828;">
                                            <i class="bi bi-dot fs-4 lh-1"></i> {{ $error }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Close" style="opacity: 0.6;"></button>
                    </div>
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
                    return $booking->total_amount
                        ?? $booking->total_price
                        ?? $booking->final_amount
                        ?? 0;
                });
            @endphp

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 booking-stat-card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Tổng đơn</p>
                            <h4 class="fw-bold mb-0">{{ $totalBookings }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 booking-stat-card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Chờ xác nhận</p>
                            <h4 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 booking-stat-card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Đã xác nhận</p>
                            <h4 class="fw-bold text-success mb-0">{{ $confirmedCount }}</h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 booking-stat-card">
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
            <div class="card border-0 shadow-sm rounded-4 booking-list-card">
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
                        <table class="table table-hover align-middle mb-0 booking-history-table">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 22%;">Mã đơn</th>
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

                                        $totalMoneyRow = (float)($booking->total_amount
                                            ?? $booking->total_price
                                            ?? $booking->final_amount
                                            ?? 0);

                                        $bookingDate = $booking->detail_booking_date
                                            ?? $booking->booking_date
                                            ?? null;

                                        $startTime = $booking->slot_start_time
                                            ?? $booking->start_time
                                            ?? null;

                                        $endTime = $booking->slot_end_time
                                            ?? $booking->end_time
                                            ?? null;

                                        $isMonthly = (($booking->booking_type ?? 'single') === 'monthly');

                                        $pType = strtolower(trim((string)($booking->payment_type ?? '')));
                                        $pStatus = strtolower(trim((string)($booking->payment_status ?? '')));
                                        $paidAmt = (float)($booking->paid_amount ?? 0);
                                        $remainingAmount = max(0, $totalMoneyRow - $paidAmt);

                                        // KIỂM TRA ĐIỀU KIỆN ĐÃ CHECK-OUT HOẶC SỬ DỤNG XONG SÂN
                                        $usageStatus = strtolower((string)($booking->usage_status ?? 'not_checked_in'));
                                        $isFinishedUsing = in_array($usageStatus, ['checked_out', 'completed']) || ($status === 'completed');

                                        $isPaidFull = false;
                                        if ($totalMoneyRow > 0) {
                                            if ($paidAmt >= $totalMoneyRow) {
                                                $isPaidFull = true;
                                            } elseif (in_array($pStatus, ['paid_full', 'completed'])) {
                                                $isPaidFull = true;
                                            } elseif ($status !== 'pending' && in_array($pType, ['full', 'full_payment'])) {
                                                $isPaidFull = true;
                                            }
                                        }

                                        if ($isPaidFull) {
                                            $depositAmount = $totalMoneyRow;
                                            $depositLabel = 'Đã thanh toán 100%';
                                        } else {
                                            $defaultDepositPercent = $isMonthly ? 0.50 : 0.30;
                                            $depositAmount = (float)($booking->deposit_amount ?? ($totalMoneyRow * $defaultDepositPercent));
                                            $depositLabel = $isMonthly ? 'Đã cọc 50%' : 'Đã cọc 30%';
                                        }

                                        $rfStatus = $booking->refund_status ?? 'none';
                                        $bookingCodeText = $booking->booking_code ?? $booking->code ?? '#'.$booking->id;
                                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" . urlencode($bookingCodeText);
                                    @endphp

                                    <tr>
                                        {{-- CỘT MÃ ĐƠN --}}
                                        <td class="ps-4">
                                            @if($status === 'pending')
                                                <a href="{{ route('user.payment.show', $booking->id) }}" class="fw-bold text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-hash small"></i>{{ $booking->id }} 
                                                    <span class="badge bg-warning text-dark px-1.5 py-0.5" style="font-size: 0.65rem;">Thanh toán ngay</span>
                                                </a>
                                            @else
                                                <div class="fw-bold text-dark d-inline-flex align-items-center gap-1">
                                                    <i class="bi bi-hash small text-muted"></i>{{ $booking->id }}
                                                </div>
                                            @endif

                                            @if($isMonthly)
                                                <span class="badge bg-success text-white ms-1" style="font-size: 0.65rem;">Lịch tháng</span>
                                            @endif

                                            @if(!empty($booking->booking_code))
                                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-qr-code me-1"></i>{{ $booking->booking_code }}
                                                </small>
                                            @endif

                                            @if($status !== 'pending')
                                                @if($isPaidFull)
                                                    <small class="text-success d-block mt-1 fw-bold" style="font-size: 0.72rem;">
                                                        <i class="bi bi-shield-check me-1"></i>Đã thanh toán 100%: {{ number_format($totalMoneyRow, 0, ',', '.') }}đ
                                                    </small>
                                                @else
                                                    <small class="text-primary d-block mt-1 fw-bold" style="font-size: 0.72rem;">
                                                        <i class="bi bi-shield-check me-1"></i>{{ $depositLabel }}: {{ number_format($depositAmount, 0, ',', '.') }}đ
                                                    </small>
                                                @endif
                                            @else
                                                <small class="text-danger d-block mt-1 fw-medium" style="font-size: 0.72rem;">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Chưa cọc: {{ number_format($depositAmount, 0, ',', '.') }}đ
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
                                                    {{ $startTime ? substr($startTime, 0, 5) : '' }} - {{ $endTime ? substr($endTime, 0, 5) : '' }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </td>

                                        <td class="fw-bold text-success fs-6">
                                            {{ number_format($totalMoneyRow, 0, ',', '.') }}đ
                                        </td>

                                        {{-- CỘT TRẠNG THÁI --}}
                                        <td>
                                            <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill fw-semibold">
                                                @if($status === 'pending') <i class="bi bi-hourglass-split me-1"></i> @endif
                                                @if($status === 'confirmed') <i class="bi bi-check-circle-fill me-1"></i> @endif
                                                @if($status === 'completed') <i class="bi bi-patch-check-fill me-1"></i> @endif
                                                @if($status === 'cancelled') <i class="bi bi-x-circle-fill me-1"></i> @endif
                                                {{ $statusText }}
                                            </span>

                                            @if($status === 'cancelled' && isset($booking->refund_amount) && $booking->refund_amount > 0)
                                                @if($rfStatus === 'completed')
                                                    <div class="mt-1">
                                                        <span class="badge bg-success text-white px-2 py-1" style="font-size: 0.7rem;">
                                                            <i class="bi bi-envelope-paper-check-fill me-1"></i> Đã gửi Bill
                                                        </span>
                                                    </div>
                                                @elseif($rfStatus === 'pending')
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.7rem;">
                                                            <i class="bi bi-clock me-1"></i> Đang chờ CK
                                                        </span>
                                                    </div>
                                                @elseif($rfStatus === 'confirmed_by_user')
                                                    <div class="mt-1">
                                                        <span class="badge bg-primary text-white px-2 py-1" style="font-size: 0.7rem;">
                                                            <i class="bi bi-check-all me-1"></i> Đã nhận đủ tiền
                                                        </span>
                                                    </div>
                                                @elseif($rfStatus === 'disputed')
                                                    <div class="mt-1">
                                                        <span class="badge bg-danger text-white px-2 py-1" style="font-size: 0.7rem;">
                                                            <i class="bi bi-exclamation-triangle me-1"></i> Phản hồi sự cố
                                                        </span>
                                                    </div>
                                                @endif
                                            @endif
                                        </td>

                                        {{-- CỘT THAO TÁC --}}
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-1 align-items-center flex-wrap justify-content-end booking-action-group">
                                                {{-- NÚT THANH TOÁN SỐ TIỀN CÒN LẠI (Chỉ hiện khi đã check-out / đá xong) --}}
                                                @if($remainingAmount > 0 && $isFinishedUsing && !in_array($status, ['cancelled', 'pending']))
                                                    <a href="{{ route('user.payment.show', $booking->id) }}" 
                                                       class="btn btn-sm btn-warning rounded-3 d-inline-flex align-items-center gap-1 fw-bold px-2.5 py-1.5 shadow-sm text-dark" 
                                                       title="Thanh toán số tiền còn lại">
                                                        <i class="bi bi-wallet2"></i> Thanh toán ({{ number_format($remainingAmount, 0, ',', '.') }}đ)
                                                    </a>
                                                @endif

                                                @if($status === 'pending')
                                                    <a href="{{ route('user.payment.show', $booking->id) }}" 
                                                       class="btn btn-sm btn-success rounded-3 d-inline-flex align-items-center gap-1 py-1.5 shadow-sm fw-medium" 
                                                       title="Thanh toán đơn hàng">
                                                        <i class="bi bi-credit-card-2-back-fill"></i> 
                                                    </a>
                                                @endif

                                                @if($status !== 'cancelled')
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success rounded-3 d-inline-flex align-items-center gap-1 py-1.5"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#checkinModal{{ $booking->id }}"
                                                            title="Mở Thẻ Check-in / Vé Vào Sân">
                                                        <i class="bi bi-qr-code-scan"></i>
                                                    </button>
                                                @endif

                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info rounded-3 d-inline-flex align-items-center gap-1 py-1.5"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#detailModal{{ $booking->id }}"
                                                        title="Xem chi tiết đơn & Dịch vụ">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>

                                                @if(\Illuminate\Support\Facades\Route::has('user.bookings.invoice'))
                                                    <a href="{{ route('user.bookings.invoice', $booking->id) }}"
                                                       class="btn btn-sm invoice-action rounded-3"
                                                       target="_blank"
                                                       title="Xem và in hóa đơn">
                                                        <i class="bi bi-receipt-cutoff"></i>
                                                    </a>
                                                @endif

                                                @if($status === 'completed')
                                                    <a href="{{ route('user.bookings.show', $booking->id) }}" 
                                                       class="btn btn-sm btn-outline-warning rounded-3 d-inline-flex align-items-center gap-1 py-1.5"
                                                       title="Đánh giá đơn hoàn thành">
                                                        <i class="bi bi-star-fill"></i>
                                                    </a>
                                                @endif

                                                {{-- NÚT HỦY ĐƠN --}}
                                                @if(in_array($status, ['pending', 'confirmed']))
                                                    @if(!$isMonthly)
                                                        @php
                                                            $mDate = \Carbon\Carbon::parse(($bookingDate ?? now()->format('Y-m-d')) . ' ' . ($startTime ?? '00:00:00'));
                                                            $hrs = \Carbon\Carbon::now()->diffInHours($mDate, false);
                                                            $totMoney = $totalMoneyRow;

                                                            $estRefund = 0;
                                                            $policyText = '';

                                                            if ($status === 'pending') {
                                                                $policyText = 'Đơn chưa thanh toán, hủy không mất phí.';
                                                            } elseif ($hrs >= 24) {
                                                                if ($isPaidFull) {
                                                                    $estRefund = $totMoney * 0.70;
                                                                    $policyText = 'Hủy trước 24h bóng lăn (Đã thanh toán 100%): Hoàn 70% tổng tiền sân (' . number_format($estRefund, 0, ',', '.') . 'đ)';
                                                                } else {
                                                                    $actualDeposit = $depositAmount > 0 ? $depositAmount : ($totMoney * 0.30);
                                                                    $estRefund = $actualDeposit * 0.50;
                                                                    $policyText = 'Hủy trước 24h bóng lăn (' . $depositLabel . '): Hoàn 50% số tiền đã cọc (' . number_format($estRefund, 0, ',', '.') . 'đ)';
                                                                }
                                                            } else {
                                                                if ($isPaidFull) {
                                                                    $estRefund = $totMoney * 0.30;
                                                                    $policyText = 'Hủy sát giờ < 24h bóng lăn (Đã thanh toán 100%): Nhận lại 30% tổng tiền sân (' . number_format($estRefund, 0, ',', '.') . 'đ)';
                                                                } else {
                                                                    $estRefund = 0;
                                                                    $policyText = 'Hủy sát giờ < 24h bóng lăn (' . $depositLabel . '): Mất 100% số tiền đã cọc (Hoàn 0đ)';
                                                                }
                                                            }
                                                        @endphp

                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger rounded-3 d-inline-flex align-items-center py-1.5"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#cancelModal{{ $booking->id }}"
                                                                title="Hủy đơn đặt sân">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>

                                                        <!-- MODAL XÁC NHẬN HỦY SÂN -->
                                                        <div class="modal fade text-start" id="cancelModal{{ $booking->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                                    <form action="{{ route('user.bookings.destroy', $booking->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')

                                                                        <div class="modal-header border-0 bg-danger-subtle p-4 pb-3">
                                                                            <div class="d-flex align-items-center gap-3">
                                                                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" 
                                                                                     style="width: 44px; height: 44px;">
                                                                                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="modal-title fw-bold text-danger mb-0">Xác nhận hủy đặt sân</h5>
                                                                                    <small class="text-danger-emphasis">Đơn đặt sân #{{ $booking->id }}</small>
                                                                                </div>
                                                                            </div>
                                                                            <button type="button" class="btn-close align-self-start" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>

                                                                        <div class="modal-body p-4">
                                                                            <div class="rounded-4 p-3.5 border {{ $estRefund > 0 ? 'bg-success-subtle border-success-subtle' : 'bg-light border-secondary-subtle' }} mb-3">
                                                                                <div class="d-flex align-items-start gap-2.5">
                                                                                    <i class="bi {{ $estRefund > 0 ? 'bi-check-circle-fill text-success' : 'bi-info-circle-fill text-secondary' }} fs-5 mt-0.5"></i>
                                                                                    <div>
                                                                                        <span class="d-block text-muted small fw-medium mb-1" style="font-size: 0.78rem;">Quy định hoàn tiền áp dụng:</span>
                                                                                        <div class="fw-bold fs-6 {{ $estRefund > 0 ? 'text-success-emphasis' : 'text-dark' }}">
                                                                                            {{ $policyText }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            @if($estRefund > 0)
                                                                                <div class="alert alert-success d-flex align-items-center justify-content-between p-3 rounded-3 mb-3 shadow-sm border-0">
                                                                                    <span class="small fw-medium"><i class="bi bi-wallet2 me-1.5"></i> Số tiền Admin sẽ chuyển hoàn lại:</span>
                                                                                    <strong class="fs-5">{{ number_format($estRefund, 0, ',', '.') }}đ</strong>
                                                                                </div>

                                                                                <div class="card border rounded-4 p-3 mb-2 bg-body-tertiary">
                                                                                    <h6 class="fw-bold text-dark mb-3">
                                                                                        <i class="bi bi-bank me-1.5 text-primary"></i> Nhập thông tin tài khoản nhận tiền hoàn:
                                                                                    </h6>

                                                                                    <div class="row g-3">
                                                                                        <div class="col-md-6">
                                                                                            <label class="form-label small fw-semibold text-secondary">Tên ngân hàng <span class="text-danger">*</span></label>
                                                                                            
                                                                                            <!-- Input kết hợp datalist giúp vừa chọn vừa gõ tìm kiếm tự động -->
                                                                                            <input type="text" name="bank_name" class="form-control rounded-3" list="bankList" placeholder="Gõ để tìm hoặc chọn ngân hàng..." required autocomplete="off">
                                                                                            
                                                                                            <datalist id="bankList">
                                                                                                <option value="MB Bank (Ngân hàng Quân Đội)">
                                                                                                <option value="Vietcombank (TMCP Ngoại Thương Việt Nam)">
                                                                                                <option value="Techcombank (TMCP Kỹ Thương Việt Nam)">
                                                                                                <option value="BIDV (Đầu tư và Phát triển Việt Nam)">
                                                                                                <option value="Agribank (Nông nghiệp và PTNT Việt Nam)">
                                                                                                <option value="ACB (TMCP Á Châu)">
                                                                                                <option value="VPBank (TMCP Việt Nam Thịnh Vượng)">
                                                                                                <option value="TPBank (TMCP Tiên Phong)">
                                                                                                <option value="Sacombank (TMCP Sài Gòn Thương Tín)">
                                                                                                <option value="SHB (TMCP Sài Gòn - Hà Nội)">
                                                                                                <option value="HDBank (TMCP Phát triển TP.HCM)">
                                                                                                <option value="VIB (TMCP Quốc Tế Việt Nam)">
                                                                                                <option value="SeABank (TMCP Đông Nam Á)">
                                                                                                <option value="OCB (TMCP Phương Đông)">
                                                                                                <option value="Eximbank (TMCP Xuất Nhập Khẩu)">
                                                                                                <option value="MSB (TMCP Hàng Hải Việt Nam)">
                                                                                                <option value="Nam A Bank (TMCP Nam Á)">
                                                                                            </datalist>
                                                                                        </div>

                                                                                        <div class="col-md-6">
                                                                                            <label class="form-label small fw-semibold text-secondary">Số tài khoản <span class="text-danger">*</span></label>
                                                                                            <input type="text" name="bank_account_number" class="form-control rounded-3" placeholder="Nhập số tài khoản ngân hàng" required>
                                                                                        </div>

                                                                                        <div class="col-md-12">
                                                                                            <label class="form-label small fw-semibold text-secondary">Tên chủ tài khoản (Viết hoa không dấu) <span class="text-danger">*</span></label>
                                                                                            <input type="text" name="bank_account_holder" class="form-control rounded-3 text-uppercase" placeholder="NGUYEN VAN A" required>
                                                                                        </div>

                                                                                        <div class="col-md-12">
                                                                                            <label class="form-label small fw-semibold text-secondary">Lý do hủy sân (Không bắt buộc)</label>
                                                                                            <textarea name="cancel_reason" class="form-control rounded-3" rows="2" placeholder="Nhập lý do hủy sân..."></textarea>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @else
                                                                                <p class="fs-6 text-dark mb-1">
                                                                                    Bạn có chắc chắn muốn hủy đơn đặt sân này không? Do đơn chưa thanh toán hoặc đã sát giờ nên sẽ <strong class="text-danger">không có tiền hoàn lại</strong>.
                                                                                </p>
                                                                            @endif
                                                                        </div>

                                                                        <div class="modal-footer border-0 p-4 pt-3 d-flex justify-content-end gap-2">
                                                                            <button type="button" class="btn btn-light rounded-3 px-4 fw-medium border" data-bs-dismiss="modal">
                                                                                Bỏ qua
                                                                            </button>
                                                                            <button type="submit" class="btn btn-danger rounded-3 px-4 fw-medium shadow-sm">
                                                                                <i class="bi bi-trash3-fill me-1"></i> {{ $estRefund > 0 ? 'Gửi yêu cầu hủy & Hoàn tiền' : 'Xác nhận Hủy' }}
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- KHÓA HỦY NẾU LÀ ĐƠN THÁNG --}}
                                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1.5 rounded-3 fw-normal" 
                                                              title="Đơn đặt lịch cố định tháng không hỗ trợ hủy hay hoàn tiền">
                                                            <i class="bi bi-lock-fill text-danger me-1"></i> Đơn tháng
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- MODAL THẺ CHECK-IN / VÉ ĐIỆN TỬ -->
                                            <div class="modal fade text-start" id="checkinModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" id="printableTicket{{ $booking->id }}">
                                                        
                                                        <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #198754 0%, #0d5132 100%);">
                                                            <div class="d-flex align-items-center justify-content-between w-100 me-2">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <i class="bi bi-ticket-perforated-fill fs-2"></i>
                                                                    <div>
                                                                        <h5 class="modal-title fw-bold mb-0 text-white">THẺ CHECK-IN SÂN BÓNG</h5>
                                                                        <small class="text-white-50">Xuất trình thẻ này cho quản lý sân khi nhận sân</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body p-4 bg-light">
                                                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
                                                                <div class="card-body p-4 text-center bg-white">
                                                                    
                                                                    <div class="d-inline-block p-3 bg-light rounded-4 border mb-2 shadow-sm">
                                                                        <img src="{{ $qrUrl }}" alt="Mã QR Checkin" class="img-fluid rounded-3" style="width: 160px; height: 180px;">
                                                                    </div>

                                                                    <div class="fw-bold fs-5 text-dark letter-spacing-1 font-monospace mt-1">
                                                                        {{ $bookingCodeText }}
                                                                    </div>
                                                                    <small class="text-muted d-block mb-3">Quét mã QR để xác nhận và in hóa đơn tại quầy</small>

                                                                    <hr class="my-3 border-dashed">

                                                                    <div class="text-start">
                                                                        <div class="row g-3">
                                                                            <div class="col-6">
                                                                                <small class="text-muted d-block">Tên sân:</small>
                                                                                <strong class="text-dark fs-6">{{ $booking->field_name ?? 'Sân bóng' }}</strong>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <small class="text-muted d-block">Khung giờ đá:</small>
                                                                                <strong class="text-primary fs-6">{{ $startTime ? substr($startTime, 0, 5) : '-' }} - {{ $endTime ? substr($endTime, 0, 5) : '-' }}</strong>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <small class="text-muted d-block">Ngày đá sân:</small>
                                                                                <strong class="text-dark">{{ $bookingDate ? \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') : '-' }}</strong>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <small class="text-muted d-block">Người đặt:</small>
                                                                                <strong class="text-dark">{{ Auth::user()->name }}</strong>
                                                                            </div>
                                                                            <div class="col-12">
                                                                                <small class="text-muted d-block">Tình trạng thanh toán:</small>
                                                                                @if($status === 'pending')
                                                                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill">Chưa thanh toán ({{ number_format($totalMoneyRow, 0, ',', '.') }}đ)</span>
                                                                                @elseif($isPaidFull)
                                                                                    <span class="badge bg-success text-white px-3 py-1.5 rounded-pill"><i class="bi bi-check-circle me-1"></i>Đã thanh toán 100% ({{ number_format($totalMoneyRow, 0, ',', '.') }}đ)</span>
                                                                                @else
                                                                                    <span class="badge bg-primary text-white px-3 py-1.5 rounded-pill"><i class="bi bi-shield-check me-1"></i>{{ $depositLabel }} ({{ number_format($depositAmount, 0, ',', '.') }}đ)</span>
                                                                                    <span class="small text-danger ms-1 fw-bold">Còn thiếu tại sân: {{ number_format($totalMoneyRow - $depositAmount, 0, ',', '.') }}đ</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer border-0 p-3 bg-white justify-content-between">
                                                            <button type="button" class="btn btn-light rounded-3 border" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="button" class="btn btn-success rounded-3 px-4 shadow-sm" onclick="printCheckinPass('printableTicket{{ $booking->id }}')">
                                                                <i class="bi bi-printer-fill me-1.5"></i> In Vé / Hóa Đơn
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- MODAL CHI TIẾT (ĐẦY ĐỦ MÃ TRA CỨU, TRẠNG THÁI, TỔNG TIỀN, THÔNG TIN NGƯỜI ĐẶT & FORM BÁO SỰ CỐ GỬI ẢNH) -->
                                            <div class="modal fade text-start" id="detailModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                                        <div class="modal-header border-0 bg-primary-subtle p-3.5 px-4">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <i class="bi bi-receipt fs-4 text-primary"></i>
                                                                <h5 class="modal-title fw-bold text-primary mb-0">Chi tiết đơn đặt sân #{{ $booking->id }}</h5>
                                                            </div>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>

                                                        <div class="modal-body p-4">
                                                            
                                                            {{-- TIẾN ĐỘ HOÀN TIỀN VÀ 2 NÚT XÁC NHẬN / BÁO SỰ CỐ KÈM FORM ẢNH --}}
                                                            @if($status === 'cancelled' && isset($booking->refund_amount) && $booking->refund_amount > 0)
                                                                <div class="card border-danger-subtle bg-danger-subtle bg-opacity-10 rounded-4 p-3 mb-4">
                                                                    <div class="d-flex align-items-center justify-content-between mb-2 border-bottom border-danger-subtle pb-2">
                                                                        <h6 class="fw-bold text-danger mb-0">
                                                                            <i class="bi bi-wallet2 me-1"></i> Tiến độ hoàn tiền (Số tiền hoàn: {{ number_format($booking->refund_amount, 0, ',', '.') }}đ)
                                                                        </h6>
                                                                    </div>
                                                                    @if($rfStatus === 'pending')
                                                                        <div class="alert alert-warning border-0 rounded-3 py-2 px-3 small mb-2">
                                                                            <i class="bi bi-hourglass-split me-1"></i> Admin đang rà soát thông tin STK và tiến hành chuyển khoản hoàn tiền. Vui lòng chờ!
                                                                        </div>
                                                                    @elseif($rfStatus === 'completed')
                                                                        <div class="alert alert-success border-0 rounded-3 py-2 px-3 small mb-2">
                                                                            <i class="bi bi-check-circle-fill me-1"></i> Admin đã xác nhận chuyển khoản hoàn tiền thành công!
                                                                        </div>
                                                                        @if(!empty($booking->refund_proof_image))
                                                                            <div class="text-center p-3 bg-white border rounded-3 mb-3 shadow-sm">
                                                                                <span class="small text-muted d-block mb-2 fw-semibold">Hóa đơn chuyển khoản từ Admin:</span>
                                                                                <a href="{{ asset($booking->refund_proof_image) }}" target="_blank">
                                                                                    <img src="{{ asset($booking->refund_proof_image) }}" alt="Bill chuyển khoản" class="img-thumbnail rounded-3" style="max-height: 180px;">
                                                                                </a>
                                                                            </div>
                                                                        @endif

                                                                        <div class="d-flex gap-2 mt-3">
                                                                            @if(\Illuminate\Support\Facades\Route::has('user.bookings.confirmRefund'))
                                                                                <form action="{{ route('user.bookings.confirmRefund', $booking->id) }}" method="POST" class="flex-grow-1">
                                                                                    @csrf
                                                                                    <button type="submit" class="btn btn-success btn-sm w-100 fw-bold shadow-sm rounded-3 py-2">
                                                                                        <i class="bi bi-check-circle-fill me-1"></i> Tôi đã nhận đủ tiền
                                                                                    </button>
                                                                                </form>
                                                                            @endif

                                                                            @if(\Illuminate\Support\Facades\Route::has('user.bookings.disputeRefundWithImage'))
                                                                                <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-bold shadow-sm rounded-3 py-2" data-bs-toggle="collapse" data-bs-target="#disputeForm{{ $booking->id }}">
                                                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Báo sự cố (Chưa nhận được tiền)
                                                                                </button>
                                                                            @endif
                                                                        </div>

                                                                        @if(\Illuminate\Support\Facades\Route::has('user.bookings.disputeRefundWithImage'))
                                                                            <div class="collapse mt-3" id="disputeForm{{ $booking->id }}">
                                                                                <div class="card card-body border bg-white rounded-3 shadow-sm">
                                                                                    <form action="{{ route('user.bookings.disputeRefundWithImage', $booking->id) }}" method="POST" enctype="multipart/form-data">
                                                                                        @csrf
                                                                                        <div class="mb-2">
                                                                                            <label class="form-label small fw-semibold text-secondary">Nội dung báo cáo sự cố:</label>
                                                                                            <textarea name="dispute_reason" class="form-control form-control-sm rounded-3" rows="2" placeholder="Nhập lý do chưa nhận được tiền..." required></textarea>
                                                                                        </div>
                                                                                        <div class="mb-2">
                                                                                            <label class="form-label small fw-semibold text-secondary">Đính kèm ảnh (nếu có):</label>
                                                                                            <input type="file" name="dispute_image" class="form-control form-control-sm rounded-3" accept="image/*">
                                                                                        </div>
                                                                                        <button type="submit" class="btn btn-danger btn-sm w-100 fw-bold rounded-3">
                                                                                            Gửi báo cáo sự cố về Admin
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                    @elseif($rfStatus === 'confirmed_by_user')
                                                                        <div class="alert alert-primary border-0 rounded-3 py-2 px-3 small mb-0">
                                                                            <i class="bi bi-check-all me-1"></i> Bạn đã xác nhận nhận đủ tiền hoàn. Cảm ơn bạn!
                                                                        </div>
                                                                    @elseif($rfStatus === 'disputed')
                                                                        <div class="alert alert-danger border-0 rounded-3 py-2 px-3 small mb-0">
                                                                            <i class="bi bi-exclamation-triangle me-1"></i> Bạn đã báo cáo sự cố về tiền hoàn. Admin sẽ kiểm tra lại giao dịch!
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            {{-- KHỐI 3 Ô THÔNG TIN: MÃ TRA CỨU, TRẠNG THÁI, TỔNG TIỀN THỰC TẾ --}}
                                                            <div class="row g-3 mb-4">
                                                                <div class="col-md-4">
                                                                    <div class="p-3 bg-light rounded-3 border h-100">
                                                                        <span class="text-muted small d-block mb-1">Mã tra cứu:</span>
                                                                        <strong class="text-dark small font-monospace">{{ $booking->booking_code ?? $booking->code ?? '#'.$booking->id }}</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="p-3 bg-light rounded-3 border h-100">
                                                                        <span class="text-muted small d-block mb-1">Trạng thái:</span>
                                                                        <span class="badge {{ $statusClass }} px-2.5 py-1 fw-semibold">{{ $statusText }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="p-3 bg-light rounded-3 border h-100">
                                                                        <span class="text-muted small d-block mb-1">Tổng tiền thực tế:</span>
                                                                        <strong class="text-success fs-6">{{ number_format($totalMoneyRow, 0, ',', '.') }}đ</strong>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- 1. FORM THÊM GIỜ --}}
                                                            @if($status === 'confirmed')
                                                                <div class="p-3 bg-light border border-warning-subtle rounded-4 mb-3">
                                                                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-clock-history text-warning me-1"></i> Bạn muốn đá thêm giờ?</h6>
                                                                    <form action="{{ route('user.bookings.add-extra-time', $booking->id) }}" method="POST" onsubmit="return confirm('Xác nhận gia hạn thêm giờ cho sân này?');">
                                                                        @csrf
                                                                        <div class="row g-2 align-items-center">
                                                                            <div class="col-sm-7">
                                                                                <select name="duration_minutes" class="form-select form-select-sm rounded-3 fw-semibold">
                                                                                    <option value="30">Gia hạn thêm 30 phút</option>
                                                                                    <option value="60" selected>Gia hạn thêm 1 tiếng</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-sm-5">
                                                                                <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark w-100 rounded-3 shadow-sm py-1.5">
                                                                                    <i class="bi bi-plus-circle me-1"></i> Xác nhận thêm giờ
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endif

                                                            {{-- 2. FORM GỌI THÊM ĐỒ UỐNG / DỊCH VỤ --}}
                                                            @if(in_array($status, ['confirmed', 'pending']))
                                                                @php
                                                                    $availableServices = DB::table('services')->where('status', true)->get();
                                                                @endphp
                                                                <div class="p-3 bg-light border border-primary-subtle rounded-4 mb-4">
                                                                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-basket text-primary me-1"></i> Gọi thêm đồ uống / dịch vụ?</h6>
                                                                    <form action="{{ route('user.bookings.add-service', $booking->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="row g-2 align-items-center">
                                                                            <div class="col-sm-6">
                                                                                <select name="service_id" class="form-select form-select-sm rounded-3 fw-semibold" required>
                                                                                    <option value="">-- Chọn dịch vụ --</option>
                                                                                    @foreach($availableServices as $srv)
                                                                                        <option value="{{ $srv->id }}">{{ $srv->name }} ({{ number_format($srv->price, 0, ',', '.') }}đ)</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-sm-3">
                                                                                <input type="number" name="quantity" class="form-control form-control-sm rounded-3 text-center fw-bold" value="1" min="1" max="20" required>
                                                                            </div>
                                                                            <div class="col-sm-3">
                                                                                <button type="submit" class="btn btn-primary btn-sm fw-bold w-100 rounded-3 shadow-sm py-1.5">
                                                                                    <i class="bi bi-plus-circle me-1"></i> Thêm đồ
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            @endif

                                                            {{-- 3. BẢNG HIỂN THỊ TẤT CẢ CÁC SÂN VÀ KHUNG GIỜ --}}
                                                            <h6 class="fw-bold text-dark mb-2">
                                                                <i class="bi bi-dribbble text-success me-1"></i> Thông tin sân đặt
                                                            </h6>
                                                            <div class="table-responsive rounded-3 border mb-3">
                                                                <table class="table table-striped align-middle mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Tên Sân</th>
                                                                            <th>Ngày Đá</th>
                                                                            <th>Khung Giờ</th>
                                                                            <th class="text-end">Giá Tiền Sân</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $detailsList = DB::table('booking_details')
                                                                                ->leftJoin('fields', 'booking_details.field_id', '=', 'fields.id')
                                                                                ->leftJoin('time_slots', 'booking_details.time_slot_id', '=', 'time_slots.id')
                                                                                ->where('booking_details.booking_id', $booking->id)
                                                                                ->select('booking_details.*', 'fields.name as field_name', 'time_slots.start_time', 'time_slots.end_time')
                                                                                ->get();
                                                                        @endphp
                                                                        @foreach($detailsList as $detailItem)
                                                                            <tr>
                                                                                <td class="fw-bold text-dark">{{ $detailItem->field_name ?? 'Sân bóng' }}</td>
                                                                                <td>{{ \Carbon\Carbon::parse($detailItem->booking_date)->format('d/m/Y') }}</td>
                                                                                <td>
                                                                                    <span class="badge bg-white text-dark border px-2.5 py-1">
                                                                                        {{ substr($detailItem->start_time ?? '', 0, 5) }} - {{ substr($detailItem->end_time ?? '', 0, 5) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td class="text-end fw-bold text-success">
                                                                                    {{ number_format($detailItem->price, 0, ',', '.') }}đ
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            {{-- 4. BẢNG HIỂN THỊ DỊCH VỤ / ĐỒ UỐNG KHÁCH ĐÃ DÙNG --}}
                                                            @php
                                                                $bookingServicesList = DB::table('booking_services')
                                                                    ->leftJoin('services', 'booking_services.service_id', '=', 'services.id')
                                                                    ->where('booking_services.booking_id', $booking->id)
                                                                    ->select('booking_services.*', 'services.name as service_name', 'services.unit as service_unit')
                                                                    ->get();
                                                            @endphp

                                                            @if($bookingServicesList->isNotEmpty())
                                                                <h6 class="fw-bold text-dark mb-2 mt-4">
                                                                    <i class="bi bi-basket text-primary me-1"></i> Dịch vụ / Đồ uống đã dùng
                                                                </h6>
                                                                <div class="table-responsive rounded-3 border mb-4">
                                                                    <table class="table table-striped align-middle mb-0">
                                                                        <thead class="table-light">
                                                                            <tr>
                                                                                <th>Tên dịch vụ</th>
                                                                                <th>Số lượng</th>
                                                                                <th>Đơn giá</th>
                                                                                <th class="text-end">Thành tiền</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($bookingServicesList as $srvItem)
                                                                                <tr>
                                                                                    <td class="fw-semibold text-dark">{{ $srvItem->service_name ?? 'Dịch vụ' }}</td>
                                                                                    <td>{{ $srvItem->quantity }} {{ $srvItem->service_unit ?? '' }}</td>
                                                                                    <td>{{ number_format($srvItem->price, 0, ',', '.') }}đ</td>
                                                                                    <td class="text-end fw-bold text-success">
                                                                                        {{ number_format($srvItem->total ?? ($srvItem->price * $srvItem->quantity), 0, ',', '.') }}đ
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif

                                                            {{-- 5. THÔNG TIN NGƯỜI ĐẶT --}}
                                                            <div class="p-3 bg-light rounded-3 border mt-4">
                                                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-lines-fill text-primary me-1"></i> Thông tin người đặt</h6>
                                                                <div class="row g-2 small text-secondary">
                                                                    <div class="col-6"><strong>Họ tên:</strong> {{ Auth::user()->name ?? 'Khách hàng' }}</div>
                                                                    <div class="col-6"><strong>SDT:</strong> {{ Auth::user()->phone ?? 'Chưa cập nhật' }}</div>
                                                                    <div class="col-6"><strong>Email:</strong> {{ Auth::user()->email ?? 'Chưa có email' }}</div>
                                                                    <div class="col-6"><strong>Ngày đặt:</strong> {{ !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : '-' }}</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer border-0 p-3 pt-0 d-flex justify-content-between">
                                                            @if(\Illuminate\Support\Facades\Route::has('user.bookings.invoice'))
                                                                <a href="{{ route('user.bookings.invoice', $booking->id) }}"
                                                                   class="btn btn-outline-dark btn-sm rounded-3 px-3"
                                                                   target="_blank">
                                                                    <i class="bi bi-receipt-cutoff me-1"></i> Xem hóa đơn
                                                                </a>
                                                            @endif
                                                            <button type="button" class="btn btn-secondary btn-sm rounded-3 px-4" data-bs-dismiss="modal">Đóng</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                            <h6 class="fw-semibold">Bạn chưa có đơn đặt sân nào</h6>
                                            <a href="{{ route('home') }}" class="btn btn-primary rounded-3 mt-2">
                                                <i class="bi bi-plus-circle me-1"></i> Đặt sân ngay
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

<script>
function printCheckinPass(elementId) {
    var printContents = document.getElementById(elementId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = `
        <div style="max-width: 450px; margin: 20px auto; font-family: sans-serif;">
            ${printContents}
        </div>
    `;

    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>

@endsection