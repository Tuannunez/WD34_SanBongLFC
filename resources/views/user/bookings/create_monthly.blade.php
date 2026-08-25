@extends('layouts.app')

@section('title', 'Đăng ký đặt sân cố định theo tháng')

@section('content')
<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-semibold mb-2">
                <i class="bi bi-calendar-range-fill me-1"></i> Lịch cố định đội bóng
            </span>
            <h3 class="fw-bold mb-1">Đăng ký giữ sân cố định theo tháng</h3>
            <p class="text-muted mb-0">Cụm sân: <strong>{{ $stadium->name }}</strong></p>
        </div>

        <a href="{{ route('stadiums.show', $stadium->id) }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại chi tiết sân
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 mb-4 p-3 border-0 shadow-sm">
            <div class="d-flex align-items-center gap-2 text-danger fw-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i> Vui lòng kiểm tra lại thông tin:
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- THẺ FORM ÔM TRỌN CẢ 2 CỘT ĐỂ ĐẢM BẢO GỬI ĐỦ DỮ LIỆU --}}
    <form action="{{ route('user.bookings.storeMonthly') }}" method="POST" id="monthlyBookingForm">
        @csrf
        <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">

        {{-- 2 THẺ ẨN TRUYỀN CHÍNH XÁC SỐ TIỀN TỪ JS SANG CONTROLLER --}}
        <input type="hidden" name="calculated_total_amount" id="inputTotalAmount" value="0">
        <input type="hidden" name="calculated_payable_amount" id="inputPayableAmount" value="0">

        <div class="row g-4">
            {{-- CỘT CẤU HÌNH LỊCH THÁNG (BÊN TRÁI) --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-success text-white p-3.5 px-4 border-0">
                        <h5 class="fw-bold mb-0 fs-6">
                            <i class="bi bi-sliders me-2"></i> Thiết lập lịch cố định cả tháng
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <div class="alert alert-warning border-0 rounded-3 small mb-4 bg-warning bg-opacity-10 text-warning-emphasis">
                            <i class="bi bi-shield-lock-fill me-1 text-warning"></i>
                            <strong>Quy chế đặt lịch tháng:</strong> Đội bóng có thể chọn cọc trước 50% hoặc thanh toán đủ 100%. Hệ thống chỉ tính tiền và giữ lịch từ ngày hiện tại trở đi.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Chọn sân đá <span class="text-danger">*</span></label>
                                <select name="field_id" id="fieldSelect" class="form-select rounded-3 py-2.5" required>
                                    <option value="">-- Chọn sân --</option>
                                    @foreach($fields as $field)
                                        <option value="{{ $field->id }}" data-price="{{ $field->price_per_hour ?? 350000 }}" @selected(request()->input('field') == $field->id)>
                                            {{ $field->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Khung giờ đá cố định <span class="text-danger">*</span></label>
                                <select name="time_slot_id" id="timeSlotSelect" class="form-select rounded-3 py-2.5" required>
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

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Thứ cố định trong tuần <span class="text-danger">*</span></label>
                                <select name="day_of_week" id="dayOfWeekSelect" class="form-select rounded-3 py-2.5" required>
                                    <option value="0" selected>Chủ Nhật (Mọi Chủ Nhật)</option>
                                    <option value="6">Thứ Bảy (Mọi Thứ Bảy)</option>
                                    <option value="5">Thứ Sáu</option>
                                    <option value="4">Thứ Năm</option>
                                    <option value="3">Thứ Tư</option>
                                    <option value="2">Thứ Ba</option>
                                    <option value="1">Thứ Hai</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Tháng đăng ký <span class="text-danger">*</span></label>
                                <select name="month" id="monthSelect" class="form-select rounded-3 py-2.5" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" @selected($m == now()->month)>Tháng {{ $m }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Năm đăng ký <span class="text-danger">*</span></label>
                                <select name="year" id="yearSelect" class="form-select rounded-3 py-2.5" required>
                                    <option value="2026" selected>Năm 2026</option>
                                    <option value="2027">Năm 2027</option>
                                </select>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label fw-semibold">Hình thức thanh toán áp dụng</label>
                                <div class="d-flex gap-4 p-3 bg-light rounded-3 border">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="mPayDeposit50" value="deposit_50" checked>
                                        <label class="form-check-label fw-medium text-dark" for="mPayDeposit50">
                                            Đặt cọc trước <strong>50% tổng tiền</strong> (50% còn lại trả theo từng buổi)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_type" id="mPayFull" value="full">
                                        <label class="form-check-label fw-medium text-success" for="mPayFull">
                                            Thanh toán đủ <strong>100% chi phí cả tháng</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT HIỂN THỊ CHI TIẾT SỐ BUỔI & NÚT SUBMIT (BÊN PHẢI) --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 20px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-success"><i class="bi bi-receipt me-2"></i>Chi tiết thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small text-muted mb-4">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>Tổng số buổi (từ hôm nay):</span>
                                <strong class="text-dark" id="totalSlotsText">0 buổi</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span>Đơn giá tạm tính/buổi:</span>
                                <strong class="text-dark" id="slotPriceText">0đ</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom fs-6">
                                <span class="fw-bold text-dark">Tổng tiền cả tháng:</span>
                                <strong class="text-success" id="totalAmountText">0đ</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 pt-3 fs-6 bg-light px-2 rounded-3 mt-2" id="payableBox">
                                <span class="fw-bold text-danger" id="payableLabel">Cần thanh toán ngay (Cọc 50%):</span>
                                <strong class="text-danger fs-5" id="payableAmountText">0đ</strong>
                            </li>
                        </ul>

                        <div class="p-3 bg-success-subtle rounded-3 text-success mb-3 small" id="noticeText">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Chỉ tính tiền từ các buổi từ hôm nay trở đi trong tháng.
                        </div>

                        {{-- NÚT SUBMIT ĐẶT LỊCH THÁNG --}}
                        <button type="submit" id="submitMonthlyBtn" class="btn btn-success rounded-3 w-100 py-2.5 fw-bold shadow-sm fs-6">
                            <i class="bi bi-calendar-plus me-1"></i> Tạo Đơn Đặt Lịch Tháng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

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

        const selectedField = fieldSelect.options[fieldSelect.selectedIndex];
        let pricePerSlot = selectedSlot && selectedField
            ? parseFloat(prices[selectedField.value]) || parseFloat(selectedField.dataset.price) || 350000
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
            noticeText.innerHTML = '<i class="bi bi-info-circle-fill me-1"></i> 50% tiền sân còn lại sẽ được thanh toán dần theo từng buổi khi ra sân.';
        } else {
            payableLabel.textContent = 'Thanh toán ngay (100%):';
            noticeText.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Đội bóng đã thanh toán đủ toàn bộ chi phí các buổi trong tháng.';
        }

        totalSlotsText.textContent = slotCount + ' buổi';
        slotPriceText.textContent = new Intl.NumberFormat('vi-VN').format(pricePerSlot) + 'đ';
        totalAmountText.textContent = new Intl.NumberFormat('vi-VN').format(totalAmount) + 'đ';
        payableAmountText.textContent = new Intl.NumberFormat('vi-VN').format(payableAmount) + 'đ';

        // GÁN TRỰC TIẾP VÀO THẺ ẨN ĐỂ GỬI SANG CONTROLLER CHÍNH XÁC
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