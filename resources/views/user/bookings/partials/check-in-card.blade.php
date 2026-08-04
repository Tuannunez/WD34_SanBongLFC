@php
    $bookingStatus = strtolower((string) ($booking->status ?? 'pending'));
    $usageStatus = strtolower((string) ($booking->usage_status ?? ''));

    $checkedInAt = null;
    $checkedOutAt = null;

    if (!empty($booking->checked_in_at)) {
        try {
            $checkedInAt = \Carbon\Carbon::parse($booking->checked_in_at);
        } catch (\Throwable $exception) {
            $checkedInAt = null;
        }
    }

    if (!empty($booking->checked_out_at)) {
        try {
            $checkedOutAt = \Carbon\Carbon::parse($booking->checked_out_at);
        } catch (\Throwable $exception) {
            $checkedOutAt = null;
        }
    }

    if ($usageStatus === '') {
        if ($checkedOutAt) {
            $usageStatus = 'checked_out';
        } elseif ($checkedInAt) {
            $usageStatus = 'checked_in';
        } else {
            $usageStatus = 'not_checked_in';
        }
    }

    $canCheckIn =
        in_array($bookingStatus, ['confirmed', 'paid'], true)
        && in_array($usageStatus, ['not_checked_in', 'waiting'], true);
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-box-arrow-in-right text-success me-2"></i>
            Check-in sân
        </h6>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success rounded-3">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger rounded-3">
                <i class="bi bi-exclamation-circle me-1"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(in_array($bookingStatus, ['cancelled', 'canceled'], true))
            <div class="alert alert-danger mb-0 rounded-3">
                <i class="bi bi-x-circle me-1"></i>
                Đơn đặt sân đã bị hủy nên không thể check-in.
            </div>

        @elseif($usageStatus === 'checked_out')
            <div class="alert alert-success mb-0 rounded-3">
                <div class="fw-bold">
                    <i class="bi bi-check2-circle me-1"></i>
                    Đã hoàn tất sử dụng sân
                </div>

                @if($checkedOutAt)
                    <div class="small mt-1">
                        Check-out lúc {{ $checkedOutAt->format('H:i d/m/Y') }}.
                    </div>
                @endif
            </div>

        @elseif(in_array($usageStatus, ['checked_in', 'in_use'], true))
            <div class="alert alert-info mb-0 rounded-3">
                <div class="fw-bold">
                    <i class="bi bi-person-check me-1"></i>
                    Bạn đã check-in
                </div>

                @if($checkedInAt)
                    <div class="small mt-1">
                        Thời gian check-in: {{ $checkedInAt->format('H:i d/m/Y') }}.
                    </div>
                @endif
            </div>

        @elseif($canCheckIn)
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="fw-bold text-dark">
                        Bạn đã đến sân?
                    </div>
                    <div class="small text-muted">
                        Bấm nút bên cạnh để xác nhận bắt đầu sử dụng sân.
                    </div>
                </div>

                <form
                    action="{{ route('user.bookings.check-in', $booking->id) }}"
                    method="POST"
                    onsubmit="return confirm('Bạn xác nhận đã đến sân và thực hiện check-in?')">

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="btn btn-success rounded-3 fw-bold px-4">

                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Check-in
                    </button>
                </form>
            </div>

        @else
            <div class="alert alert-warning mb-0 rounded-3">
                <i class="bi bi-clock me-1"></i>
                Đơn cần được quản trị viên xác nhận trước khi bạn có thể check-in.
            </div>
        @endif
    </div>
</div>