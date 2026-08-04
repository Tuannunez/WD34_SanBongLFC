@extends('admin.layouts.app')

@section('title', 'Quét mã đơn check-in')

@push('styles')
<style>
    .scanner-card {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 14px 38px rgba(15, 23, 42, .08);
    }

    .scanner-icon {
        width: 64px;
        height: 64px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #0f172a);
        font-size: 28px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .info-row:last-child {
        border-bottom: 0;
    }

    .booking-code-input {
        min-height: 54px;
        font-size: 18px;
        font-weight: 700;
        letter-spacing: .4px;
    }
</style>
@endpush

@section('content')
@php
    $bookingCode = old(
        'booking_code',
        request('booking_code', $booking->booking_code ?? $booking->code ?? '')
    );

    $customerName = data_get($booking, 'user.name')
        ?? data_get($booking, 'customer_name')
        ?? data_get($booking, 'name')
        ?? 'Khách hàng';

    $customerPhone = data_get($booking, 'customer_phone')
        ?? data_get($booking, 'phone')
        ?? data_get($booking, 'user.phone')
        ?? '-';

    $firstDetail = $booking
        ? collect($booking->bookingDetails ?? [])->first()
        : null;

    $fieldName = data_get($firstDetail, 'field.name')
        ?? data_get($firstDetail, 'field.field_name')
        ?? data_get($firstDetail, 'field_name')
        ?? 'Chưa xác định';

    $status = strtolower((string) data_get($booking, 'status', 'pending'));
    $usageStatus = strtolower((string) data_get(
        $booking,
        'usage_status',
        'not_checked_in'
    ));

    $statusText = match ($status) {
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        default => 'Chờ xử lý',
    };

    $usageText = match ($usageStatus) {
        'checked_in' => 'Đang hoạt động',
        'checked_out' => 'Đã check-out',
        default => 'Chưa check-in',
    };
@endphp

<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Mô phỏng quét mã đơn</h3>
            <p class="text-muted mb-0">
                Nhập mã đơn khách cung cấp để kiểm tra và xác nhận sân hoạt động.
            </p>
        </div>

        <a href="{{ route('admin.bookings.index') }}" class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Danh sách đơn
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @if($lookupError)
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-search me-2"></i>
            {{ $lookupError }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card scanner-card h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="scanner-icon mb-4">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>

                    <h4 class="fw-bold">Nhập mã đơn</h4>
                    <p class="text-muted">
                        Sao chép mã từ trang đơn của khách rồi dán vào ô bên dưới.
                    </p>

                    <form
                        method="GET"
                        action="{{ route('admin.bookings.check-in.index') }}"
                    >
                        <label for="booking_code" class="form-label fw-semibold">
                            Mã đơn đặt sân
                        </label>

                        <input
                            type="text"
                            id="booking_code"
                            name="booking_code"
                            class="form-control booking-code-input text-uppercase"
                            value="{{ $bookingCode }}"
                            placeholder="Ví dụ: BK202608030001"
                            autocomplete="off"
                            autofocus
                            required
                        >

                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                            <i class="bi bi-search me-1"></i>
                            Kiểm tra mã đơn
                        </button>
                    </form>

                    <div class="alert alert-light border rounded-4 mt-4 mb-0 small">
                        <i class="bi bi-shield-check text-primary me-1"></i>
                        Hệ thống chỉ cho check-in khi khách đã thanh toán đủ,
                        đơn còn hiệu lực và chưa hết giờ sân.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            @if($booking)
                <div class="card scanner-card">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <div class="text-muted small">Mã đơn</div>
                                <h4 class="fw-bold mb-1">
                                    {{ $booking->booking_code ?? $booking->code ?? '#'.$booking->id }}
                                </h4>
                                <div class="text-muted">Đơn #{{ $booking->id }}</div>
                            </div>

                            <span class="badge {{ $usageStatus === 'checked_in' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                {{ $usageText }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="text-muted">Khách hàng</span>
                                    <strong class="text-end">{{ $customerName }}</strong>
                                </div>
                                <div class="info-row">
                                    <span class="text-muted">Số điện thoại</span>
                                    <strong class="text-end">{{ $customerPhone }}</strong>
                                </div>
                                <div class="info-row">
                                    <span class="text-muted">Sân</span>
                                    <strong class="text-end">{{ $fieldName }}</strong>
                                </div>
                                <div class="info-row">
                                    <span class="text-muted">Trạng thái đơn</span>
                                    <strong class="text-end">{{ $statusText }}</strong>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="text-muted">Thanh toán</span>
                                    <strong class="text-end {{ ($checkIn['payment_full'] ?? false) ? 'text-success' : 'text-danger' }}">
                                        {{ ($checkIn['payment_full'] ?? false)
                                            ? 'Đã thanh toán đủ'
                                            : 'Chưa thanh toán đủ' }}
                                    </strong>
                                </div>
                                <div class="info-row">
                                    <span class="text-muted">Bắt đầu</span>
                                    <strong class="text-end">
                                        {{ ($checkIn['starts_at'] ?? null)?->format('H:i d/m/Y') ?? '-' }}
                                    </strong>
                                </div>
                                <div class="info-row">
                                    <span class="text-muted">Kết thúc</span>
                                    <strong class="text-end">
                                        {{ ($checkIn['ends_at'] ?? null)?->format('H:i d/m/Y') ?? '-' }}
                                    </strong>
                                </div>
                                <div class="info-row">
                                    <span class="text-muted">Check-in từ</span>
                                    <strong class="text-end">
                                        {{ ($checkIn['opens_at'] ?? null)?->format('H:i d/m/Y') ?? '-' }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="alert {{ ($checkIn['can_check_in'] ?? false) ? 'alert-success' : 'alert-warning' }} rounded-4 mt-4">
                            <i class="bi {{ ($checkIn['can_check_in'] ?? false) ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }} me-2"></i>
                            {{ $checkIn['message'] ?? 'Chưa kiểm tra điều kiện check-in.' }}
                        </div>

                        @if($checkIn['can_check_in'] ?? false)
                            <form
                                method="POST"
                                action="{{ route('admin.bookings.check-in.store') }}"
                                onsubmit="return confirm('Xác nhận khách đã đến sân và bắt đầu sử dụng sân?');"
                            >
                                @csrf
                                <input
                                    type="hidden"
                                    name="booking_code"
                                    value="{{ $booking->booking_code ?? $booking->code }}"
                                >

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-play-circle-fill me-1"></i>
                                    Xác nhận check-in — Sân hoạt động
                                </button>
                            </form>
                        @elseif($usageStatus === 'checked_in')
                            <a
                                href="{{ route('admin.bookings.show', $booking->id) }}"
                                class="btn btn-outline-primary btn-lg w-100"
                            >
                                <i class="bi bi-eye me-1"></i>
                                Xem đơn đang hoạt động
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="card scanner-card h-100">
                    <div class="card-body p-5 d-flex flex-column align-items-center justify-content-center text-center">
                        <i class="bi bi-ticket-perforated display-3 text-muted mb-3"></i>
                        <h4 class="fw-bold">Chưa có mã đơn</h4>
                        <p class="text-muted mb-0">
                            Nhập mã đơn ở bên trái để hiển thị thông tin khách và sân.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
