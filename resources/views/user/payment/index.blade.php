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

                    {{-- THANH HIỂN THỊ ĐẾM NGƯỢC THỜI GIAN GIỮ SÂN 5 PHÚT --}}
                    <div class="alert alert-warning text-center fw-bold mb-4 shadow-sm border-0 rounded-3">
                        <i class="bi bi-clock-history me-2 text-danger"></i>
                        Thời gian giữ sân còn lại để thanh toán: 
                        <span id="countdown-timer" class="text-danger fs-5 ms-1 fw-extrabold">05:00</span>
                    </div>
                    
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
                                            @if(($booking->booking_type ?? 'single') === 'monthly')
                                                <span class="badge bg-success">Đặt cố định tháng</span>
                                            @else
                                                <span class="badge bg-primary">Đặt theo ngày lẻ</span>
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- KHỐI HIỂN THỊ SỐ TIỀN --}}
                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                <span class="text-muted small mb-1" id="amount-title">Số tiền cần thanh toán ngay</span>
                                <h3 class="text-danger fw-bold mb-0">
                                    <span id="display-amount">{{ number_format($booking->deposit_amount ?? $booking->total_amount, 0, ',', '.') }}</span> <span class="fs-6 text-secondary">VNĐ</span>
                                </h3>
                                <span class="badge bg-warning text-dark mt-2 px-3 py-1.5 rounded-pill small" id="deposit-note">
                                    Chờ thanh toán
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Kho dữ liệu ẩn phục vụ cho JavaScript đọc giá trị gốc --}}
                    <input type="hidden" id="raw-total-price" value="{{ $booking->total_price ?? $booking->total_amount ?? 0 }}">
                    <input type="hidden" id="raw-deposit-amount" value="{{ $booking->deposit_amount ?? (($booking->total_price ?? $booking->total_amount ?? 0) * 0.3) }}">

                    <form action="{{ route('user.payment.process') }}" method="POST" id="payment-form">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                        @php 
                            $isMonthlyBooking = (($booking->booking_type ?? 'single') === 'monthly'); 
                        @endphp

                        @if(!$isMonthlyBooking)
                            {{-- ĐƠN LẺ: HIỆN CHỌN PHƯƠNG THỨC THANH TOÁN (TẠI SÂN / QR) --}}
                            <h5 class="text-secondary border-bottom pb-2 mb-3">Chọn phương thức thanh toán</h5>
                            @if(isset($paymentMethods) && $paymentMethods->isNotEmpty())
                                @foreach($paymentMethods as $method)
                                    <div class="card p-3 mb-2 border @if($loop->first) border-success @endif d-flex flex-row align-items-center payment-method-block" style="gap: 12px;">
                                        <input class="form-check-input payment-method-radio m-0" type="radio" name="payment_method_id" 
                                            id="method-{{ $method->id }}" value="{{ $method->id }}" data-code="{{ $method->code }}"
                                            @if($loop->first) checked @endif required>
                                        <label class="fw-bold text-dark method-name-text m-0 w-100" for="method-{{ $method->id }}" style="cursor: pointer;">
                                            {{ $method->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
                        @else
                            {{-- ĐƠN THÁNG: ẨN HOÀN TOÀN TẠI SÂN/QR, CHỈ HIỆN THÔNG BÁO XÁC NHẬN --}}
                            <div class="alert alert-success border-0 rounded-4 p-4 mb-4 shadow-sm bg-success bg-opacity-10 text-success-emphasis">
                                <h6 class="fw-bold mb-1"><i class="bi bi-shield-check-fill me-1"></i> Xác nhận đơn lịch cố định tháng</h6>
                                <p class="small mb-0">Hệ thống đã ghi nhận số tiền cần thanh toán cho đơn lịch tháng. Vui lòng bấm xác nhận bên dưới để hoàn tất.</p>
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
        const isMonthly = @json($isMonthlyBooking);
        
        if (!isMonthly) {
            const displayAmount = document.getElementById('display-amount');
            const amountTitle = document.getElementById('amount-title');
            const depositNote = document.getElementById('deposit-note');
            
            const rawTotalPrice = parseFloat(document.getElementById('raw-total-price').value) || 0;
            const rawDepositAmount = parseFloat(document.getElementById('raw-deposit-amount').value) || (rawTotalPrice * 0.3);
            
            const radios = document.querySelectorAll('.payment-method-radio');

            function formatMoney(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount);
            }

            function updateDisplayPrice() {
                let selectedRadio = document.querySelector('.payment-method-radio:checked');
                if (!selectedRadio) return;

                document.querySelectorAll('.payment-method-block').forEach(c => c.classList.remove('border-success'));
                const currentBlock = selectedRadio.closest('.payment-method-block');
                if (currentBlock) {
                    currentBlock.classList.add('border-success');
                }

                const methodCode = (selectedRadio.getAttribute('data-code') || '').toUpperCase();
                const methodText = currentBlock ? currentBlock.querySelector('.method-name-text').textContent.trim().toLowerCase() : '';

                if (methodCode.includes('FIELD') || methodCode.includes('TIEN_MAT') || methodText.includes('tại sân') || methodText.includes('tai san')) {
                    if (displayAmount) displayAmount.textContent = formatMoney(rawDepositAmount);
                    if (amountTitle) amountTitle.textContent = "Số tiền cần cọc trước (30%)";
                    if (depositNote) {
                        depositNote.textContent = "Cọc trước 30% giữ sân";
                        depositNote.className = "badge bg-warning text-dark mt-2 px-3 py-1.5 rounded-pill small";
                    }
                } else {
                    if (displayAmount) displayAmount.textContent = formatMoney(rawTotalPrice);
                    if (amountTitle) amountTitle.textContent = "Tổng số tiền cần trả (100%)";
                    if (depositNote) {
                        depositNote.textContent = "Thanh toán đủ";
                        depositNote.className = "badge bg-success text-white mt-2 px-3 py-1.5 rounded-pill small";
                    }
                }
            }

            radios.forEach(radio => {
                radio.addEventListener('change', updateDisplayPrice);
            });

            updateDisplayPrice();
        }

        // Đếm ngược 5 phút giữ sân
        const bookingCreatedAt = "{{ $booking->created_at ?? now() }}";
        const createdAtTime = new Date(bookingCreatedAt.replace(/-/g, "/")).getTime();
        const limitMinutes = 5; 
        const expireTime = createdAtTime + limitMinutes * 60 * 1000; 

        const countdownTimer = document.getElementById('countdown-timer');

        if (countdownTimer) {
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
        }
    });
</script>
@endsection