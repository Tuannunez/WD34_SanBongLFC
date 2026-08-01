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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

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


    @php
        $timezone = config(
            'booking_lifecycle.timezone',
            config('app.timezone', 'Asia/Ho_Chi_Minh')
        );

        $checkInEarlyMinutes = max(
            0,
            (int) config('booking_lifecycle.check_in_early_minutes', 15)
        );

        $noShowGraceMinutes = max(
            0,
            (int) config('booking_lifecycle.no_show_grace_minutes', 15)
        );

        $scheduleWindows = collect($bookingDetails ?? [])
            ->map(function ($detail) use ($timezone, $bookingDate) {
                $date = $detail->booking_date
                    ?? $detail->date
                    ?? $bookingDate
                    ?? null;

                $startTime = $detail->slot_start_time
                    ?? $detail->start_time
                    ?? null;

                $endTime = $detail->slot_end_time
                    ?? $detail->end_time
                    ?? null;

                if (!$date || !$startTime || !$endTime) {
                    return null;
                }

                try {
                    $startsAt = \Carbon\CarbonImmutable::parse(
                        $date . ' ' . $startTime,
                        $timezone
                    );

                    $endsAt = \Carbon\CarbonImmutable::parse(
                        $date . ' ' . $endTime,
                        $timezone
                    );

                    if ($endsAt->lessThanOrEqualTo($startsAt)) {
                        $endsAt = $endsAt->addDay();
                    }

                    return [
                        'starts_at' => $startsAt,
                        'ends_at' => $endsAt,
                    ];
                } catch (\Throwable $exception) {
                    return null;
                }
            })
            ->filter()
            ->values();

        $firstSchedule = $scheduleWindows
            ->sortBy(fn ($window) => $window['starts_at']->getTimestamp())
            ->first();

        $lastSchedule = $scheduleWindows
            ->sortByDesc(fn ($window) => $window['ends_at']->getTimestamp())
            ->first();

        $startsAt = $firstSchedule['starts_at'] ?? null;
        $endsAt = $lastSchedule['ends_at'] ?? null;

        $checkInOpensAt = $startsAt?->subMinutes($checkInEarlyMinutes);
        $checkInDeadlineAt = $startsAt?->addMinutes($noShowGraceMinutes);
        $now = \Carbon\CarbonImmutable::now($timezone);

        $normalizedStatus = strtolower((string) ($booking->status ?? 'pending'));
        $usageStatus = strtolower(
            (string) ($booking->usage_status ?? 'not_checked_in')
        );
        $paymentStatus = strtolower(
            (string) ($booking->payment_status ?? 'unpaid')
        );

        $isPaid = in_array(
            $paymentStatus,
            ['deposit_paid', 'paid'],
            true
        )
            || (float) ($booking->paid_amount ?? 0) > 0
            || (bool) ($booking->is_deposit_paid ?? false);

        $canCheckIn = $normalizedStatus === 'confirmed'
            && $usageStatus === 'not_checked_in'
            && $isPaid
            && $checkInOpensAt
            && $checkInDeadlineAt
            && $now->greaterThanOrEqualTo($checkInOpensAt)
            && $now->lessThan($checkInDeadlineAt);

        $usageStatusText = match ($usageStatus) {
            'checked_in' => 'Đang sử dụng sân',
            'checked_out' => 'Đã check-out',
            default => 'Chưa check-in',
        };

        $usageStatusClass = match ($usageStatus) {
            'checked_in' => 'bg-success-subtle text-success',
            'checked_out' => 'bg-primary-subtle text-primary',
            default => 'bg-secondary-subtle text-secondary',
        };

        $checkedInDisplay = null;
        if (!empty($booking->checked_in_at)) {
            try {
                $checkedInDisplay = \Carbon\CarbonImmutable::parse(
                    $booking->checked_in_at,
                    $timezone
                )->format('H:i d/m/Y');
            } catch (\Throwable $exception) {
                $checkedInDisplay = (string) $booking->checked_in_at;
            }
        }

        $checkedOutDisplay = null;
        if (!empty($booking->checked_out_at)) {
            try {
                $checkedOutDisplay = \Carbon\CarbonImmutable::parse(
                    $booking->checked_out_at,
                    $timezone
                )->format('H:i d/m/Y');
            } catch (\Throwable $exception) {
                $checkedOutDisplay = (string) $booking->checked_out_at;
            }
        }

        $forfeitedDeposit = (float) (
            $booking->deposit_forfeited_amount
            ?? $booking->deposit_amount
            ?? 0
        );
    @endphp

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-box-arrow-in-right text-success me-2"></i>
                    Check-in và sử dụng sân
                </h5>

                <span class="badge {{ $usageStatusClass }} px-3 py-2">
                    {{ $usageStatusText }}
                </span>
            </div>
        </div>

        <div class="card-body">
            @if(!empty($booking->no_show_at))
                <div class="alert alert-danger rounded-4 mb-0">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-person-x-fill fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold mb-1">Đơn đã bị hủy do khách không đến</div>
                            <div>
                                Bạn không check-in trong {{ $noShowGraceMinutes }} phút
                                sau giờ bắt đầu. Tiền cọc
                                <strong>{{ number_format($forfeitedDeposit, 0, ',', '.') }}đ</strong>
                                không được hoàn lại.
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($usageStatus === 'checked_in')
                <div class="alert alert-success rounded-4 mb-0">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold mb-1">Bạn đã check-in thành công</div>
                            <div>
                                Thời gian check-in:
                                <strong>{{ $checkedInDisplay ?? '-' }}</strong>.
                                Hệ thống sẽ tự check-out
                                @if($endsAt)
                                    lúc <strong>{{ $endsAt->format('H:i d/m/Y') }}</strong>
                                @else
                                    khi hết giờ sân
                                @endif
                                .
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($usageStatus === 'checked_out' || $normalizedStatus === 'completed')
                <div class="alert alert-primary rounded-4 mb-0">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-check2-all fs-4 me-3"></i>
                        <div>
                            <div class="fw-bold mb-1">Phiên sử dụng sân đã hoàn tất</div>
                            <div>
                                Check-in: <strong>{{ $checkedInDisplay ?? '-' }}</strong><br>
                                Check-out: <strong>{{ $checkedOutDisplay ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($normalizedStatus === 'confirmed' && $startsAt && $checkInDeadlineAt)
                <div class="row g-3 align-items-stretch">
                    <div class="col-lg-7">
                        <div class="border rounded-4 p-3 h-100">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="text-muted small">Giờ bắt đầu</div>
                                    <div class="fw-bold">
                                        {{ $startsAt->format('H:i d/m/Y') }}
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="text-muted small">Giờ kết thúc</div>
                                    <div class="fw-bold">
                                        {{ $endsAt?->format('H:i d/m/Y') ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="text-muted small">Được check-in từ</div>
                                    <div class="fw-semibold text-primary">
                                        {{ $checkInOpensAt?->format('H:i d/m/Y') ?? '-' }}
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="text-muted small">Hạn cuối check-in</div>
                                    <div class="fw-semibold text-danger">
                                        {{ $checkInDeadlineAt->format('H:i d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <p class="small text-muted mb-0">
                                Nếu bạn không check-in trong
                                <strong>{{ $noShowGraceMinutes }} phút</strong>
                                sau giờ bắt đầu, đơn sẽ tự hủy và tiền cọc
                                không được hoàn lại.
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="border rounded-4 p-3 h-100 d-flex flex-column justify-content-center">
                            @if(!$isPaid)
                                <div class="alert alert-warning rounded-3 mb-0">
                                    Đơn chưa ghi nhận thanh toán nên chưa thể check-in.
                                </div>
                            @elseif($canCheckIn)
                                <form
                                    method="POST"
                                    action="{{ route('user.bookings.check-in', $booking->id) }}"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-success btn-lg rounded-3 w-100"
                                        onclick="return confirm('Bạn xác nhận đã có mặt tại sân và muốn check-in?')"
                                    >
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        Check-in ngay
                                    </button>
                                </form>

                                <div class="small text-muted text-center mt-2">
                                    Sau khi check-in, hệ thống sẽ tự check-out khi hết giờ.
                                </div>
                            @elseif($checkInOpensAt && $now->lessThan($checkInOpensAt))
                                <div class="alert alert-info rounded-3 mb-0">
                                    Nút check-in sẽ mở lúc
                                    <strong>{{ $checkInOpensAt->format('H:i d/m/Y') }}</strong>.
                                </div>
                            @else
                                <div class="alert alert-danger rounded-3 mb-0">
                                    Đã quá hạn check-in. Scheduler sẽ tự xử lý đơn vắng mặt.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($normalizedStatus === 'pending')
                <div class="alert alert-warning rounded-4 mb-0">
                    Đơn đang chờ xác nhận thanh toán. Bạn chỉ có thể check-in
                    sau khi đơn chuyển sang trạng thái đã xác nhận.
                </div>
            @elseif($normalizedStatus === 'cancelled')
                <div class="alert alert-secondary rounded-4 mb-0">
                    Đơn đã bị hủy nên không thể check-in.
                </div>
            @else
                <div class="alert alert-secondary rounded-4 mb-0">
                    Chưa đủ dữ liệu lịch sân để xác định thời gian check-in.
                </div>
            @endif
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