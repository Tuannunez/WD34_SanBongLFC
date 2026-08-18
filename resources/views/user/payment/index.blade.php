@extends('layouts.app')

@section('content')
@php
    $totalAmountVal = (float)($booking->total_amount ?? $booking->final_amount ?? $booking->total_price ?? 0);
    $currentPaidAmt = (float)($booking->paid_amount ?? 0);
    $remainingAmt = max(0, $totalAmountVal - $currentPaidAmt);
    $isMonthlyPay = (($booking->booking_type ?? 'single') === 'monthly');
    $isRemainingPayment = ($currentPaidAmt > 0 && $remainingAmt > 0);

    if ($isRemainingPayment) {
        $payAmount = $remainingAmt;
    } elseif ($isMonthlyPay) {
        $payAmount = (float)($booking->deposit_amount ?? $totalAmountVal);
    } else {
        $payAmount = (float)($booking->deposit_amount ?? ($totalAmountVal * 0.3));
    }
@endphp

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            
            <!-- Card tổng thể sử dụng class bootstrap chuẩn -->
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                
                <!-- Header Card -->
                <div class="card-header bg-success bg-gradient text-white text-center py-4 border-0">
                    <h4 class="fw-bold mb-1">Xác Nhận Thanh Toán</h4>
                    <p class="mb-0 small opacity-75">Kiểm tra thông tin và hoàn tất giao dịch giữ sân</p>
                </div>

                <div class="card-body p-4 p-md-4 bg-white">

                    <!-- Thời gian giữ sân -->
                    @if(!$isRemainingPayment)
                    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center justify-content-between py-2 px-3 mb-4 shadow-sm">
                        <div class="d-flex align-items-center gap-2 small fw-bold text-dark">
                            <i class="bi bi-clock-history text-danger fs-5"></i>
                            <span>Thời gian giữ sân:</span>
                        </div>
                        <span id="countdown-timer" class="badge bg-danger fs-6 px-3 py-1.5 rounded-pill font-monospace">05:00</span>
                    </div>
                    @endif

                    <form action="{{ route('user.payment.process') }}" method="POST" id="payment-form">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                        <!-- Thông tin khách hàng & Chi tiết sân -->
                        <div class="card bg-light border-0 rounded-3 p-3 mb-4">
                            <div class="small fw-bold text-muted text-uppercase mb-2">Thông tin khách hàng</div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Họ tên</span>
                                <span class="fw-semibold text-dark">{{ $booking->customer_name ?? $booking->name ?? Auth::user()->name ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Số điện thoại</span>
                                <span class="fw-semibold text-dark">{{ $booking->customer_phone ?? $booking->phone ?? Auth::user()->phone ?? 'N/A' }}</span>
                            </div>

                            <div class="small fw-bold text-muted text-uppercase mb-2 pt-2 border-top">Chi tiết đơn đặt</div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Mã đơn hàng</span>
                                <span class="fw-semibold text-dark font-monospace">{{ $booking->booking_code ?? 'N/A' }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Sân bóng đặt</span>
                                <span class="fw-semibold text-success">{{ $booking->field_name ?? 'Sân bóng' }}</span>
                            </div>
                        </div>

                        <!-- Chọn mã khuyến mãi -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Mã khuyến mãi</label>
                            <div class="card border rounded-3 p-3 bg-white shadow-sm cursor-pointer d-flex flex-row align-items-center justify-content-between hover-bg-light"
                                 data-bs-toggle="modal" data-bs-target="#voucherModal">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-ticket-perforated fs-5"></i>
                                    </div>
                                    <div>
                                        @php
                                            $selectedPromoName = 'Chọn hoặc nhập mã giảm giá';
                                            if(!empty($booking->promotion_id) && isset($promotions)) {
                                                foreach($promotions as $p) {
                                                    if($p->id == $booking->promotion_id) {
                                                        $selectedPromoName = $p->code . ' - ' . ($p->name ?? '');
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div id="selected-voucher-text" class="fw-bold text-dark small">{{ $selectedPromoName }}</div>
                                        <div id="selected-voucher-desc" class="text-muted" style="font-size: 0.75rem;">Tiết kiệm hơn cho đơn hàng</div>
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">Chọn mã &gt;</span>
                            </div>
                            <input type="hidden" name="promotion_id" id="promotion-id-input" value="{{ $booking->promotion_id }}">
                        </div>

                        <!-- Phương thức thanh toán -->
                        @if(!$isMonthlyPay && !$isRemainingPayment)
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Phương thức thanh toán</label>
                            <div class="vstack gap-2">
                                <div class="card border p-3 rounded-3 shadow-sm cursor-pointer">
                                    <div class="form-check m-0 d-flex align-items-center gap-3">
                                        <input class="form-check-input payment-method-radio mt-0 fs-5" type="radio" name="payment_method_id" value="1" data-code="DEPOSIT_30" id="method1" checked>
                                        <label class="form-check-label w-100 cursor-pointer" for="method1">
                                            <span class="fw-bold text-dark d-block small">Cọc 30% giá trị sân</span>
                                            <span class="text-muted" style="font-size: 0.75rem;">Thanh toán trước một phần qua mã QR để giữ chỗ</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="card border p-3 rounded-3 shadow-sm cursor-pointer">
                                    <div class="form-check m-0 d-flex align-items-center gap-3">
                                        <input class="form-check-input payment-method-radio mt-0 fs-5" type="radio" name="payment_method_id" value="2" data-code="PAID_100" id="method2">
                                        <label class="form-check-label w-100 cursor-pointer" for="method2">
                                            <span class="fw-bold text-dark d-block small">Thanh toán toàn bộ 100%</span>
                                            <span class="text-muted" style="font-size: 0.75rem;">Thanh toán đủ để hoàn tất đơn ngay lập tức</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Khung Tổng Tiền Thanh Toán Dưới Cùng -->
                        <div class="card bg-success-subtle border border-success-subtle rounded-3 p-3 mb-4 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="small fw-bold text-success text-uppercase d-block">Tổng tiền thanh toán</span>
                                    <span class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-shield-check me-1"></i>Đã bao gồm ưu đãi</span>
                                </div>
                                <span class="fs-3 fw-bold text-danger font-monospace" id="display-amount">{{ number_format($payAmount, 0, ',', '.') }}đ</span>
                            </div>
                        </div>

                        <!-- Nút Xác Nhận -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg fw-bold py-3 shadow-sm text-uppercase">
                                <i class="bi bi-shield-lock-fill me-2"></i>Xác nhận & Thanh toán
                            </button>
                            <a href="{{ route('user.bookings.index') }}" class="btn btn-link text-muted text-decoration-none text-center small mt-1">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách đơn đặt
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Chọn Voucher -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 pt-4 pb-3 bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-ticket-perforated text-success me-2"></i>Chọn Voucher Khuyến Mãi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="card border p-3 mb-3 cursor-pointer bg-light shadow-sm hover-border-success transition-all" onclick="selectVoucher('', 'Chọn hoặc nhập mã giảm giá', 'Tiết kiệm hơn cho đơn hàng', 0, 0)">
                    <div class="fw-bold text-dark small">Không sử dụng mã giảm giá</div>
                    <small class="text-muted" style="font-size: 0.75rem;">Bỏ qua ưu đãi này</small>
                </div>

                <div class="vstack gap-3">
                    @if(isset($promotions) && count($promotions) > 0)
                        @foreach($promotions as $promo)
                            @php
                                $pType = $promo->discount_type ?? 'amount';
                                $pVal = (float)($promo->discount_value ?? 0);
                                $pPercent = (float)($promo->discount_percent ?? 0);
                                $pAmount = (float)($promo->discount_amount ?? 0);

                                if ($pType === 'percent' || $pPercent > 0) {
                                    $isPercent = 1;
                                    $discountNum = $pPercent > 0 ? $pPercent : $pVal;
                                    $descText = "Giảm " . $discountNum . "%";
                                } else {
                                    $isPercent = 0;
                                    $discountNum = $pAmount > 0 ? $pAmount : $pVal;
                                    $descText = "Giảm trực tiếp " . number_format($discountNum, 0, ',', '.') . "đ";
                                }
                            @endphp

                            <div class="card border p-3 cursor-pointer shadow-sm hover-border-success transition-all"
                                 onclick="selectVoucher('{{ $promo->id }}', '{{ $promo->code }}', '{{ $promo->name ?? '' }}', '{{ $discountNum }}', '{{ $isPercent }}')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge text-white mb-1 px-2.5 py-1 rounded fw-bold shadow-xs" style="background-color: #065f46; font-size: 0.7rem;">{{ $promo->code }}</span>
                                        <h6 class="fw-bold text-dark mb-1 small">{{ $promo->name ?? 'Ưu đãi đặt sân' }}</h6>
                                        <p class="text-danger fw-semibold mb-0" style="font-size: 0.75rem;"><i class="bi bi-tag-fill me-1"></i>{{ $descText }}</p>
                                    </div>
                                    <span class="btn btn-sm text-white rounded-pill px-3 py-1.5 fw-bold shadow-sm" style="background-color: #065f46; font-size: 0.75rem;">Dùng ngay</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted small">Hiện tại không có mã khuyến mãi nào khả dụng.</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4 bg-white">
                <button type="button" class="btn btn-secondary w-100 rounded-3 py-2 fw-semibold" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const totalAmountOriginal = parseFloat("{{ $totalAmountVal }}") || 0;
    let currentDiscountAmount = 0;

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount > 0 ? Math.round(amount) : 0) + 'đ';
    }

    function updateDisplayPrice() {
        let displayTotal = totalAmountOriginal - currentDiscountAmount;
        if (displayTotal < 0) displayTotal = 0;

        let selectedRadio = document.querySelector('.payment-method-radio:checked');
        let methodCode = selectedRadio ? selectedRadio.getAttribute('data-code') : 'PAID_100';

        let finalPayAmount = displayTotal;
        if (methodCode === 'DEPOSIT_30') {
            finalPayAmount = displayTotal * 0.3;
        }

        const displayAmountEl = document.getElementById('display-amount');
        if (displayAmountEl) {
            displayAmountEl.textContent = formatMoney(finalPayAmount);
        }
    }

    function selectVoucher(id, code, name, discountValue, isPercent) {
        document.getElementById('promotion-id-input').value = id;
        
        let descText = "Tiết kiệm hơn cho đơn hàng";
        currentDiscountAmount = 0;

        if (id !== "") {
            document.getElementById('selected-voucher-text').textContent = code + " - " + name;
            
            let val = parseFloat(discountValue) || 0;
            if (isPercent == "1" || isPercent == 1) {
                currentDiscountAmount = totalAmountOriginal * (val / 100);
                descText = "Giảm " + val + "% (Tiết kiệm " + formatMoney(currentDiscountAmount) + ")";
            } else {
                currentDiscountAmount = val;
                descText = "Giảm trực tiếp " + formatMoney(currentDiscountAmount);
            }
        } else {
            document.getElementById('selected-voucher-text').textContent = "Chọn hoặc nhập mã giảm giá";
            currentDiscountAmount = 0;
        }

        document.getElementById('selected-voucher-desc').textContent = descText;
        
        var myModalEl = document.getElementById('voucherModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if(modal) {
            modal.hide();
        }

        updateDisplayPrice();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.payment-method-radio').forEach(radio => {
            radio.addEventListener('change', updateDisplayPrice);
        });

        updateDisplayPrice();

        const countdownTimer = document.getElementById('countdown-timer');
        if (countdownTimer) {
            const bookingCreatedAt = "{{ $booking->payment_started_at ?? $booking->created_at ?? now() }}";
            const createdAtTime = new Date(bookingCreatedAt.replace(/-/g, "/")).getTime();
            const limitMinutes = 5; 
            const expireTime = createdAtTime + limitMinutes * 60 * 1000; 

            const timerInterval = setInterval(function() {
                const now = new Date().getTime();
                const timeLeft = expireTime - now;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    countdownTimer.innerHTML = "ĐÃ HẾT HẠN!";
                    
                    // GỌI AJAX VÀO ROUTE ĐÃ CÓ SẴN TRONG WEB.PHP ĐỂ CẬP NHẬT DATABASE
                    $.ajax({
                        url: "{{ route('user.bookings.cancel-timeout', $booking->id) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        complete: function() {
                            alert("Đơn hàng đã hết hạn giữ sân (quá 5 phút)!");
                            window.location.href = "{{ route('user.bookings.index') }}";
                        }
                    });
                    return;
                }

                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                countdownTimer.innerHTML = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
            }, 1000);
        }
    });
</script>

<style>
    .cursor-pointer { cursor: pointer; }
    .hover-bg-light:hover { background-color: #f8f9fa !important; }
    .hover-border-success:hover { border-color: #198754 !important; background-color: #f0fdf4 !important; }
</style>
@endsection