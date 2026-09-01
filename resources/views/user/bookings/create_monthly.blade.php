@extends('layouts.app')

@section('title', 'Đăng ký giữ sân cố định theo tháng')

@section('content')
<div class="container py-4" style="max-width: 1140px;">

    {{-- HEADER TRANG (GỌN GÀNG, CÂN ĐỐI) --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-3 border-bottom">
        <div>
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill fw-bold mb-2">
                <i class="bi bi-calendar-check-fill me-1"></i> ĐẶT LỊCH ĐỊNH KỲ THÁNG
            </span>
            <h3 class="fw-bold text-dark mb-1">Đăng ký giữ sân cố định theo tháng</h3>
            <p class="text-secondary small mb-0">Cụm sân thi đấu: <strong class="text-dark">{{ $stadium->name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('stadiums.show', $stadium->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold bg-white">
                <i class="bi bi-arrow-left me-1"></i> Quay lại chi tiết sân
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 mb-4 p-3 border-0 shadow-sm bg-danger bg-opacity-10 text-danger">
            <div class="d-flex align-items-center gap-2 fw-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i> Vui lòng kiểm tra lại thông tin:
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.bookings.storeMonthly') }}" method="POST" id="monthlyBookingForm">
        @csrf
        <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">
        <input type="hidden" name="calculated_total_amount" id="inputTotalAmount" value="0">
        <input type="hidden" name="calculated_payable_amount" id="inputPayableAmount" value="0">

        <div class="row g-4 align-items-start">
            {{-- CỘT CẤU HÌNH LỊCH THÁNG (BÊN TRÁI - 8 PHẦN) --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-body p-4">
                        
                        {{-- TIÊU ĐỀ SECTION --}}
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                            <div class="bg-success text-white rounded-3 p-2.5 d-flex align-items-center justify-content-center shadow-xs" style="width: 42px; height: 42px;">
                                <i class="bi bi-sliders fs-5 text-white"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-0 fs-6">Thiết lập lịch cố định cả tháng</h5>
                                <span class="text-muted small">Chọn khung giờ vàng và lịch đá định kỳ cho đội bóng</span>
                            </div>
                        </div>

                        {{-- CẢNH BÁO QUY CHẾ --}}
                        <div class="alert alert-warning border-0 rounded-3 small mb-4 bg-warning bg-opacity-10 text-dark p-3 d-flex align-items-start gap-2">
                            <i class="bi bi-shield-lock-fill fs-5 text-warning flex-shrink-0 mt-0.5"></i>
                            <div style="line-height: 1.4;">
                                <strong class="d-block mb-0.5 text-dark">Quy chế giữ lịch tháng:</strong>
                                Đội bóng có thể chọn cọc trước 50% hoặc thanh toán đủ 100%. Hệ thống tự động tính toán chi phí từ ngày hôm nay trở đi.
                            </div>
                        </div>

                        <div class="row g-3">
                            {{-- CHỌN SÂN --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-1">Sân thi đấu</label>
                                @php
                                    $selectedFieldId = request()->input('field_id') ?? request()->input('field');
                                    $selectedField = $fields->where('id', $selectedFieldId)->first() ?? $fields->first();
                                @endphp
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-success fw-bold px-3">
                                        <i class="bi bi-shield-shaded"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-start-0 rounded-end-3 py-2 fw-bold text-dark shadow-none" value="{{ $selectedField->name ?? 'Không xác định' }}" readonly>
                                </div>
                                <input type="hidden" name="field_id" id="fieldSelect" value="{{ $selectedField->id ?? '' }}" data-price="{{ $selectedField->price_per_hour ?? 350000 }}">
                            </div>

                            {{-- KHUNG GIỜ CỐ ĐỊNH --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-1">Khung giờ cố định <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-success fw-bold px-3">
                                        <i class="bi bi-clock"></i>
                                    </span>
                                    <select name="time_slot_id" id="timeSlotSelect" class="form-select border-start-0 rounded-end-3 py-2 shadow-none fw-medium text-dark" required>
                                        <option value="">-- Chọn khung giờ đá --</option>
                                        @foreach($timeSlots as $timeSlot)
                                            <option
                                                value="{{ $timeSlot->id }}"
                                                data-prices='@json(collect($fields)->mapWithKeys(fn ($field) => [$field->id => $fieldSlotPrices[$field->id][$timeSlot->id] ?? null]))'
                                                data-time="{{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }}"
                                            >
                                                {{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- THỨ TRONG TUẦN --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-1">Thứ trong tuần <span class="text-danger">*</span></label>
                                <select name="day_of_week" id="dayOfWeekSelect" class="form-select rounded-3 py-2 shadow-none fw-medium text-dark" required>
                                    <option value="0" selected>Chủ Nhật (Hằng tuần)</option>
                                    <option value="6">Thứ Bảy (Hằng tuần)</option>
                                    <option value="5">Thứ Sáu</option>
                                    <option value="4">Thứ Năm</option>
                                    <option value="3">Thứ Tư</option>
                                    <option value="2">Thứ Ba</option>
                                    <option value="1">Thứ Hai</option>
                                </select>
                            </div>

                            {{-- THÁNG ĐĂNG KÝ --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-1">Tháng đăng ký <span class="text-danger">*</span></label>
                                <select name="month" id="monthSelect" class="form-select rounded-3 py-2 shadow-none fw-medium text-dark" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected($m == now()->month)>Tháng {{ $m }}</option>
                                    @endfor
                                </select>
                            </div>

                            {{-- NĂM ĐĂNG KÝ --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-1">Năm đăng ký <span class="text-danger">*</span></label>
                                <select name="year" id="yearSelect" class="form-select rounded-3 py-2 shadow-none fw-medium text-dark" required>
                                    <option value="2026" selected>Năm 2026</option>
                                    <option value="2027">Năm 2027</option>
                                </select>
                            </div>

                            {{-- HÌNH THỨC THANH TOÁN --}}
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold text-secondary small text-uppercase mb-2">Hình thức thanh toán áp dụng</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="payment_type" id="mPayDeposit50" value="deposit_50" checked>
                                        <label class="card h-100 border p-3 rounded-3 cursor-pointer shadow-xs transition-all payment-card" for="mPayDeposit50">
                                            <div class="d-flex align-items-start gap-2.5">
                                                <div class="form-check mt-0.5">
                                                    <input class="form-check-input shadow-none" type="radio" name="payment_type_fake" checked>
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark d-block mb-1 small">Đặt cọc trước 50%</span>
                                                    <span class="text-muted small d-block" style="font-size: 12px; line-height: 1.4;">50% tiền sân còn lại sẽ được thanh toán dần theo từng buổi khi ra sân.</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="radio" class="btn-check" name="payment_type" id="mPayFull" value="full">
                                        <label class="card h-100 border p-3 rounded-3 cursor-pointer shadow-xs transition-all payment-card" for="mPayFull">
                                            <div class="d-flex align-items-start gap-2.5">
                                                <div class="form-check mt-0.5">
                                                    <input class="form-check-input shadow-none" type="radio" name="payment_type_fake">
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-success d-block mb-1 small">Thanh toán đủ 100%</span>
                                                    <span class="text-muted small d-block" style="font-size: 12px; line-height: 1.4;">Thanh toán trọn gói cả tháng, an tâm ra sân thi đấu không cần bận tâm thanh toán lẻ.</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- CỘT HÓA ĐƠN THANH TOÁN (BÊN PHẢI - 4 PHẦN) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 position-sticky bg-white overflow-hidden" style="top: 20px;">
                    
                    {{-- HEADER HÓA ĐƠN CHỮ TRẮNG --}}
                    <div class="p-3 bg-success text-white">
                        <h5 class="fw-bold mb-0 text-white d-flex align-items-center gap-2 fs-6">
                            <i class="bi bi-receipt-cutoff text-white"></i> Chi tiết thanh toán
                        </h5>
                    </div>

                    <div class="card-body p-3.5">
                        <ul class="list-unstyled text-dark mb-3 small">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-secondary">Tổng số buổi (từ hôm nay):</span>
                                <strong class="text-dark fw-bold" id="totalSlotsText">0 buổi</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-secondary">Đơn giá tạm tính/buổi:</span>
                                <strong class="text-dark fw-bold" id="slotPriceText">0đ</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-dark fw-bold">Tổng tiền cả tháng:</span>
                                <strong class="text-success fw-bold" id="totalAmountText">0đ</strong>
                            </li>
                        </ul>

                        {{-- KHUNG THÀNH TIỀN NỔI BẬT --}}
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <span class="d-block text-secondary small fw-bold mb-0.5" id="payableLabel">Cần thanh toán ngay (Cọc 50%):</span>
                            <div class="text-danger fs-3 fw-extrabold" id="payableAmountText">0đ</div>
                        </div>

                        {{-- ĐOẠN THÔNG BÁO CHỮ TRẮNG KHI DÙNG NỀN XANH LÁ --}}
                        <div class="p-3 bg-success rounded-3 text-white mb-3 small d-flex align-items-start gap-2 shadow-xs" id="noticeText">
                            <i class="bi bi-shield-check fs-5 flex-shrink-0 text-white mt-0.5"></i>
                            <div class="text-white fw-medium small" style="line-height: 1.4;">50% tiền sân còn lại sẽ được thanh toán dần theo từng buổi khi ra sân.</div>
                        </div>

                        {{-- NÚT SUBMIT ĐẶT LỊCH THÁNG --}}
                        <button type="submit" id="submitMonthlyBtn" class="btn btn-success rounded-3 w-100 py-2.5 fw-bold shadow-sm fs-6 transition-all hover-scale d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-calendar-plus-fill"></i> Tạo Đơn Đặt Lịch Tháng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- CSS TÙY CHỈNH THẨM MỸ --}}
<style>
    .payment-card {
        transition: all 0.2s ease-in-out;
        background-color: #fafbfc;
    }
    .payment-card:hover {
        border-color: #198754 !important;
        background-color: #fff;
    }
    .btn-check:checked + .payment-card {
        border-color: #198754 !important;
        background-color: rgba(25, 135, 84, 0.04);
        box-shadow: 0 0.25rem 0.75rem rgba(25, 135, 84, 0.1) !important;
    }
    .btn-check:checked + .payment-card .form-check-input {
        background-color: #198754;
        border-color: #198754;
    }
    .shadow-xs {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.04);
    }
    .cursor-pointer {
        cursor: pointer;
    }
    .fw-extrabold {
        font-weight: 800;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fieldSelect = document.getElementById('fieldSelect');
    const timeSlotSelect = document.getElementById('timeSlotSelect');
    const dayOfWeekSelect = document.getElementById('dayOfWeekSelect');
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
    const monthlyBookingForm = document.getElementById('monthlyBookingForm');
    const submitMonthlyBtn = document.getElementById('submitMonthlyBtn');

    const totalSlotsText = document.getElementById('totalSlotsText');
    const slotPriceText = document.getElementById('slotPriceText');
    const totalAmountText = document.getElementById('totalAmountText');
    const payableAmountText = document.getElementById('payableAmountText');
    const payableLabel = document.getElementById('payableLabel');
    const noticeText = document.getElementById('noticeText');

    const inputTotalAmount = document.getElementById('inputTotalAmount');
    const inputPayableAmount = document.getElementById('inputPayableAmount');

    function calculateMonthlyBooking() {
        const year = parseInt(yearSelect.value);
        const month = parseInt(monthSelect.value);
        const dayOfWeek = parseInt(dayOfWeekSelect.value);

        const selectedSlot = timeSlotSelect.options[timeSlotSelect.selectedIndex];
        let prices = {};
        try {
            prices = selectedSlot ? JSON.parse(selectedSlot.dataset.prices || '{}') : {};
        } catch (error) {
            prices = {};
        }

        const fieldId = fieldSelect.value;
        let pricePerSlot = selectedSlot && fieldId
            ? parseFloat(prices[fieldId]) || parseFloat(fieldSelect.dataset.price) || 350000
            : 0;

        let slotCount = 0;
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const date = new Date(year, month - 1, 1);
        while (date.getMonth() === month - 1) {
            if (date.getDay() === dayOfWeek && date >= today) {
                slotCount++;
            }
            date.setDate(date.getDate() + 1);
        }

        const totalAmount = slotCount * pricePerSlot;
        
        let selectedPaymentType = document.querySelector('input[name="payment_type"]:checked').value;
        let payableAmount = totalAmount;

        if (selectedPaymentType === 'deposit_50') {
            payableAmount = totalAmount * 0.50;
            payableLabel.textContent = 'Cần thanh toán ngay (Cọc 50%):';
            noticeText.innerHTML = '<i class="bi bi-shield-check fs-5 flex-shrink-0 text-white mt-0.5"></i><div class="text-white fw-medium small" style="line-height: 1.4;">50% tiền sân còn lại sẽ được thanh toán dần theo từng buổi khi ra sân.</div>';
        } else {
            payableLabel.textContent = 'Thanh toán ngay (100%):';
            noticeText.innerHTML = '<i class="bi bi-check-circle-fill fs-5 flex-shrink-0 text-white mt-0.5"></i><div class="text-white fw-medium small" style="line-height: 1.4;">Đội bóng đã thanh toán đủ toàn bộ chi phí các buổi trong tháng.</div>';
        }

        totalSlotsText.textContent = slotCount + ' buổi';
        slotPriceText.textContent = new Intl.NumberFormat('vi-VN').format(pricePerSlot) + 'đ';
        totalAmountText.textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + 'đ';
        payableAmountText.textContent = new Intl.NumberFormat('vi-VN').format(payableAmount) + 'đ';

        if(inputTotalAmount) inputTotalAmount.value = totalAmount;
        if(inputPayableAmount) inputPayableAmount.value = payableAmount;
    }

    fieldSelect.addEventListener('change', calculateMonthlyBooking);
    timeSlotSelect.addEventListener('change', calculateMonthlyBooking);
    dayOfWeekSelect.addEventListener('change', calculateMonthlyBooking);
    monthSelect.addEventListener('change', calculateMonthlyBooking);
    yearSelect.addEventListener('change', calculateMonthlyBooking);
    paymentTypeRadios.forEach(radio => radio.addEventListener('change', calculateMonthlyBooking));

    calculateMonthlyBooking();

    if (monthlyBookingForm) {
        monthlyBookingForm.addEventListener('submit', function (e) {
            if (!fieldSelect.value || !timeSlotSelect.value) {
                e.preventDefault();
                alert('Vui lòng chọn đầy đủ Sân đá và Khung giờ cố định!');
                return;
            }
            submitMonthlyBtn.disabled = true;
            submitMonthlyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang tạo đơn...';
        });
    }
});
</script>
@endsection