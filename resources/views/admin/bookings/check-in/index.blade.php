@extends('admin.layouts.app')

@section('title', 'Quét QR / Check-in đơn đặt sân')

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

    .qr-reader-wrap {
        overflow: hidden;
        border: 1px solid #dbe4f0;
        border-radius: 18px;
        background: #0f172a;
    }

    #qr-reader {
        width: 100%;
        min-height: 300px;
    }

    #qr-reader video {
        border-radius: 16px;
    }

    .camera-placeholder {
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 28px;
        color: #cbd5e1;
        text-align: center;
    }

    .camera-placeholder i {
        font-size: 52px;
        margin-bottom: 12px;
    }

    .amount-box {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 16px 18px;
        background: #f8fafc;
    }

    .amount-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 7px 0;
    }

    .amount-line.total {
        margin-top: 6px;
        padding-top: 13px;
        border-top: 1px dashed #cbd5e1;
        font-size: 17px;
    }

    .status-pill {
        border-radius: 999px;
        padding: 8px 13px;
        font-size: 13px;
        font-weight: 700;
    }

    .scanner-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    @media (max-width: 575.98px) {
        .scanner-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $booking = $booking ?? null;
    $checkIn = $checkIn ?? [];
    $lookupError = $lookupError ?? null;

    $bookingCode = old(
        'booking_code',
        request(
            'booking_code',
            data_get($booking, 'booking_code')
                ?? data_get($booking, 'code')
                ?? ''
        )
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

    $fieldNames = $booking
        ? collect($booking->bookingDetails ?? [])
            ->map(function ($detail) {
                return data_get($detail, 'field.name')
                    ?? data_get($detail, 'field.field_name')
                    ?? data_get($detail, 'field_name');
            })
            ->filter()
            ->unique()
            ->values()
        : collect();

    $fieldName = $fieldNames->isNotEmpty()
        ? $fieldNames->join(', ')
        : (
            data_get($firstDetail, 'field.name')
            ?? data_get($firstDetail, 'field.field_name')
            ?? data_get($firstDetail, 'field_name')
            ?? 'Chưa xác định'
        );

    $status = strtolower((string) data_get($booking, 'status', 'pending'));
    $usageStatus = strtolower((string) data_get(
        $booking,
        'usage_status',
        'not_checked_in'
    ));

    $paymentStatus = strtolower((string) data_get(
        $booking,
        'payment_status',
        'unpaid'
    ));

    $paymentType = strtolower((string) data_get(
        $booking,
        'payment_type',
        ''
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

    $usageBadgeClass = match ($usageStatus) {
        'checked_in' => 'bg-success-subtle text-success',
        'checked_out' => 'bg-primary-subtle text-primary',
        default => 'bg-secondary-subtle text-secondary',
    };

    $totalAmount = max(
        0,
        (float) (
            data_get($booking, 'final_amount')
            ?? data_get($booking, 'total_amount')
            ?? data_get($booking, 'total_price')
            ?? data_get($booking, 'total')
            ?? data_get($booking, 'amount')
            ?? 0
        )
    );

    $depositAmount = max(
        0,
        (float) data_get($booking, 'deposit_amount', 0)
    );

    $paidAmount = max(
        0,
        (float) data_get($booking, 'paid_amount', 0)
    );

    /*
     * Dự án hiện có trường hợp payment_status = paid nhưng payment_type = deposit.
     * Vì vậy KHÔNG mặc định "paid" là đã trả toàn bộ.
     */
    if (
        $paymentType === 'deposit'
        && $paidAmount <= 0
        && $depositAmount > 0
    ) {
        $paidAmount = $depositAmount;
    }

    if (
        in_array($paymentType, ['full', 'full_payment'], true)
        && in_array($paymentStatus, ['paid', 'paid_full', 'completed'], true)
        && $totalAmount > 0
    ) {
        $paidAmount = max($paidAmount, $totalAmount);
    }

    $paidAmount = $totalAmount > 0
        ? min($paidAmount, $totalAmount)
        : $paidAmount;

    $remainingAmount = max(0, $totalAmount - $paidAmount);

    $paymentFull = array_key_exists('payment_full', $checkIn)
        ? (bool) $checkIn['payment_full']
        : (
            $totalAmount > 0
            && $remainingAmount <= 0.01
        );

    $startsAt = $checkIn['starts_at'] ?? null;
    $endsAt = $checkIn['ends_at'] ?? null;
    $opensAt = $checkIn['opens_at'] ?? null;
    $deadlineAt = $checkIn['deadline_at'] ?? null;

    $canCheckIn = (bool) ($checkIn['can_check_in'] ?? false);

    /*
     * Controller mới có thể truyền can_pay_and_check_in.
     * Nếu controller cũ chưa có biến này thì giao diện không tự ý cho phép thu tiền.
     */
    $canPayAndCheckIn = (bool) ($checkIn['can_pay_and_check_in'] ?? false);

    $bookingCodeForSubmit = data_get($booking, 'booking_code')
        ?? data_get($booking, 'code')
        ?? data_get($booking, 'id')
        ?? '';
@endphp

<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-qr-code-scan me-2 text-primary"></i>
                Quét QR / Check-in
            </h3>
            <p class="text-muted mb-0">
                Quét QR bằng camera máy tính hoặc nhập mã đơn thủ công.
                Hệ thống kiểm tra thanh toán trước khi cho phép check-in.
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @if(!empty($lookupError))
        <div class="alert alert-danger rounded-4">
            <i class="bi bi-search me-2"></i>
            {{ $lookupError }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card scanner-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="scanner-icon flex-shrink-0">
                            <i class="bi bi-camera-video"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Camera quét QR</h4>
                            <div class="text-muted small">
                                QR đọc xong chỉ tra cứu đơn, không tự check-in ngay.
                            </div>
                        </div>
                    </div>

                    <div class="qr-reader-wrap">
                        <div id="camera-placeholder" class="camera-placeholder">
                            <i class="bi bi-camera-video-off"></i>
                            <strong>Camera chưa bật</strong>
                            <span class="small mt-1">
                                Bấm “Bật camera” rồi cho phép trình duyệt sử dụng webcam.
                            </span>
                        </div>

                        <div id="qr-reader" class="d-none"></div>
                    </div>

                    <div class="scanner-actions mt-3">
                        <button
                            type="button"
                            id="btn-start-camera"
                            class="btn btn-primary btn-lg"
                        >
                            <i class="bi bi-camera-video me-1"></i>
                            Bật camera
                        </button>

                        <button
                            type="button"
                            id="btn-stop-camera"
                            class="btn btn-outline-secondary btn-lg"
                            disabled
                        >
                            <i class="bi bi-stop-circle me-1"></i>
                            Tắt camera
                        </button>
                    </div>

                    <div
                        id="camera-status"
                        class="small text-muted mt-3"
                    >
                        Có thể dùng ô nhập mã bên dưới nếu camera không hoạt động.
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold">Nhập mã thủ công</h5>
                    <p class="text-muted small">
                        Dùng để demo hoặc dự phòng khi máy tính không đọc được QR.
                    </p>

                    <form
                        method="GET"
                        action="{{ route('admin.bookings.check-in.index') }}"
                        id="booking-lookup-form"
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
                            required
                        >

                        <button type="submit" class="btn btn-dark btn-lg w-100 mt-3">
                            <i class="bi bi-search me-1"></i>
                            Kiểm tra mã đơn
                        </button>
                    </form>

                    <div class="alert alert-light border rounded-4 mt-4 mb-0 small">
                        <i class="bi bi-shield-check text-primary me-1"></i>
                        Quét QR chỉ dùng để tìm đơn. Việc thanh toán/check-in phải được
                        nhân viên xác nhận ở bước tiếp theo.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            @if($booking)
                <div class="card scanner-card mb-4">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <div class="text-muted small">Mã đơn</div>
                                <h4 class="fw-bold mb-1">
                                    {{ data_get($booking, 'booking_code')
                                        ?? data_get($booking, 'code')
                                        ?? '#'.data_get($booking, 'id') }}
                                </h4>
                                <div class="text-muted">
                                    Đơn #{{ data_get($booking, 'id') }}
                                </div>
                            </div>

                            <span class="status-pill {{ $usageBadgeClass }}">
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
                                    <strong class="text-end {{ $paymentFull ? 'text-success' : 'text-danger' }}">
                                        {{ $paymentFull
                                            ? 'Đã thanh toán đủ'
                                            : 'Chưa thanh toán đủ' }}
                                    </strong>
                                </div>

                                <div class="info-row">
                                    <span class="text-muted">Bắt đầu</span>
                                    <strong class="text-end">
                                        {{ $startsAt?->format('H:i d/m/Y') ?? '-' }}
                                    </strong>
                                </div>

                                <div class="info-row">
                                    <span class="text-muted">Kết thúc</span>
                                    <strong class="text-end">
                                        {{ $endsAt?->format('H:i d/m/Y') ?? '-' }}
                                    </strong>
                                </div>

                                <div class="info-row">
                                    <span class="text-muted">Check-in từ</span>
                                    <strong class="text-end">
                                        {{ $opensAt?->format('H:i d/m/Y') ?? '-' }}
                                    </strong>
                                </div>

                                @if($deadlineAt)
                                    <div class="info-row">
                                        <span class="text-muted">Hạn check-in</span>
                                        <strong class="text-end text-danger">
                                            {{ $deadlineAt->format('H:i d/m/Y') }}
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="amount-box mt-4">
                            <div class="amount-line">
                                <span class="text-muted">Tổng tiền</span>
                                <strong>
                                    {{ number_format($totalAmount, 0, ',', '.') }}đ
                                </strong>
                            </div>

                            <div class="amount-line">
                                <span class="text-muted">Đã thanh toán</span>
                                <strong class="text-success">
                                    {{ number_format($paidAmount, 0, ',', '.') }}đ
                                </strong>
                            </div>

                            <div class="amount-line total">
                                <span class="fw-semibold">Còn phải thanh toán</span>
                                <strong class="{{ $remainingAmount > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($remainingAmount, 0, ',', '.') }}đ
                                </strong>
                            </div>
                        </div>

                        <div class="alert {{ $canCheckIn ? 'alert-success' : 'alert-warning' }} rounded-4 mt-4">
                            <i class="bi {{ $canCheckIn ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }} me-2"></i>
                            {{ $checkIn['message'] ?? 'Chưa kiểm tra đủ điều kiện check-in.' }}
                        </div>

                        @if($status === 'cancelled')
                            <div class="alert alert-danger rounded-4 mb-0">
                                <i class="bi bi-x-octagon-fill me-2"></i>
                                Đơn đã bị hủy. Không thể thanh toán hoặc check-in.
                            </div>

                        @elseif($status === 'completed' || $usageStatus === 'checked_out')
                            <div class="alert alert-primary rounded-4 mb-0">
                                <i class="bi bi-flag-fill me-2"></i>
                                Đơn đã hoàn tất và đã check-out.
                            </div>

                        @elseif($usageStatus === 'checked_in')
                            <div class="alert alert-success rounded-4 mb-0">
                                <i class="bi bi-play-circle-fill me-2"></i>
                                Khách đã check-in. Sân đang hoạt động.
                            </div>

                        @elseif($paymentFull && $canCheckIn)
                            <form
                                method="POST"
                                action="{{ route('admin.bookings.check-in.store') }}"
                                onsubmit="return confirm('Xác nhận khách đã đến sân và bắt đầu sử dụng sân?');"
                            >
                                @csrf

                                <input
                                    type="hidden"
                                    name="booking_code"
                                    value="{{ $bookingCodeForSubmit }}"
                                >

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-play-circle-fill me-1"></i>
                                    Xác nhận Check-in — Sân hoạt động
                                </button>
                            </form>

                        @elseif(!$paymentFull && $remainingAmount > 0 && $canPayAndCheckIn)
                            <div class="alert alert-warning rounded-4">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-cash-coin me-1"></i>
                                    Khách còn thiếu tiền
                                </div>
                                Cần thu thêm
                                <strong>{{ number_format($remainingAmount, 0, ',', '.') }}đ</strong>
                                trước khi check-in.
                            </div>

                            <form
                                method="POST"
                                action="{{ route('admin.bookings.check-in.store') }}"
                                onsubmit="return confirm('Xác nhận đã thu đủ {{ number_format($remainingAmount, 0, ',', '.') }}đ và check-in cho khách?');"
                            >
                                @csrf

                                <input
                                    type="hidden"
                                    name="booking_code"
                                    value="{{ $bookingCodeForSubmit }}"
                                >

                                <input
                                    type="hidden"
                                    name="payment_confirmed"
                                    value="1"
                                >

                                <div class="form-check border rounded-4 p-3 ps-5 mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        value="1"
                                        id="confirm_remaining_payment"
                                        required
                                    >

                                    <label
                                        class="form-check-label fw-semibold"
                                        for="confirm_remaining_payment"
                                    >
                                        Tôi xác nhận đã thu đủ
                                        {{ number_format($remainingAmount, 0, ',', '.') }}đ
                                        tại quầy.
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-cash-coin me-1"></i>
                                    Xác nhận thanh toán & Check-in
                                </button>
                            </form>

                        @elseif(!$paymentFull && $remainingAmount > 0)
                            <div class="alert alert-warning rounded-4 mb-0">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Đơn chưa thanh toán đủ
                                <strong>{{ number_format($remainingAmount, 0, ',', '.') }}đ</strong>.
                                Nếu khách đang trong thời gian được check-in, controller cần trả
                                <code>can_pay_and_check_in = true</code> để hiện nút
                                “Xác nhận thanh toán & Check-in”.
                            </div>

                        @else
                            <div class="alert alert-secondary rounded-4 mb-0">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Đơn hiện chưa đủ điều kiện để check-in.
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card scanner-card h-100">
                    <div class="card-body p-5 d-flex flex-column align-items-center justify-content-center text-center">
                        <i class="bi bi-ticket-perforated display-3 text-muted mb-3"></i>
                        <h4 class="fw-bold">Chưa có đơn được quét</h4>
                        <p class="text-muted mb-0">
                            Bật camera quét QR hoặc nhập mã đơn ở bên trái để hiển thị thông tin.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reader = document.getElementById('qr-reader');
    const placeholder = document.getElementById('camera-placeholder');
    const startButton = document.getElementById('btn-start-camera');
    const stopButton = document.getElementById('btn-stop-camera');
    const statusElement = document.getElementById('camera-status');
    const bookingCodeInput = document.getElementById('booking_code');
    const lookupForm = document.getElementById('booking-lookup-form');

    let scanner = null;
    let running = false;
    let submitting = false;

    function setCameraStatus(message, danger = false) {
        statusElement.textContent = message;
        statusElement.className = danger
            ? 'small text-danger mt-3'
            : 'small text-muted mt-3';
    }

    function normalizeQrValue(rawValue) {
        const raw = String(rawValue || '').trim();

        if (!raw) {
            return '';
        }

        // QR dạng JSON:
        // {"booking_code":"BK202608030001"}
        try {
            const json = JSON.parse(raw);

            if (json && typeof json === 'object') {
                return String(
                    json.booking_code
                    || json.code
                    || json.booking
                    || json.id
                    || raw
                ).trim();
            }
        } catch (error) {
            // Không phải JSON.
        }

        // QR dạng URL:
        // https://.../check-in?booking_code=BK...
        try {
            const url = new URL(raw);

            const queryCode =
                url.searchParams.get('booking_code')
                || url.searchParams.get('code')
                || url.searchParams.get('booking');

            if (queryCode) {
                return queryCode.trim();
            }

            const parts = url.pathname
                .split('/')
                .map(part => part.trim())
                .filter(Boolean);

            if (parts.length > 0) {
                const lastPart = decodeURIComponent(parts[parts.length - 1]);

                if (/^(BK[A-Z0-9_-]+|\d+)$/i.test(lastPart)) {
                    return lastPart;
                }
            }
        } catch (error) {
            // Không phải URL.
        }

        return raw;
    }

    async function stopCamera() {
        if (!scanner || !running) {
            reader.classList.add('d-none');
            placeholder.classList.remove('d-none');
            return;
        }

        try {
            await scanner.stop();
            await scanner.clear();
        } catch (error) {
            console.warn('Không thể dừng camera:', error);
        }

        running = false;
        startButton.disabled = false;
        stopButton.disabled = true;
        reader.classList.add('d-none');
        placeholder.classList.remove('d-none');

        setCameraStatus('Camera đã tắt.');
    }

    async function startCamera() {
        if (typeof Html5Qrcode === 'undefined') {
            setCameraStatus(
                'Không tải được thư viện quét QR. Hãy kiểm tra Internet hoặc nhập mã thủ công.',
                true
            );
            return;
        }

        if (running) {
            return;
        }

        startButton.disabled = true;
        setCameraStatus('Đang yêu cầu quyền sử dụng camera...');

        try {
            const cameras = await Html5Qrcode.getCameras();

            if (!cameras || cameras.length === 0) {
                throw new Error('Máy tính không tìm thấy camera.');
            }

            const preferredCamera =
                cameras.find(camera =>
                    /back|rear|environment/i.test(camera.label || '')
                )
                || cameras[0];

            placeholder.classList.add('d-none');
            reader.classList.remove('d-none');

            scanner = new Html5Qrcode('qr-reader');
            submitting = false;

            await scanner.start(
                preferredCamera.id,
                {
                    fps: 10,
                    qrbox: function (viewfinderWidth, viewfinderHeight) {
                        const size = Math.floor(
                            Math.min(viewfinderWidth, viewfinderHeight) * 0.72
                        );

                        return {
                            width: size,
                            height: size
                        };
                    },
                    aspectRatio: 1.333333
                },
                async function (decodedText) {
                    if (submitting) {
                        return;
                    }

                    const code = normalizeQrValue(decodedText);

                    if (!code) {
                        setCameraStatus('QR không chứa mã đơn hợp lệ.', true);
                        return;
                    }

                    submitting = true;
                    bookingCodeInput.value = code.toUpperCase();

                    setCameraStatus(
                        'Đã đọc QR: ' + code + '. Đang kiểm tra đơn...'
                    );

                    try {
                        await stopCamera();
                    } finally {
                        lookupForm.submit();
                    }
                },
                function () {
                    // Lỗi đọc từng frame là bình thường, không hiển thị liên tục.
                }
            );

            running = true;
            stopButton.disabled = false;

            setCameraStatus(
                'Camera đang hoạt động. Đưa QR vào giữa khung hình.'
            );
        } catch (error) {
            console.error(error);

            running = false;
            startButton.disabled = false;
            stopButton.disabled = true;
            reader.classList.add('d-none');
            placeholder.classList.remove('d-none');

            setCameraStatus(
                'Không mở được camera: '
                + (error && error.message ? error.message : String(error))
                + '. Hãy thử localhost/127.0.0.1 hoặc HTTPS.',
                true
            );
        }
    }

    startButton.addEventListener('click', startCamera);
    stopButton.addEventListener('click', stopCamera);

    window.addEventListener('beforeunload', function () {
        if (scanner && running) {
            scanner.stop().catch(function () {});
        }
    });
});
</script>
@endsection