@extends('layouts.app') {{-- Hoặc master layout dự án của bạn --}}

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">XÁC NHẬN THANH TOÁN ĐƠN ĐẶT SÂN</h4>
                </div>
                <div class="card-body p-4">

                    {{-- THANH HIỂN THỊ ĐẾM NGƯỢC THỜI GIAN GIỮ SÂN 5 PHÚT --}}
                    <div class="alert alert-warning text-center fw-bold mb-4 shadow-sm border-0 rounded-3">
                        <i class="bi bi-clock-history me-2 text-danger animate-pulse"></i>
                        Thời gian giữ sân còn lại để thanh toán: 
                        <span id="countdown-timer" class="text-danger fs-5 ms-1 fw-extrabold">05:00</span>
                    </div>
                    
                    <h5 class="text-secondary border-bottom pb-2 mb-3">Thông tin lượt đặt sân</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <div class="p-3 bg-light rounded-3 border-start border-success border-4 h-100">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Mã đơn hàng:</span>
                                    <strong class="text-dark">{{ $booking->booking_code }}</strong>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Sân bóng đặt:</span>
                                    <strong class="text-success fs-5">
                                        {{ $booking->field_name ?? 'Sân số 1 (Sân 7 người)' }}
                                    </strong>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Ngày đá:</span>
                                        <strong class="text-dark">
                                            {{ isset($booking->play_date) ? \Carbon\Carbon::parse($booking->play_date)->format('d/m/Y') : '04/07/2026' }}
                                        </strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Khung giờ:</span>
                                        <strong class="text-primary">
                                            {{ $booking->start_time ?? '17:30' }} - {{ $booking->end_time ?? '19:00' }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KHỐI HIỂN THỊ SỐ TIỀN - TỰ ĐỘNG BIẾN ĐỔI THEO LỰA CHỌN PHƯƠNG THỨC --}}
                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                <span class="text-muted small mb-1" id="amount-title">Tổng số tiền cần trả</span>
                                <h3 class="text-danger fw-bold mb-0">
                                    <span id="display-amount">{{ number_format($booking->total_price ?? $booking->total_amount ?? 0) }}</span> <span class="fs-6 text-secondary">VNĐ</span>
                                </h3>
                                <span class="badge bg-warning text-dark mt-2 px-3 py-1.5 rounded-pill small" id="deposit-note">
                                    Chờ xác nhận
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Kho dữ liệu ẩn phục vụ cho JavaScript đọc giá trị gốc --}}
                    <input type="hidden" id="raw-total-price" value="{{ $booking->total_price ?? $booking->total_amount ?? 0 }}">
                    <input type="hidden" id="raw-deposit-amount" value="{{ $booking->deposit_amount ?? (($booking->total_price ?? $booking->total_amount ?? 0) * 0.3) }}">

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li class="text-danger fw-bold">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('user.payment.process') }}" method="POST" id="payment-form">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                        <h5 class="text-secondary border-bottom pb-2 mb-3">Chọn phương thức thanh toán</h5>
                        
                        @foreach($paymentMethods as $method)
                            <div class="card p-3 mb-2 border @if($loop->first) border-success @endif d-flex flex-row align-items-center payment-method-block" 
                                 style="gap: 12px;">
                                <input class="form-check-input payment-method-radio m-0" type="radio" name="payment_method_id" 
                                    id="method-{{ $method->id }}" value="{{ $method->id }}" data-code="{{ $method->code }}"
                                    @if($loop->first) checked @endif required>
                                <label class="fw-bold text-dark method-name-text m-0 w-100" for="method-{{ $method->id }}" style="cursor: pointer;">
                                    {{ $method->name }}
                                </label>
                            </div>
                        @endforeach

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg w-100 py-2 fw-bold">XÁC NHẬN VÀ TIẾN HÀNH THANH TOÁN</button>
                            <a href="{{ route('user.bookings.index') }}" class="btn btn-link text-muted d-block text-center mt-2">Quay lại danh sách đơn đặt</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const displayAmount = document.getElementById('display-amount');
        const amountTitle = document.getElementById('amount-title');
        const depositNote = document.getElementById('deposit-note');
        
        const rawTotalPrice = parseFloat(document.getElementById('raw-total-price').value);
        const rawDepositAmount = parseFloat(document.getElementById('raw-deposit-amount').value);
        
        const radios = document.querySelectorAll('.payment-method-radio');

        // Hàm định dạng số thành dạng chuỗi tiền tệ (140000 -> 140.000)
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        function updateDisplayPrice() {
            let selectedRadio = document.querySelector('.payment-method-radio:checked');
            if (!selectedRadio) return;

            document.querySelectorAll('.payment-method-block').forEach(c => c.classList.remove('border-success'));
            const currentBlock = selectedRadio.closest('.payment-method-block');
            currentBlock.classList.add('border-success');

            const methodCode = (selectedRadio.getAttribute('data-code') || '').toUpperCase();
            const methodText = currentBlock.querySelector('.method-name-text').textContent.trim().toLowerCase();

            if (methodCode === 'PAY_AT_FIELD' || methodCode === 'TIEN_MAT' || methodText.includes('tại sân') || methodText.includes('tai san')) {
                displayAmount.textContent = formatMoney(rawDepositAmount);
                amountTitle.textContent = "Số tiền cần cọc trước (30%)";
                depositNote.textContent = "Cọc trước 30% giữ sân";
                depositNote.className = "badge bg-warning text-dark mt-2 px-3 py-1.5 rounded-pill small";
            } else {
                displayAmount.textContent = formatMoney(rawTotalPrice);
                amountTitle.textContent = "Tổng số tiền cần trả (100%)";
                depositNote.textContent = "Thanh toán đủ";
                depositNote.className = "badge bg-success text-white mt-2 px-3 py-1.5 rounded-pill small";
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', updateDisplayPrice);
        });

        updateDisplayPrice();

        // =========================================================================
        // LOGIC XỬ LÝ ĐẾM NGƯỢC 5 PHÚT GIỮ SÂN BẰNG JAVASCRIPT
        // =========================================================================
        const bookingCreatedAt = "{{ $booking->created_at }}";
        const createdAtTime = new Date(bookingCreatedAt.replace(/-/g, "/")).getTime();
        const limitMinutes = 5; 
        const expireTime = createdAtTime + limitMinutes * 60 * 1000; 

        const countdownTimer = document.getElementById('countdown-timer');

        const timerInterval = setInterval(function() {
            const now = new Date().getTime();
            const timeLeft = expireTime - now;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                countdownTimer.innerHTML = "ĐÃ HẾT HẠN GIỮ SÂN!";
                alert("Đơn hàng của bạn đã vượt quá thời gian giữ sân tạm thời (5 phút). Vui lòng thực hiện đặt lại lịch mới!");
                window.location.href = "{{ route('user.bookings.index') }}"; 
                return;
            }

            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            countdownTimer.innerHTML = 
                (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
        }, 1000);
    });
</script>
@endsection