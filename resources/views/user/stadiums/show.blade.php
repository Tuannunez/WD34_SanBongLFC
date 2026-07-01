@extends('layouts.app')

@section('title', 'Chi tiết sân bóng')

@push('styles')
<style>
    .stadium-detail-hero {
        background: linear-gradient(135deg, #0f766e, #16a34a);
        border-radius: 28px;
        overflow: hidden;
        color: #fff;
    }

    .stadium-main-img {
        height: 430px;
        object-fit: cover;
        border-radius: 24px;
    }

    .info-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
    }

    .booking-panel {
        position: sticky;
        top: 95px;
    }

    .slot-btn {
        border: 1px solid #d1d5db;
        border-radius: 16px;
        padding: 14px;
        background: #fff;
        width: 100%;
        text-align: left;
        transition: .2s;
    }

    .slot-btn:hover {
        border-color: #16a34a;
        background: #f0fdf4;
    }

    .slot-btn.active {
        border-color: #16a34a;
        background: #dcfce7;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
    }

    .slot-time {
        font-weight: 700;
        color: #111827;
    }

    .slot-price {
        color: #16a34a;
        font-weight: 800;
    }

    .feature-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #dcfce7;
        color: #16a34a;
        font-size: 20px;
    }

    @media (max-width: 991px) {
        .booking-panel {
            position: static;
        }

        .stadium-main-img {
            height: 280px;
        }
    }
</style>
@endpush

@section('content')
@php
    $stadiumName = $stadium->name ?? 'Sân bóng';
    $stadiumAddress = $stadium->address ?? 'Đang cập nhật địa chỉ';
    $stadiumPhone = $stadium->phone ?? 'Đang cập nhật';
    $stadiumDescription = $stadium->description ?? 'Cơ sở sân bóng chất lượng, phù hợp cho các đội bóng đặt lịch thi đấu và luyện tập.';

    $openTime = $stadium->open_time ?? '06:00';
    $closeTime = $stadium->close_time ?? '22:30';

    $image = $stadium->image
        ?? $stadium->thumbnail
        ?? $stadium->image_url
        ?? null;

    $imageUrl = $image
        ? asset('storage/' . $image)
        : asset('images/banner1.png');

    $firstSlot = $timeSlots[0]['slots'][0] ?? null;
    $defaultPrice = $firstSlot['price'] ?? 0;
@endphp

