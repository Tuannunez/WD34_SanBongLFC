@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Đặt sân</h3>
            <p class="text-muted mb-0">
                Chọn sân, ngày đặt, khung giờ và dịch vụ đi kèm
            </p>
        </div>

        <a href="{{ route('stadiums.show', $stadium->id) }}" class="btn btn-secondary rounded-3">
            Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.bookings.store') }}" method="POST">
        @csrf

        <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-calendar-check text-primary me-2"></i>
                            Thông tin đặt sân
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Cụm sân</label>
                            <input type="text"
                                   class="form-control rounded-3"
                                   value="{{ $stadium->name }}"
                                   disabled>
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
                                   class="form-control rounded-3"
                                   required>
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
                            <textarea name="note"
                                      rows="3"
                                      class="form-control rounded-3"
                                      placeholder="Nhập ghi chú nếu có...">{{ old('note') }}</textarea>
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
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
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
                                                </div>

                                                <input type="hidden" name="services[{{ $index }}][id]" value="{{ $service->id }}">
                                            </td>

                                            <td class="fw-bold text-success">
                                                {{ number_format($service->price ?? 0, 0, ',', '.') }}đ
                                            </td>

                                            <td>
                                                <input type="number"
                                                       name="services[{{ $index }}][quantity]"
                                                       value="0"
                                                       min="0"
                                                       class="form-control rounded-3">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5">
                                                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                                <span class="text-muted">Chưa có dịch vụ đi kèm.</span>
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
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-semibold mb-0">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Xác nhận đặt sân
                        </h5>
                    </div>

                    <div class="card-body">
                        <p class="text-muted">
                            Sau khi gửi đơn, admin sẽ kiểm tra và xác nhận trạng thái đơn đặt sân của bạn.
                        </p>

                        <button type="submit" class="btn btn-primary rounded-3 w-100 py-2">
                            <i class="bi bi-check-circle me-1"></i>
                            Gửi đơn đặt sân
                        </button>

                        <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-secondary rounded-3 w-100 mt-2">
                            <i class="bi bi-clock-history me-1"></i>
                            Xem đơn của tôi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection