@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0 fw-bold">XÁC NHẬN THANH TOÁN ĐƠN ĐẶT SÂN</h4>
                </div>
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-4"></i>
                                <div>
                                    <strong>Thành công!</strong>
                                    <div class="small">{{ session('success') }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @php
                        $totalAmountVal = (float)($booking->total_amount ?? $booking->final_amount ?? $booking->total_price ?? 0);
                        $currentPaidAmt = (float)($booking->paid_amount ?? 0);
                        $remainingAmt = max(0, $totalAmountVal - $currentPaidAmt);
                        $isMonthlyPay = (($booking->booking_type ?? 'single') === 'monthly');
                        $isRemainingPayment = ($currentPaidAmt > 0 && $remainingAmt > 0);
                    @endphp

                    @if(!$isRemainingPayment)
                        <div class="alert alert-warning text-center fw-bold mb-4 shadow-sm border-0 rounded-3">
                            <i class="bi bi-clock-history me-2 text-danger"></i>
                            Thời gian giữ sân còn lại để thanh toán: 
                            <span id="countdown-timer" class="text-danger fs-5 ms-1 fw-extrabold">05:00</span>
                        </div>
                    @endif
                    
                    <h5 class="text-secondary border-bottom pb-2 mb-3">Thông tin lượt đặt sân</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-7">
                            <div class="p-3 bg-light rounded-3 border-start border-success border-4 h-100">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Mã đơn hàng:</span>
                                    <strong class="text-dark">{{ $booking->booking_code ?? $booking->code ?? 'N/A' }}</strong>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Sân bóng đặt:</span>
                                    <strong class="text-success fs-5">
                                        {{ $booking->field_name ?? 'Sân bóng' }}
                                    </strong>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Loại đơn:</span>
                                        <strong class="text-dark">
                                            @if($isMonthlyPay)
                                                <span class="badge bg-success">Đặt cố định tháng</span>
                                            @else
                                                <span class="badge bg-primary">Đặt theo ngày lẻ</span>
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @php
                            if ($isRemainingPayment) {
                                $payAmount = $remainingAmt;
                                $payTitle = "Số tiền còn lại cần thanh toán nốt";
                                $badgeText = "Thanh toán phần còn thiếu";
                                $badgeClass = "bg-warning text-dark";
                            } elseif ($isMonthlyPay) {
                                $payAmount = (float)($booking->deposit_amount ?? $totalAmountVal);
                                $payTitle = "Tổng số tiền thanh toán lịch tháng";
                                $badgeText = "Thanh toán lịch tháng";
                                $badgeClass = "bg-success text-white";
                            } else {
                                $payAmount = (float)($booking->deposit_amount ?? ($totalAmountVal * 0.3));
                                $payTitle = "Số tiền cần thanh toán ngay";
                                $badgeText = "Chờ thanh toán";
                                $badgeClass = "bg-warning text-dark";
                            }
                        @endphp

                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                <span class="text-muted small mb-1" id="amount-title">{{ $payTitle }}</span>
                                <h3 class="text-danger fw-bold mb-0">
                                    <span id="display-amount">{{ number_format($payAmount, 0, ',', '.') }}</span> <span class="fs-6 text-secondary">VNĐ</span>
                                </h3>
                                @if($isRemainingPayment)
                                    <span class="badge bg-success-subtle text-success small mt-2 px-3 py-1.5 rounded-pill">
                                        Đã trả trước đó: {{ number_format($currentPaidAmt, 0, ',', '.') }}đ
                                    </span>
                                @else
                                    <span class="badge {{ $badgeClass }} mt-2 px-3 py-1.5 rounded-pill small" id="deposit-note">
                                        {{ $badgeText }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="raw-total-price" value="{{ $totalAmountVal }}">
                    <input type="hidden" id="raw-deposit-amount" value="{{ $payAmount }}">

                    <form action="{{ route('user.payment.process') }}" method="POST" id="payment-form">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                        <h5 class="text-secondary border-bottom pb-2 mb-3">Mã khuyến mãi</h5>
                        <div class="mb-4">
                            <select name="promotion_id" id="promotion-select" class="form-select rounded-3 py-2 border">
                                <option value="">-- Chọn mã khuyến mãi (nếu có) --</option>
                                @if(isset($promotions))
                                    @foreach($promotions as $promo)
                                        <option value="{{ $promo->id }}" 
                                                data-percent="{{ $promo->discount_percent ?? $promo->discount_value ?? 0 }}" 
                                                data-amount="{{ $promo->discount_amount ?? 0 }}"
                                                @if($booking->promotion_id == $promo->id) selected @endif>
                                            {{ $promo->code }} - {{ $promo->name ?? '' }} 
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        @if(!$isMonthlyPay && !$isRemainingPayment)
                            <h5 class="text-secondary border-bottom pb-2 mb-3">Chọn phương thức thanh toán</h5>
                            <!-- Lựa chọn 1: Cọc 30% -->
                            <div class="card p-3 mb-2 border border-success d-flex flex-row align-items-center payment-method-block" style="gap: 12px;">
                                <input class="form-check-input payment-method-radio m-0" type="radio" name="payment_method_id" 
                                    id="method-deposit" value="deposit_30" data-code="DEPOSIT_30" checked required>
                                <label class="fw-bold text-dark method-name-text m-0 w-100" for="method-deposit" style="cursor: pointer;">
                                    Cọc 30% tiền sân
                                    <span class="d-block text-muted small fw-normal">Thanh toán trước 30% giá trị để giữ sân</span>
                                </label>
                            </div>

                            <!-- Lựa chọn 2: Thanh toán 100% -->
                            <div class="card p-3 mb-2 border d-flex flex-row align-items-center payment-method-block" style="gap: 12px;">
                                <input class="form-check-input payment-method-radio m-0" type="radio" name="payment_method_id" 
                                    id="method-full" value="paid_100" data-code="PAID_100" required>
                                <label class="fw-bold text-dark method-name-text m-0 w-100" for="method-full" style="cursor: pointer;">
                                    Thanh toán 100%
                                    <span class="d-block text-muted small fw-normal">Thanh toán toàn bộ tiền sân</span>
                                </label>
                            </div>
                        @else
                            <div class="alert alert-success border-0 rounded-4 p-4 mb-4 shadow-sm bg-success bg-opacity-10 text-success-emphasis">
                                <h6 class="fw-bold mb-1"><i class="bi bi-shield-check-fill me-1"></i> Xác nhận thanh toán trực tuyến</h6>
                                <p class="small mb-0">Hệ thống ghi nhận số tiền của đơn hàng. Vui lòng bấm xác nhận bên dưới để hoàn tất.</p>
                            </div>
                        @endif

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
        const isMonthly = @json($isMonthlyPay);
        const isRemainingPayment = @json($isRemainingPayment);
        
        if (!isMonthly && !isRemainingPayment) {
            const displayAmount = document.getElementById('display-amount');
            const amountTitle = document.getElementById('amount-title');
            const depositNote = document.getElementById('deposit-note');
            const promotionSelect = document.getElementById('promotion-select');
            
            const rawTotalPriceInput = document.getElementById('raw-total-price');
            const rawDepositAmountInput = document.getElementById('raw-deposit-amount');

            function formatMoney(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount > 0 ? Math.round(amount) : 0);
            }

            function updateDisplayPrice() {
                let baseTotalPrice = parseFloat(rawTotalPriceInput.value) || 0;
                let baseDepositAmount = baseTotalPrice * 0.3;

                if (promotionSelect && promotionSelect.selectedIndex > 0) {
                    let selectedOption = promotionSelect.options[promotionSelect.selectedIndex];
                    let percent = parseFloat(selectedOption.getAttribute('data-percent')) || 0;
                    let fixedAmount = parseFloat(selectedOption.getAttribute('data-amount')) || 0;

                    if (percent > 0) {
                        let discount = baseTotalPrice * (percent / 100);
                        baseTotalPrice -= discount;
                        baseDepositAmount = baseTotalPrice * 0.3;
                    } else if (fixedAmount > 0) {
                        baseTotalPrice -= fixedAmount;
                        baseDepositAmount = baseTotalPrice * 0.3;
                    }
                }

                baseTotalPrice = Math.max(0, baseTotalPrice);
                baseDepositAmount = Math.max(0, baseDepositAmount);

                let selectedRadio = document.querySelector('.payment-method-radio:checked');
                if (!selectedRadio) return;

                document.querySelectorAll('.payment-method-block').forEach(c => c.classList.remove('border-success'));
                const currentBlock = selectedRadio.closest('.payment-method-block');
                if (currentBlock) currentBlock.classList.add('border-success');

                const methodCode = (selectedRadio.getAttribute('data-code') || '').toUpperCase();

                if (methodCode === 'DEPOSIT_30') {
                    if (displayAmount) displayAmount.textContent = formatMoney(baseDepositAmount);
                    if (amountTitle) amountTitle.textContent = "Số tiền cần cọc trước (30%)";
                    if (depositNote) {
                        depositNote.textContent = "Cọc trước 30% giữ sân";
                        depositNote.className = "badge bg-warning text-dark mt-2 px-3 py-1.5 rounded-pill small";
                    }
                } else {
                    if (displayAmount) displayAmount.textContent = formatMoney(baseTotalPrice);
                    if (amountTitle) amountTitle.textContent = "Tổng số tiền cần trả (100%)";
                    if (depositNote) {
                        depositNote.textContent = "Thanh toán đủ";
                        depositNote.className = "badge bg-success text-white mt-2 px-3 py-1.5 rounded-pill small";
                    }
                }
            }

            document.querySelectorAll('.payment-method-radio').forEach(radio => {
                radio.addEventListener('change', updateDisplayPrice);
            });

            if (promotionSelect) promotionSelect.addEventListener('change', updateDisplayPrice);
        }

        const countdownTimer = document.getElementById('countdown-timer');
        if (countdownTimer && !isRemainingPayment) {
            const bookingCreatedAt = "{{ $booking->created_at ?? now() }}";
            const createdAtTime = new Date(bookingCreatedAt.replace(/-/g, "/")).getTime();
            const limitMinutes = 5; 
            const expireTime = createdAtTime + limitMinutes * 60 * 1000; 

            const timerInterval = setInterval(function() {
                const now = new Date().getTime();
                const timeLeft = expireTime - now;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    countdownTimer.innerHTML = "ĐÃ HẾT HẠN!";
                    alert("Đơn hàng đã hết hạn giữ sân!");
                    window.location.href = "{{ route('user.bookings.index') }}";
                    return;
                }

                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                countdownTimer.innerHTML = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
            }, 1000);
        }
    });
</script>
@endsection