<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            @foreach($errors->all() as $error)
                <div>
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $error }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="stadium-detail-hero p-4 p-lg-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="mb-3">
                    <a href="{{ route('home') }}" class="btn btn-light rounded-3">
                        <i class="bi bi-arrow-left me-1"></i>
                        Quay lại trang chủ
                    </a>
                </div>

                <span class="badge bg-light text-success rounded-pill px-3 py-2 mb-3">
                    <i class="bi bi-star-fill me-1"></i>
                    Cơ sở sân bóng nổi bật
                </span>

                <h1 class="fw-bold display-6 mb-3">
                    {{ $stadiumName }}
                </h1>

                <p class="lead mb-4">
                    {{ $stadiumDescription }}
                </p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                            <div class="fw-bold mb-1">
                                <i class="bi bi-clock me-1"></i>
                                Giờ mở cửa
                            </div>
                            <div>{{ $openTime }} - {{ $closeTime }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                            <div class="fw-bold mb-1">
                                <i class="bi bi-cash-coin me-1"></i>
                                Giá từ
                            </div>
                            <div>{{ number_format((float) $defaultPrice, 0, ',', '.') }}đ</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                            <div class="fw-bold mb-1">
                                <i class="bi bi-check-circle me-1"></i>
                                Trạng thái
                            </div>
                            <div>Sẵn sàng đặt sân</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <img src="{{ $imageUrl }}"
                     alt="{{ $stadiumName }}"
                     class="img-fluid w-100 stadium-main-img shadow">
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card info-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Thông tin cơ sở sân
                    </h4>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Tên cơ sở</div>
                                    <div class="fw-bold">{{ $stadiumName }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Số điện thoại</div>
                                    <div class="fw-bold">{{ $stadiumPhone }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Địa chỉ</div>
                                    <div class="fw-bold">{{ $stadiumAddress }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-card-text"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Mô tả</div>
                                    <div>{{ $stadiumDescription }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card info-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-clock-history text-success me-2"></i>
                        Bảng giá theo khung giờ
                    </h4>
                </div>

                <div class="card-body p-4">
                    @foreach($timeSlots as $group)
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                {{ $group['session'] }}
                            </h5>

                            <div class="row g-3">
                                @foreach($group['slots'] as $slot)
                                    <div class="col-md-6">
                                        <div class="border rounded-4 p-3 h-100 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold">
                                                        <i class="bi bi-clock me-1 text-primary"></i>
                                                        {{ $slot['time'] }}
                                                    </div>
                                                    <small class="text-muted">Khung giờ đặt sân</small>
                                                </div>

                                                <div class="fw-bold text-success">
                                                    {{ number_format((float) $slot['price'], 0, ',', '.') }}đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card info-card">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-shield-check text-warning me-2"></i>
                        Quy định đặt sân
                    </h4>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Đặt sân trước
                                </div>
                                <div class="text-muted">
                                    Khách hàng nên đặt sân trước để giữ lịch thi đấu.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Xác nhận từ admin
                                </div>
                                <div class="text-muted">
                                    Đơn đặt sân sẽ có trạng thái chờ xác nhận sau khi gửi.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Hủy đơn
                                </div>
                                <div class="text-muted">
                                    User chỉ nên hủy đơn khi đơn còn ở trạng thái chờ xác nhận.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Liên hệ hỗ trợ
                                </div>
                                <div class="text-muted">
                                    Liên hệ cơ sở sân nếu cần hỗ trợ thêm về lịch đặt.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="card info-card booking-panel">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-calendar-check text-success me-2"></i>
                        Đặt sân ngay
                    </h4>
                    <p class="text-muted mb-0 mt-1">
                        Chọn ngày và khung giờ bạn muốn đặt
                    </p>
                </div>

                <div class="card-body p-4">
                    @auth
                        <form action="{{ route('user.bookings.store.from-stadium', $stadium->id) }}"
                              method="POST"
                              id="bookingForm">
                            @csrf

                            <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">
                            <input type="hidden" name="time_slot" id="selectedTimeSlot" value="{{ old('time_slot') }}">
                            <input type="hidden" name="total_price" id="selectedPrice" value="{{ old('total_price') }}">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Ngày đặt sân <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       name="booking_date"
                                       value="{{ old('booking_date') }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="form-control rounded-3 @error('booking_date') is-invalid @enderror"
                                       required>

                                @error('booking_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Số điện thoại liên hệ
                                </label>
                                <input type="text"
                                       name="customer_phone"
                                       value="{{ old('customer_phone', Auth::user()->phone ?? '') }}"
                                       class="form-control rounded-3"
                                       placeholder="Nhập số điện thoại liên hệ">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Chọn khung giờ <span class="text-danger">*</span>
                                </label>

                                <div class="accordion" id="slotAccordion">
                                    @foreach($timeSlots as $groupIndex => $group)
                                        <div class="accordion-item border rounded-4 mb-2 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button {{ $groupIndex !== 0 ? 'collapsed' : '' }}"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#slotGroup{{ $groupIndex }}">
                                                    {{ $group['session'] }}
                                                </button>
                                            </h2>

                                            <div id="slotGroup{{ $groupIndex }}"
                                                 class="accordion-collapse collapse {{ $groupIndex === 0 ? 'show' : '' }}"
                                                 data-bs-parent="#slotAccordion">
                                                <div class="accordion-body">
                                                    <div class="d-grid gap-2">
                                                        @foreach($group['slots'] as $slot)
                                                            <button type="button"
                                                                    class="slot-btn js-slot-btn"
                                                                    data-time="{{ $slot['time'] }}"
                                                                    data-price="{{ $slot['price'] }}">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <div class="slot-time">
                                                                            {{ $slot['time'] }}
                                                                        </div>
                                                                        <small class="text-muted">Bấm để chọn</small>
                                                                    </div>

                                                                    <div class="slot-price">
                                                                        {{ number_format((float) $slot['price'], 0, ',', '.') }}đ
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('time_slot')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                                @error('booking_time')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Ghi chú
                                </label>
                                <textarea name="note"
                                          rows="3"
                                          class="form-control rounded-3"
                                          placeholder="Nhập ghi chú nếu có">{{ old('note') }}</textarea>
                            </div>

                            <div class="bg-light rounded-4 p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Khung giờ</span>
                                    <strong id="summaryTime">Chưa chọn</strong>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Tổng tiền</span>
                                    <strong class="text-success fs-5" id="summaryPrice">0đ</strong>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success rounded-3 w-100 py-3 fw-bold">
                                <i class="bi bi-calendar-check me-1"></i>
                                Gửi đơn đặt sân
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                 style="width: 72px; height: 72px;">
                                <i class="bi bi-lock fs-2"></i>
                            </div>

                            <h5 class="fw-bold">Bạn cần đăng nhập</h5>
                            <p class="text-muted">
                                Vui lòng đăng nhập để gửi đơn đặt sân.
                            </p>

                            <a href="{{ route('login') }}" class="btn btn-primary rounded-3 w-100 py-3">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Đăng nhập để đặt sân
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slotButtons = document.querySelectorAll('.js-slot-btn');
        const selectedTimeSlot = document.getElementById('selectedTimeSlot');
        const selectedPrice = document.getElementById('selectedPrice');
        const summaryTime = document.getElementById('summaryTime');
        const summaryPrice = document.getElementById('summaryPrice');
        const bookingForm = document.getElementById('bookingForm');

        function formatMoney(value) {
            value = Number(value || 0);
            return value.toLocaleString('vi-VN') + 'đ';
        }

        slotButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                slotButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                button.classList.add('active');

                const time = button.dataset.time;
                const price = button.dataset.price;

                if (selectedTimeSlot) {
                    selectedTimeSlot.value = time;
                }

                if (selectedPrice) {
                    selectedPrice.value = price;
                }

                if (summaryTime) {
                    summaryTime.innerText = time;
                }

                if (summaryPrice) {
                    summaryPrice.innerText = formatMoney(price);
                }
            });
        });

        if (bookingForm) {
            bookingForm.addEventListener('submit', function (event) {
                if (!selectedTimeSlot || !selectedTimeSlot.value) {
                    event.preventDefault();
                    alert('Vui lòng chọn khung giờ đặt sân.');
                    return false;
                }
            });
        }
    });
</script>
@endpush