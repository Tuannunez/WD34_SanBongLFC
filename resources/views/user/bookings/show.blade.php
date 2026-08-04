@extends('layouts.app')

@section('title', 'Chi tiết đơn đặt sân')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết đơn đặt sân #{{ $booking->id }}</h3>
            <p class="text-muted mb-0">Thông tin sân, khung giờ và dịch vụ đi kèm</p>
        </div>

        <a href="{{ route('user.bookings.index') }}" class="btn btn-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

    @php
        $status = $booking->status ?? 'pending';

        $statusText = match ($status) {
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            default => 'Chờ xác nhận',
        };

        $statusClass = match ($status) {
            'confirmed' => 'bg-success-subtle text-success',
            'completed' => 'bg-primary-subtle text-primary',
            'cancelled' => 'bg-danger-subtle text-danger',
            default => 'bg-warning-subtle text-warning',
        };

        $totalMoney = $booking->total_price
            ?? $booking->total_amount
            ?? $booking->total
            ?? $booking->amount
            ?? 0;

        $bookingDate = $booking->booking_date
            ?? $booking->date
            ?? null;

        $customerName = $booking->customer_name
            ?? $booking->name
            ?? Auth::user()->name
            ?? 'Khách hàng';

        $customerEmail = $booking->customer_email
            ?? $booking->email
            ?? Auth::user()->email
            ?? '-';

        $customerPhone = $booking->customer_phone
            ?? $booking->phone
            ?? '-';
    @endphp

