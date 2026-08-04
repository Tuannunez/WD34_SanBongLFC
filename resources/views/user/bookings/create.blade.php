@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Đặt sân bóng</h3>
            <p class="text-muted mb-0">
                Chọn đặt theo từng buổi hoặc đăng ký cố định cả tháng
            </p>
        </div>

        <a href="{{ route('stadiums.show', $stadium->id) }}" class="btn btn-secondary rounded-3">
            Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 mb-4">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ======================================================== --}}
    {{-- 🔥 TAB CHUYỂN ĐỔI: ĐẶT THEO BUỔI VS ĐẶT CỐ ĐỊNH THEO THÁNG --}}
    {{-- ======================================================== --}}
    <ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm mb-4" id="bookingTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 py-3 fw-bold fs-6" id="single-tab" data-bs-toggle="tab" data-bs-target="#single-booking" type="button" role="tab">
                <i class="bi bi-calendar-event me-2"></i> 1. Đặt Lịch Theo Buổi (Lẻ)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 py-3 fw-bold fs-6 text-success" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly-booking" type="button" role="tab">
                <i class="bi bi-calendar-range-fill me-2"></i> 2. Đặt Cố Định Theo Tháng (Giữ Lịch Đội)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="bookingTabContent">
        
        {{-- ========================================== --}}
        {{-- TAB 1: FORM ĐẶT LỊCH THEO BUỔI (LẺ) --}}
        {{-- ========================================== --}}
        <div class="tab-pane fade show active" id="single-booking" role="tabpanel">
            <form action="{{ route('user.bookings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-semibold mb-0">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>
                                    Thông tin đặt sân lẻ
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Cụm sân</label>
                                    <input type="text" class="form-control rounded-3" value="{{ $stadium->name }}" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Chọn sân</label>
                                    <select name="field_id" class="form-select rounded-3" required>
                                        <option value="">-- Chọn sân --</option>
                                        @foreach($fields as $field)
                                            <option value="{{ $field->id }}" @selected(old('field_id') == $field->id)>
                                                {{ $field->name }}
                                                @if(isset($field->price))
                                                    - {{ number_format($field->price, 0, ',', '.') }}đ
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ngày đặt</label>
                                    <input type="date"
                                           name="booking_date"
                                           value="{{ old('booking_date', now()->format('Y-m-d')) }}"
                                           min="{{ now()->format('Y-m-d') }}"
                                           class="form-control rounded-3 @error('booking_date') is-invalid @enderror"
                                           required>
                                    @error('booking_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Số điện thoại liên hệ</label>
                                    <input type="text"
                                           name="customer_phone"
                                           value="{{ old('customer_phone', Auth::user()->phone ?? '') }}"
                                           class="form-control rounded-3 @error('customer_phone') is-invalid @enderror"
                                           placeholder="Nhập số điện thoại liên hệ">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Khung giờ</label>
                                    <select name="time_slot_id" class="form-select rounded-3" required>
                                        <option value="">-- Chọn khung giờ --</option>
                                        @foreach($timeSlots as $timeSlot)
                                            <option value="{{ $timeSlot->id }}" @selected(old('time_slot_id') == $timeSlot->id)>
                                                {{ $timeSlot->start_time ?? '' }} - {{ $timeSlot->end_time ?? '' }}
                                                @if(isset($timeSlot->price))
                                                    - {{ number_format($timeSlot->price, 0, ',', '.') }}đ
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                

                                <div>
                                    <label class="form-label fw-semibold">Ghi chú</label>
                                    <textarea name="note" rows="3" class="form-control rounded-3" placeholder="Nhập ghi chú nếu có...">{{ old('note') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-semibold mb-0">
                                    <i class="bi bi-basket text-success me-2"></i>
                                    Dịch vụ đi kèm
                                </h5>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Dịch vụ</th>
                                                <th>Giá</th>
                                                <th style="width: 180px;">Số lượng</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @forelse($services as $index => $service)
                                                <tr data-service-index="{{ $index }}" class="service-row">
                                                    <td class="ps-4">
                                                        <input class="form-check-input service-select" type="checkbox"
                                                               id="serviceCheck{{ $index }}" data-index="{{ $index }}">
                                                        <input type="hidden" name="services[{{ $index }}][id]" value="{{ $service->id ?? '' }}">

                                                        <label for="serviceCheck{{ $index }}" class="d-flex align-items-center w-100 mb-0" style="cursor: pointer;">
                                                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                                                 style="width: 40px; height: 40px;">
                                                                <i class="bi bi-cup-straw"></i>
                                                            </div>

                                                            <div>
                                                                <div class="fw-semibold">{{ $service->name }}</div>
                                                                <small class="text-muted">
                                                                    {{ $service->description ?? 'Dịch vụ đi kèm khi đặt sân' }}
                                                                </small>
                                                            </div>
                                                        </label>
                                                    </td>

                                                    <td class="fw-bold text-success">
                                                        {{ number_format($service->price ?? 0, 0, ',', '.') }}đ / {{ $service->unit ?? 'lượt' }}
                                                    </td>

                                                    <td>
                                                        <input type="number"
                                                               name="services[{{ $index }}][quantity]"
                                                               value="0"
                                                               min="0"
                                                               class="form-control rounded-3 service-qty"
                                                               data-index="{{ $index }}"
                                                               disabled>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-5 text-muted">
                                                        Chưa có dịch vụ đi kèm.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 20px;">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-semibold mb-0">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    Xác nhận đặt sân lẻ
                                </h5>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small">
                                    Đơn đặt theo buổi sẽ áp dụng quy định hủy hoàn tiền trước 24 giờ.
                                </p>

                                <button type="submit" class="btn btn-primary rounded-3 w-100 py-2.5 fw-bold shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Gửi đơn đặt sân lẻ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ========================================== --}}
        {{-- 🔥 TAB 2: FORM ĐẶT CỐ ĐỊNH THEO THÁNG --}}
        {{-- ========================================== --}}
        <div class="tab-pane fade" id="monthly-booking" role="tabpanel">
            <form action="{{ route('user.bookings.storeMonthly') }}" method="POST">
                @csrf
                <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">

                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-success-subtle border-0 py-3">
                                <h5 class="fw-bold text-success mb-0">
                                    <i class="bi bi-calendar-range-fill me-2"></i>
                                    Đăng ký lịch cố định cả tháng
                                </h5>
                            </div>

                            <div class="card-body p-4">
                                <div class="alert alert-warning border-0 rounded-3 small mb-4">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    <strong>Quy định lịch tháng:</strong> Hệ thống sẽ giữ trọn vẹn tất cả các buổi đá trong tháng bạn đã chọn. Đơn lịch tháng <strong>KHÔNG hỗ trợ hủy hay hoàn tiền</strong> sau khi thanh toán.
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Chọn sân <span class="text-danger">*</span></label>
                                        <select name="field_id" class="form-select rounded-3" required>
                                            <option value="">-- Chọn sân --</option>
                                            @foreach($fields as $field)
                                                <option value="{{ $field->id }}">{{ $field->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Khung giờ đá cố định <span class="text-danger">*</span></label>
                                        <select name="time_slot_id" class="form-select rounded-3" required>
                                            <option value="">-- Chọn khung giờ --</option>
                                            @foreach($timeSlots as $timeSlot)
                                                <option value="{{ $timeSlot->id }}">
                                                    {{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Thứ cố định trong tuần <span class="text-danger">*</span></label>
                                        <select name="day_of_week" class="form-select rounded-3" required>
                                            <option value="0" selected>Chủ Nhật (Tất cả Chủ Nhật)</option>
                                            <option value="6">Thứ Bảy (Tất cả Thứ Bảy)</option>
                                            <option value="5">Thứ Sáu</option>
                                            <option value="4">Thứ Năm</option>
                                            <option value="3">Thứ Tư</option>
                                            <option value="2">Thứ Ba</option>
                                            <option value="1">Thứ Hai</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Tháng <span class="text-danger">*</span></label>
                                        <select name="month" class="form-select rounded-3" required>
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}" @selected($m == now()->month)>Tháng {{ $m }}</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Năm <span class="text-danger">*</span></label>
                                        <select name="year" class="form-select rounded-3" required>
                                            <option value="2026" selected>2026</option>
                                            <option value="2027">2027</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-semibold">Hình thức thanh toán</label>
                                        <div class="d-flex gap-4 p-3 bg-light rounded-3 border">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_type" id="mPayDeposit" value="deposit" checked>
                                                <label class="form-check-label fw-medium" for="mPayDeposit">
                                                    Đặt cọc 30% tổng tiền cả tháng
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_type" id="mPayFull" value="full">
                                                <label class="form-check-label fw-medium text-success" for="mPayFull">
                                                    Thanh toán đủ 100% cả tháng
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 position-sticky" style="top: 20px;">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="fw-semibold mb-0"><i class="bi bi-shield-check text-success me-2"></i>Xác nhận lịch tháng</h5>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">Hệ thống sẽ tự động quét toàn bộ các ngày trong tháng rơi vào Thứ bạn đã chọn để giữ sân.</p>
                                <button type="submit" class="btn btn-success rounded-3 w-100 py-2.5 fw-bold shadow-sm">
                                    <i class="bi bi-calendar-plus me-1"></i> Tạo Đơn Đặt Lịch Tháng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
    .service-row { cursor: pointer; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function toggleQty(index, checked) {
            const qty = document.querySelector('.service-qty[data-index="' + index + '"]');
            if (!qty) return;
            qty.disabled = !checked;
            if (!checked) qty.value = 0;
            else if (checked && (!qty.value || qty.value == 0)) qty.value = 1;
            const row = document.querySelector('.service-row[data-service-index="' + index + '"]');
            if (row) {
                row.classList.toggle('table-primary', checked);
            }
        }

        document.querySelectorAll('.service-select').forEach(function (cb) {
            cb.addEventListener('change', function (e) {
                const idx = e.target.getAttribute('data-index');
                toggleQty(idx, e.target.checked);
            });
            toggleQty(cb.getAttribute('data-index'), cb.checked);
        });

        document.querySelectorAll('.service-row').forEach(function (row) {
            row.addEventListener('click', function (e) {
                const tag = e.target.tagName;
                if (['INPUT', 'BUTTON', 'A'].includes(tag)) return;
                const idx = row.getAttribute('data-service-index');
                const cb = document.getElementById('serviceCheck' + idx);
                if (!cb) return;
                cb.checked = !cb.checked;
                cb.dispatchEvent(new Event('change'));
            });
        });

        const bookingForm = document.getElementById('singleBookingForm');
        if (bookingForm) {
            const customerPhoneInput = bookingForm.querySelector('[name="customer_phone"]');

            function markInvalidFields(form) {
                form.querySelectorAll(':invalid').forEach(function (field) {
                    field.classList.add('is-invalid');
                });
            }

            if (customerPhoneInput) {
                customerPhoneInput.addEventListener('input', function () {
                    if (customerPhoneInput.validity.valid) {
                        customerPhoneInput.classList.remove('is-invalid');
                    }
                });
                customerPhoneInput.addEventListener('invalid', function () {
                    customerPhoneInput.classList.add('is-invalid');
                });
            }

            bookingForm.addEventListener('submit', function (event) {
                if (!bookingForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    markInvalidFields(bookingForm);
                }
            });
        }
    });
</script>
@endsection