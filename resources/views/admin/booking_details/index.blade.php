@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Chi tiết đặt sân</h3>
            <p class="text-muted mb-0">Quản lý sân, ngày đặt và khung giờ trong từng đơn đặt sân</p>
        </div>
    </div>

    @php
        $detailCollection = method_exists($bookingDetails, 'getCollection')
            ? $bookingDetails->getCollection()
            : collect($bookingDetails);

        $totalMoneyOnPage = $detailCollection->sum(function ($detail) {
            return $detail->price
                ?? $detail->field_price
                ?? $detail->total_price
                ?? $detail->total
                ?? $detail->amount
                ?? $detail->field_price_per_hour
                ?? 0;
        });

        $bookingCountOnPage = $detailCollection
            ->pluck('booking_id')
            ->filter()
            ->unique()
            ->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng chi tiết</p>
                    <h4 class="fw-bold mb-0">
                        {{ method_exists($bookingDetails, 'total') ? $bookingDetails->total() : count($bookingDetails) }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Số đơn liên quan</p>
                    <h4 class="fw-bold mb-0">
                        {{ $bookingCountOnPage }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <p class="text-muted mb-1">Tổng tiền hiển thị</p>
                    <h4 class="fw-bold mb-0 text-success">
                        {{ number_format((float) $totalMoneyOnPage, 0, ',', '.') }}đ
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.booking-details.index') }}" class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text"
                           name="keyword"
                           value="{{ request('keyword') }}"
                           class="form-control rounded-3"
                           placeholder="Tìm theo mã chi tiết, mã đơn, tên sân, khách hàng...">
                </div>

                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-search me-1"></i> Tìm kiếm
                    </button>

                    <a href="{{ route('admin.booking-details.index') }}" class="btn btn-secondary rounded-3 px-4">
                        <i class="bi bi-arrow-clockwise me-1"></i> Làm mới
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-clipboard-data text-primary me-2"></i>
                Danh sách chi tiết đặt sân
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Sân</th>
                            <th>Ngày đặt</th>
                            <th>Khung giờ</th>
                            <th>Giá</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bookingDetails as $detail)
                            @php
                                $detailPrice = $detail->price
                                    ?? $detail->field_price
                                    ?? $detail->total_price
                                    ?? $detail->total
                                    ?? $detail->amount
                                    ?? $detail->field_price_per_hour
                                    ?? 0;

                                $bookingDate = $detail->booking_date
                                    ?? $detail->date
                                    ?? null;

                                $startTime = $detail->slot_start_time
                                    ?? $detail->start_time
                                    ?? null;

                                $endTime = $detail->slot_end_time
                                    ?? $detail->end_time
                                    ?? null;
                            @endphp

                            <tr>
                                <td class="ps-4 fw-semibold">
                                    #{{ $detail->id }}
                                </td>

                                <td>
                                    @if(!empty($detail->booking_id))
                                        <a href="{{ route('admin.bookings.show', $detail->booking_id) }}"
                                           class="badge bg-primary-subtle text-primary px-3 py-2 text-decoration-none">
                                            Đơn #{{ $detail->booking_id }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                            Không có đơn
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $detail->user_name ?? $detail->customer_name ?? 'Chưa có thông tin' }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $detail->user_email ?? $detail->customer_email ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-dribbble"></i>
                                        </div>

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $detail->field_name ?? 'Không có sân' }}
                                            </div>
                                            <small class="text-muted">Sân đặt</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($bookingDate)
                                        {{ \Carbon\Carbon::parse($bookingDate)->format('d/m/Y') }}
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

                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.booking-details.show', $detail->id) }}"
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(!empty($detail->booking_id))
                                        <a href="{{ route('admin.bookings.show', $detail->booking_id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-3">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có chi tiết đặt sân nào.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($bookingDetails, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $bookingDetails->links() }}
            </div>
        @endif
    </div>
</div>
@endsection