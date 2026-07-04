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
                    
                    <!-- PHẦN ĐƯỢC THÊM MỚI & THAY THẾ: Thông tin lượt đặt sân chi tiết -->
                    <h5 class="text-secondary border-bottom pb-2 mb-3">Thông tin lượt đặt sân</h5>
                    <div class="row g-3 mb-4">
                        <!-- Cột bên trái: Chi tiết sân, ngày và khung giờ đá -->
                        <div class="col-md-7">
                            <div class="p-3 bg-light rounded-3 border-start border-success border-4 h-100">
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Mã đơn hàng:</span>
                                    <strong class="text-dark">{{ $booking->booking_code }}</strong>
                                </div>
                                
                                <div class="mb-2">
                                    <span class="text-muted small d-block">Sân bóng đặt:</span>
                                    <strong class="text-success fs-5">
                                        {{-- Bạn kiểm tra tên biến relation tới sân của bạn ở đây (ví dụ: $booking->field->name) --}}
                                        {{ $booking->field_name ?? 'Sân số 1 (Sân 7 người)' }}
                                    </strong>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Ngày đá:</span>
                                        <strong class="text-dark">
                                            {{-- Hiển thị ngày đá thực tế, nếu dùng trường khác bạn nhớ đổi lại tên biến --}}
                                            {{ isset($booking->play_date) ? \Carbon\Carbon::parse($booking->play_date)->format('d/m/Y') : '04/07/2026' }}
                                        </strong>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted small d-block">Khung giờ:</span>
                                        <strong class="text-primary">
                                            {{-- Hiển thị giờ đá cụ thể --}}
                                            {{ $booking->start_time ?? '17:30' }} - {{ $booking->end_time ?? '19:00' }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cột bên phải: Tóm tắt tổng tiền -->
                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-center align-items-center text-center">
                                <span class="text-muted small mb-1">Tổng số tiền thanh toán</span>
                                <h3 class="text-danger fw-bold mb-0">
                                    {{ number_format($booking->total_amount) }} <span class="fs-6 text-secondary">VNĐ</span>
                                </h3>
                                <span class="badge bg-warning text-dark mt-2 px-3 py-1.5 rounded-pill small">
                                    Chờ thanh toán
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- KẾT THÚC PHẦN THÊM MỚI -->

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
                        
                        {{-- Đã tối ưu cấu trúc thẻ label để click vào bất kỳ vị trí nào trên ô đều nhận chọn radio --}}
                        @foreach($paymentMethods as $method)
                            <label class="form-check card p-3 mb-2 border @if($loop->first) border-success @endif d-flex flex-row align-items-center" 
                                   for="method-{{ $method->id }}" 
                                   style="cursor: pointer; gap: 12px;">
                                <input class="form-check-input payment-method-radio m-0" type="radio" name="payment_method_id" 
                                    id="method-{{ $method->id }}" value="{{ $method->id }}" data-code="{{ $method->code }}"
                                    @if($loop->first) checked @endif>
                                <span class="fw-bold text-dark">
                                    {{ $method->name }}
                                </span>
                            </label>
                        @endforeach

                        <div id="qr-payment-area" class="text-center my-4 p-3 bg-light rounded d-none">
                            <p class="mb-2 fw-bold text-muted">Vui lòng dùng ứng dụng Ngân hàng (Mobile Banking) quét mã QR dưới đây:</p>
                            <img src="{{ $qrCodeUrl }}" alt="Mã QR Chuyển khoản VietQR" class="img-fluid border p-2 bg-white" style="max-width: 280px;">
                            <p class="text-danger small mt-2 mb-0">*Lưu ý: Giữ nguyên nội dung chuyển khoản được tạo sẵn trong mã QR.</p>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg">Xác nhận hoàn tất đơn hàng</button>
                            <a href="{{ route('user.bookings.index') }}" class="btn btn-link text-muted">Thanh toán sau / Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kịch bản JS ẩn/hiện vùng quét mã QR khi đổi lựa chọn -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.payment-method-radio');
        const qrArea = document.getElementById('qr-payment-area');

        function toggleQRArea() {
            const selectedRadio = document.querySelector('.payment-method-radio:checked');
            if (selectedRadio && selectedRadio.getAttribute('data-code') === 'BANK_TRANSFER') {
                qrArea.classList.remove('d-none');
            } else {
                qrArea.classList.add('d-none');
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Đổi viền active cho card trực quan dựa trên thẻ label bọc ngoài
                document.querySelectorAll('.form-check.card').forEach(c => c.classList.remove('border-success'));
                this.closest('.form-check.card').classList.add('border-success');
                toggleQRArea();
            });
        });

        // Chạy kiểm tra lần đầu tiên khi load trang
        toggleQRArea();
    });
</script>
@endsection