@php
            $paidAmount = (float) ($booking->paid_amount ?? 0);
            $depositAmount = (float) ($booking->deposit_amount ?? 0);
            $serviceTotal = collect($bookingServices ?? [])->sum(function ($item) {
                return (float) ($item->total ?? (($item->quantity ?? 0) * ($item->price ?? 0)));
            });
            $remainingAmount = max(0, $totalMoney - $paidAmount);
            $paymentType = strtolower(trim((string) ($booking->payment_type ?? 'deposit')));
            $paymentStatus = strtolower(trim((string) ($booking->payment_status ?? 'unpaid')));
            $paymentTypeLabel = match ($paymentType) {
                'full' => 'Thanh toán 100%',
                'deposit_50' => 'Cọc 50%',
                default => 'Cọc 30%',
            };
            $paymentStatusLabel = match (true) {
                $remainingAmount <= 0 => 'Đã thanh toán xong',
                $paymentType === 'full' => 'Đã thanh toán 100%',
                in_array($paymentType, ['deposit', 'deposit_50']) && $paidAmount > 0 => 'Đã thanh toán cọc',
                default => 'Chưa thanh toán',
            };
            $remainingLabel = match (true) {
                $paymentType === 'full' && $remainingAmount > 0 => 'Phần còn lại là tiền dịch vụ mới',
                in_array($paymentType, ['deposit', 'deposit_50']) && $paidAmount > 0 => 'Còn lại = phần chưa thanh toán + phí dịch vụ',
                in_array($paymentType, ['deposit', 'deposit_50']) && $paidAmount <= 0 => 'Còn lại = tiền cọc + phí dịch vụ',
                default => 'Số tiền còn lại cần thanh toán',
            };
        @endphp

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted mb-1">Mã đơn</p>
                        <h4 class="fw-bold mb-0">#{{ $booking->id }}</h4>

                        @if(!empty($booking->booking_code))
                            <small class="text-muted">{{ $booking->booking_code }}</small>
                        @elseif(!empty($booking->code))
                            <small class="text-muted">{{ $booking->code }}</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted mb-1">Trạng thái</p>
                        <span class="badge {{ $statusClass }} px-3 py-2">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted mb-1">Tổng tiền</p>
                        <h4 class="fw-bold mb-0 text-success">
                            {{ number_format((float) $totalMoney, 0, ',', '.') }}đ
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div class="col-md-3">
                                <p class="text-muted mb-1">Hình thức thanh toán</p>
                                <div class="fw-semibold">{{ $paymentTypeLabel }}</div>
                            </div>
                            <div class="col-md-3">
                                <p class="text-muted mb-1">Trạng thái thanh toán</p>
                                <div class="fw-semibold">{{ $paymentStatusLabel }}</div>
                            </div>
                            <div class="col-md-2">
                                <p class="text-muted mb-1">Đã thanh toán</p>
                                <div class="fw-semibold">
                                    {{ number_format($paidAmount, 0, ',', '.') }}đ
                                </div>
                            </div>
                            <div class="col-md-2">
                                <p class="text-muted mb-1">Tiền dịch vụ</p>
                                <div class="fw-semibold">
                                    {{ number_format($serviceTotal, 0, ',', '.') }}đ
                                </div>
                            </div>
                            <div class="col-md-2">
                                <p class="text-muted mb-1">Số tiền còn lại</p>
                                <div class="fw-semibold text-danger">
                                    {{ number_format($remainingAmount, 0, ',', '.') }}đ
                                </div>
                            </div>
                        </div>

                        @if($remainingAmount > 0)
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    {{ $remainingLabel }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-person-circle text-primary me-2"></i>
                Thông tin khách hàng
            </h5>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="text-muted mb-1">Họ tên</label>
                    <div class="fw-semibold">{{ $customerName }}</div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Email</label>
                    <div class="fw-semibold">{{ $customerEmail }}</div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Số điện thoại</label>
                    <div class="fw-semibold">{{ $customerPhone }}</div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Ngày tạo đơn</label>
                    <div class="fw-semibold">
                        {{ !empty($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Ngày đặt</label>
                    <div class="fw-semibold">
                        @if($bookingDate)
                            {{ \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="text-muted mb-1">Ghi chú</label>
                    <div class="fw-semibold">
                        {{ $booking->note ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-dribbble text-success me-2"></i>
                Thông tin sân đã đặt
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Sân</th>
                            <th>Ngày đặt</th>
                            <th>Khung giờ</th>
                            <th>Giá sân</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($bookingDetails ?? collect()) as $detail)
                            @php
                                $detailPrice = $detail->price
                                    ?? $detail->field_price
                                    ?? $detail->total_price
                                    ?? $detail->total
                                    ?? $detail->amount
                                    ?? $detail->field_price_per_hour
                                    ?? 0;

                                $detailDate = $detail->booking_date
                                    ?? $detail->date
                                    ?? $bookingDate
                                    ?? null;

                                $startTime = $detail->slot_start_time
                                    ?? $detail->start_time
                                    ?? null;

                                $endTime = $detail->slot_end_time
                                    ?? $detail->end_time
                                    ?? null;
                            @endphp

                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-dribbble"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $detail->field_name ?? 'Sân chưa xác định' }}
                                            </div>
                                            <small class="text-muted">Sân đã đặt</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($detailDate)
                                        {{ \Carbon\Carbon::parse($detailDate)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        @if($startTime || $endTime)
                                            {{ $startTime ?? '' }} - {{ $endTime ?? '' }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>

                                <td class="fw-bold text-success">
                                    {{ number_format((float) $detailPrice, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có chi tiết sân.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-basket text-primary me-2"></i>
                Dịch vụ đi kèm
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Dịch vụ</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($bookingServices ?? collect()) as $bookingService)
                            @php
                                $servicePrice = $bookingService->price ?? 0;
                                $serviceTotal = $bookingService->total
                                    ?? $bookingService->total_price
                                    ?? (($bookingService->quantity ?? 0) * $servicePrice);
                            @endphp

                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-cup-straw"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $bookingService->service_name ?? 'Không có dịch vụ' }}
                                            </div>
                                            <small class="text-muted">Dịch vụ đi kèm</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        {{ $bookingService->quantity ?? 0 }}
                                    </span>
                                </td>

                                <td class="fw-semibold">
                                    {{ number_format((float) $servicePrice, 0, ',', '.') }}đ
                                </td>

                                <td class="fw-bold text-success">
                                    {{ number_format((float) $serviceTotal, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Đơn này chưa chọn dịch vụ đi kèm.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(!in_array($status, ['cancelled', 'completed']))
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-plus-circle text-primary me-2"></i>
                    Thêm dịch vụ sau khi đặt sân
                </h5>
                <span class="badge bg-success-subtle text-success">+ Thêm dịch vụ</span>
            </div>
            <div class="card-body">
                @if($availableServices->isEmpty())
                    <div class="alert alert-warning mb-0">Hiện tại chưa có dịch vụ nào để thêm.</div>
                @else
                    @if($errors->has('services'))
                        <div class="alert alert-danger mb-3">{{ $errors->first('services') }}</div>
                    @endif
                    <form action="{{ route('user.bookings.services.store', $booking->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            @foreach($availableServices as $service)
                                <div class="col-md-6">
                                    <div class="card border rounded-3 h-100">
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="service_select_{{ $service->id }}" name="services[{{ $service->id }}][selected]" value="1">
                                                <label class="form-check-label fw-semibold" for="service_select_{{ $service->id }}">
                                                    {{ $service->name }}
                                                </label>
                                            </div>

                                            <p class="mb-2 text-muted">Giá: <strong>{{ number_format((float)$service->price, 0, ',', '.') }}đ</strong> / {{ $service->unit ?? 'đơn vị' }}</p>
                                            <p class="mb-3 text-secondary small">{{ $service->description ?? 'Dịch vụ bổ sung cho đơn đặt sân.' }}</p>

                                            <input type="hidden" name="services[{{ $service->id }}][id]" value="{{ $service->id }}">

                                            <div class="d-flex align-items-center gap-3">
                                                <label class="form-label mb-0">Số lượng</label>
                                                <input type="number" name="services[{{ $service->id }}][quantity]" value="1" min="1" class="form-control form-control-sm" style="width: 90px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary rounded-3 px-4">
                                <i class="bi bi-cart-plus me-1"></i>
                                Xác nhận thêm dịch vụ
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    @if($status === 'completed')
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-star-fill text-warning me-2"></i>
                    Đánh giá đơn hoàn thành
                </h5>
            </div>
            <div class="card-body">
                @if(empty($bookingReview))
                    <form action="{{ route('user.bookings.review.store', $booking->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Điểm đánh giá</label>
                            <select name="rating" class="form-select rounded-3 @error('rating') is-invalid @enderror" required>
                                <option value="">-- Chọn điểm --</option>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }} sao</option>
                                @endfor
                            </select>
                            @error('rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nội dung đánh giá</label>
                            <textarea name="comment" rows="4" class="form-control rounded-3 @error('comment') is-invalid @enderror" placeholder="Chia sẻ cảm nhận của bạn...">{{ old('comment') }}</textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success rounded-3 px-4">
                            <i class="bi bi-send me-1"></i>
                            Gửi đánh giá
                        </button>
                    </form>
                @else
                    <div class="alert alert-success rounded-4">
                        Bạn đã đánh giá đơn này với <strong>{{ $bookingReview->rating }} sao</strong>.
                    </div>
                    <p class="text-muted">"{{ $bookingReview->comment ?? 'Không có nội dung đánh giá.' }}"</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection