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

    .rules-list {
        display: grid;
        gap: 14px;
        padding-left: 0;
        margin-bottom: 0;
    }

    .rules-list li {
        background: #f8fafc;
        color: #111827;
        border-radius: 16px;
        padding: 14px 18px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
        list-style: none;
        font-weight: 600;
    }

    .rules-list li::before {
        content: '';
        display: inline-block;
        width: 10px;
        height: 10px;
        margin-right: 12px;
        border-radius: 50%;
        background: #16a34a;
    }

    .related-stadium-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .related-stadium-card {
        border: 1px solid #e5e7eb;
        border-radius: 24px;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }

    .related-stadium-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
    }

    .related-stadium-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .related-stadium-card .card-body {
        padding: 1rem 1rem 1.25rem;
    }

    .related-stadium-card .stadium-name {
        min-height: 3rem;
    }

    .slot-btn {
        border: 1px solid #d1d5db;
        border-radius: 16px;
        padding: 14px;
        background: #fff;
        width: 100%;
        text-align: left;
        transition: .2s;
        color: #111827;
    }

    .slot-btn:hover:not(:disabled) {
        border-color: #16a34a;
        background: #f0fdf4;
    }

    .slot-btn.active {
        border-color: #16a34a;
        background: #dcfce7;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
    }

    .slot-btn.is-occupied {
        border-color: #fda4af;
        background: #fff1f2;
        color: #9f1239;
        opacity: .9;
    }

    .slot-btn:disabled {
        cursor: not-allowed;
        opacity: .85;
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

    .booking-panel {
        position: static;
    }

    @media (max-width: 991px) {
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

    $defaultPrice = $defaultPrice ?? $stadium->price ?? 0;
@endphp

<div class="container-fluid py-5">

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

                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <span class="badge bg-white text-success rounded-pill px-3 py-2">
                        <i class="bi bi-star-fill me-1"></i>
                        {{ $averageRating ?? 0 }}/5
                    </span>
                    <span class="text-white-75">
                        {{ optional($reviews)->count() ?? 0 }} đánh giá
                    </span>
                </div>

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

    <div class="row g-4 justify-content-center">

        <div class="col-12 col-xl-11 mx-auto" style="max-width: 1400px;">

            <div class="row g-4 mb-4">
                <div class="col-12 col-lg-5 order-lg-1 order-2">
                    <div class="card info-card h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h4 class="fw-bold mb-0">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                Thông tin cơ sở sân
                            </h4>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-12">
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

                                <div class="col-md-12">
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

                                <div class="col-md-12">
                                    <div class="mt-4">
                                        <h5 class="fw-bold mb-3">Nội quy sân</h5>
                                        <ul class="list-unstyled mb-0 rules-list">
                                            <li>✓ Có mặt trước giờ thi đấu 15 phút.</li>
                                            <li>✓ Không mang giày đinh sắt.</li>
                                            <li>✓ Không hút thuốc trên sân.</li>
                                            <li>✓ Giữ gìn vệ sinh.</li>
                                            <li>✓ Bảo quản tài sản cá nhân.</li>
                                            <li>✓ Không gây mất trật tự.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mt-4">
                                        <h5 class="fw-bold mb-3">Chính sách đặt sân</h5>
                                        <ul class="list-unstyled mb-0 rules-list">
                                            <li>✓ Đặt sân trước tối đa 30 ngày.</li>
                                            <li>✓ Hủy trước 24 giờ được hoàn tiền.</li>
                                            <li>✓ Sau thời gian trên không hoàn tiền.</li>
                                            <li>✓ Đổi lịch nếu còn sân trống.</li>
                                            <li>✓ Thanh toán trước khi vào sân.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7 order-lg-2 order-1">
                    <div class="card info-card booking-panel h-100">
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
                                    <input type="hidden" name="field_id" id="selectedField" value="{{ $selectedField?->id }}">
                                    <input type="hidden" name="time_slot" id="selectedTimeSlot" value="{{ old('time_slot') }}">
                                    <input type="hidden" name="total_price" id="selectedPrice" value="{{ old('total_price') }}">

                                    <!-- <div class="mb-3">
                                        <label class="form-label fw-semibold" for="promotionCode">Mã giảm giá</label>
                                        <input type="text"
                                               id="promotionCode"
                                               name="promotion_code"
                                               value="{{ old('promotion_code') }}"
                                               class="form-control rounded-3 @error('promotion_code') is-invalid @enderror"
                                               placeholder="Nhập mã giảm giá nếu có"
                                               style="text-transform: uppercase">
                                        @error('promotion_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div> -->

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Ngày đặt sân <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               id="bookingDate"
                                               name="booking_date"
                                               value="{{ old('booking_date', date('Y-m-d')) }}"
                                               min="{{ date('Y-m-d') }}"
                                               class="form-control rounded-3 @error('booking_date') is-invalid @enderror"
                                               required>

                                        @error('booking_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            Chọn khung giờ <span class="text-danger">*</span>
                                        </label>

                                        <div class="accordion" id="slotAccordion">
                                            @if(empty($timeSlots))
                                                <div class="alert alert-light rounded-3 border">Chưa có khung giờ nào được cấu hình cho sân này.</div>
                                            @else
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
                                                                            data-slot-id="{{ $slot['id'] ?? '' }}"
                                                                            data-time="{{ $slot['time'] }}"
                                                                            data-price="{{ $slot['price'] }}"
                                                                            data-available="true">
                                                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                                                            <div>
                                                                                <div class="slot-time">
                                                                                    {{ $slot['time'] }}
                                                                                </div>
                                                                                <small class="text-muted">Bấm để chọn</small>
                                                                            </div>

                                                                            <div class="text-end">
                                                                                <div class="slot-price">
                                                                                    {{ number_format((float) $slot['price'], 0, ',', '.') }}đ
                                                                                </div>
                                                                                <span class="slot-state badge rounded-pill mt-2 bg-success-subtle text-success">Còn trống</span>
                                                                            </div>
                                                                        </div>
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @endif
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

                                    @if($services->isNotEmpty())
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Dịch vụ đi kèm</label>
                                            <div class="list-group list-group-flush">
                                                @foreach($services as $sIndex => $service)
                                                    <div class="d-flex align-items-center justify-content-between py-2">
                                                        <div class="d-flex align-items-center">
                                                            <input class="form-check-input me-2 service-select-panel" type="checkbox"
                                                                   id="stadiumServiceCheck{{ $sIndex }}"
                                                                   data-index="{{ $sIndex }}"
                                                                   data-price="{{ $service->price }}">
                                                            <label for="stadiumServiceCheck{{ $sIndex }}" class="mb-0">
                                                                <div class="fw-semibold">{{ $service->name }}</div>
                                                                <small class="text-muted">{{ $service->unit ?? 'lượt' }} - {{ number_format((float)$service->price,0,',','.') }}đ</small>
                                                            </label>
                                                            <input type="hidden" name="services[{{ $sIndex }}][id]" value="{{ $service->id }}">
                                                        </div>

                                                        <div style="width:90px">
                                                            <input type="number" name="services[{{ $sIndex }}][quantity]" value="0" min="0" class="form-control form-control-sm service-qty-panel" data-index="{{ $sIndex }}" disabled>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

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
                                    <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
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

            @if(!empty($relatedStadiums) && $relatedStadiums->isNotEmpty())
                <div class="card info-card mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h4 class="fw-bold mb-0">
                            <i class="bi bi-grid-3x3-gap text-primary me-2"></i>
                            Gợi ý sân khác
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        <div class="related-stadium-list">
                            @foreach($relatedStadiums as $otherStadium)
                                @php
                                    $otherImage = $otherStadium->image
                                        ?? $otherStadium->thumbnail
                                        ?? $otherStadium->image_url
                                        ?? null;
                                    $otherImageUrl = $otherImage
                                        ? asset('storage/' . $otherImage)
                                        : asset('images/banner1.png');
                                @endphp

                                <a href="{{ route('stadiums.show', $otherStadium->id) }}" class="related-stadium-card text-decoration-none text-dark">
                                    <img src="{{ $otherImageUrl }}" alt="{{ $otherStadium->name }}">
                                    <div class="card-body">
                                        <h5 class="fw-bold stadium-name mb-2">{{ $otherStadium->name }}</h5>
                                        <p class="text-muted small mb-2">{{ $otherStadium->address ?? 'Địa chỉ cập nhật' }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-success fw-semibold">Giá từ {{ number_format((float) ($otherStadium->price ?? 0), 0, ',', '.') }}đ</span>
                                            <span class="badge bg-light text-dark rounded-pill">Xem thêm</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

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
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slotButtons = Array.from(document.querySelectorAll('.js-slot-btn'));
        const scheduleSlotButtons = Array.from(document.querySelectorAll('.js-schedule-slot-btn'));
        const selectedTimeSlot = document.getElementById('selectedTimeSlot');
        const selectedPrice = document.getElementById('selectedPrice');
        const summaryTime = document.getElementById('summaryTime');
        const summaryPrice = document.getElementById('summaryPrice');
        const bookingDate = document.getElementById('bookingDate');
        const selectedField = document.getElementById('selectedField');
        const bookingForm = document.getElementById('bookingForm');
        const serviceCheckboxes = Array.from(document.querySelectorAll('.service-select-panel'));
        const serviceQtyInputs = Array.from(document.querySelectorAll('.service-qty-panel'));
        let selectedSlotPrice = 0;
        const availabilityUrl = "{{ route('user.bookings.availability', $stadium->id) }}";

        function formatMoney(value) {
            value = Number(value || 0);
            return value.toLocaleString('vi-VN') + 'đ';
        }

        function getServiceTotal() {
            let total = 0;
            serviceQtyInputs.forEach(function (qty) {
                const idx = qty.dataset.index;
                const checkbox = document.querySelector('.service-select-panel[data-index="' + idx + '"]');
                if (!checkbox || !checkbox.checked) {
                    return;
                }
                const price = Number(checkbox.dataset.price || 0);
                const quantity = Number(qty.value || 0);
                total += price * quantity;
            });
            return total;
        }

        function updateTotalPrice() {
            const serviceTotal = getServiceTotal();
            const total = selectedSlotPrice + serviceTotal;

            if (selectedPrice) {
                selectedPrice.value = total;
                selectedPrice.dataset.basePrice = selectedSlotPrice;
            }

            if (summaryPrice) {
                summaryPrice.innerText = formatMoney(total);
            }
        }

        function resetSelection() {
            slotButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            selectedSlotPrice = 0;

            slotButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            if (selectedTimeSlot) {
                selectedTimeSlot.value = '';
            }

            if (selectedPrice) {
                selectedPrice.value = '';
            }

            if (summaryTime) {
                summaryTime.innerText = 'Chưa chọn';
            }

            if (summaryPrice) {
                summaryPrice.innerText = '0đ';
            }
        }

        function updateAvailability(response) {
            const slots = response.slots || [];
            const slotMap = new Map(slots.map(function (slot) {
                return [String(slot.id), slot];
            }));

            slotButtons.forEach(function (button) {
                const slotId = String(button.dataset.slotId || '');
                const slot = slotMap.get(slotId);
                const state = button.querySelector('.slot-state');

                if (!slot) {
                    return;
                }

                button.dataset.price = slot.price;
                const priceElement = button.querySelector('.slot-price');
                if (priceElement) {
                    priceElement.innerText = formatMoney(slot.price);
                }

                if (slot.status === 'available') {
                    button.disabled = false;
                    button.dataset.available = 'true';
                    button.classList.remove('is-occupied', 'locked', 'active');
                    button.classList.add('available');
                    if (state) {
                        state.className = 'slot-state badge rounded-pill mt-2 bg-success-subtle text-success';
                        state.innerText = 'Còn trống';
                    }
                } else {
                    button.disabled = true;
                    button.dataset.available = 'false';
                    button.classList.remove('active', 'available');
                    const statusClass = slot.status === 'locked' ? 'locked' : 'is-occupied';
                    button.classList.add(statusClass);
                    if (state) {
                        if (slot.status === 'locked') {
                            state.className = 'slot-state badge rounded-pill mt-2 bg-secondary-subtle text-secondary';
                            state.innerText = 'Đã khóa';
                        } else {
                            state.className = 'slot-state badge rounded-pill mt-2 bg-danger-subtle text-danger';
                            state.innerText = 'Đã đặt';
                        }
                    }
                }

                if (button.classList.contains('active')) {
                    selectedPrice.value = slot.price;
                    if (summaryPrice) {
                        summaryPrice.innerText = formatMoney(slot.price);
                    }
                }
            });

            if (selectedTimeSlot && selectedTimeSlot.value) {
                const selectedButton = slotButtons.find(function (item) {
                    return item.dataset.time === selectedTimeSlot.value;
                });

                if (selectedButton && selectedButton.disabled) {
                    resetSelection();
                }

                if (selectedButton && selectedButton.classList.contains('active')) {
                    updateTotalPrice();
                }
            }
        }

        function fetchAvailability() {
            if (!bookingDate) {
                return;
            }

            const params = new URLSearchParams({
                field_id: selectedField ? selectedField.value : '',
                booking_date: bookingDate.value
            });

            fetch(availabilityUrl + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                updateAvailability(data);
            })
            .catch(function () {
                slotButtons.forEach(function (button) {
                    button.disabled = false;
                    button.dataset.available = 'true';
                    button.classList.remove('is-occupied');
                    button.querySelector('.slot-state').className = 'slot-state badge rounded-pill mt-2 bg-success-subtle text-success';
                    button.querySelector('.slot-state').innerText = 'Còn trống';
                });
            });
        }

        slotButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (button.disabled) {
                    return;
                }

                slotButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                button.classList.add('active');

                const time = button.dataset.time;
                const price = button.dataset.price;

                if (selectedTimeSlot) {
                    selectedTimeSlot.value = time;
                }

                selectedSlotPrice = Number(price || 0);
                updateTotalPrice();

                if (summaryTime) {
                    summaryTime.innerText = time;
                }
            });
        });

        scheduleSlotButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (button.disabled) {
                    return;
                }

                scheduleSlotButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                button.classList.add('active');

                const fieldId = button.dataset.fieldId;
                const bookingDateValue = button.dataset.bookingDate;
                const time = button.dataset.time;
                const price = button.dataset.price;

                if (selectedField && fieldId) {
                    selectedField.value = fieldId;
                }

                if (bookingDate && bookingDateValue) {
                    bookingDate.value = bookingDateValue;
                }

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

        if (bookingDate) {
            bookingDate.addEventListener('change', fetchAvailability);
        }

        serviceCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const idx = checkbox.dataset.index;
                const qty = document.querySelector('.service-qty-panel[data-index="' + idx + '"]');
                if (!qty) {
                    return;
                }
                qty.disabled = !checkbox.checked;
                if (!checkbox.checked) {
                    qty.value = 0;
                } else if (!qty.value || qty.value == 0) {
                    qty.value = 1;
                }
                updateTotalPrice();
            });
        });

        serviceQtyInputs.forEach(function (qty) {
            qty.addEventListener('input', function () {
                if (qty.value < 0) {
                    qty.value = 0;
                }
                updateTotalPrice();
            });
        });

        fetchAvailability();
    });
</script>
@endpush
