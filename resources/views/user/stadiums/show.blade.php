@extends('layouts.app')

@section('title', 'Chi tiết sân bóng')

@push('styles')
<style>
    .stadium-detail-hero {
        background: linear-gradient(135deg, #0f766e, #16a34a);
        border-radius: 28px;
        overflow: hidden;
        color: #fff;
    }

    .stadium-main-img {
        height: 430px;
        object-fit: cover;
        border-radius: 24px;
    }

    .info-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
    }

    .booking-panel {
        position: sticky;
        top: 95px;
    }

    .slot-btn {
        border: 1px solid #d1d5db;
        border-radius: 16px;
        padding: 14px;
        background: #fff;
        width: 100%;
        text-align: left;
        transition: .2s;
        color: #111827;
    }

    .slot-btn:hover:not(:disabled) {
        border-color: #16a34a;
        background: #f0fdf4;
    }

    .slot-btn.active {
        border-color: #16a34a;
        background: #dcfce7;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
    }

    .slot-btn.is-occupied {
        border-color: #fda4af;
        background: #fff1f2;
        color: #9f1239;
        opacity: .9;
    }

    .slot-btn:disabled {
        cursor: not-allowed;
        opacity: .85;
    }

    .slot-time {
        font-weight: 700;
        color: #111827;
    }

    .slot-price {
        color: #16a34a;
        font-weight: 800;
    }

    .feature-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #dcfce7;
        color: #16a34a;
        font-size: 20px;
    }

    @media (max-width: 991px) {
        .booking-panel {
            position: static;
        }

        .stadium-main-img {
            height: 280px;
        }
    }
</style>
@endpush

@section('content')
@php
    $stadiumName = $stadium->name ?? 'Sân bóng';
    $stadiumAddress = $stadium->address ?? 'Đang cập nhật địa chỉ';
    $stadiumPhone = $stadium->phone ?? 'Đang cập nhật';
    $stadiumDescription = $stadium->description ?? 'Cơ sở sân bóng chất lượng, phù hợp cho các đội bóng đặt lịch thi đấu và luyện tập.';

    $openTime = $stadium->open_time ?? '06:00';
    $closeTime = $stadium->close_time ?? '22:30';

    $image = $stadium->image
        ?? $stadium->thumbnail
        ?? $stadium->image_url
        ?? null;

    $imageUrl = $image
        ? asset('storage/' . $image)
        : asset('images/banner1.png');

    $defaultPrice = $defaultPrice ?? $stadium->price ?? 0;
@endphp

<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm">
            @foreach($errors->all() as $error)
                <div>
                    <i class="bi bi-exclamation-circle me-1"></i>
                    {{ $error }}
                </div>
            @endforeach
        </div>
    @endif

    <div class="stadium-detail-hero p-4 p-lg-5 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="mb-3">
                    <a href="{{ route('home') }}" class="btn btn-light rounded-3">
                        <i class="bi bi-arrow-left me-1"></i>
                        Quay lại trang chủ
                    </a>
                </div>

                <span class="badge bg-light text-success rounded-pill px-3 py-2 mb-3">
                    <i class="bi bi-star-fill me-1"></i>
                    Cơ sở sân bóng nổi bật
                </span>

                <h1 class="fw-bold display-6 mb-3">
                    {{ $stadiumName }}
                </h1>

                <p class="lead mb-4">
                    {{ $stadiumDescription }}
                </p>

                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <span class="badge bg-white text-success rounded-pill px-3 py-2">
                        <i class="bi bi-star-fill me-1"></i>
                        {{ $averageRating ?? 0 }}/5
                    </span>
                    <span class="text-white-75">
                        {{ optional($reviews)->count() ?? 0 }} đánh giá
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                            <div class="fw-bold mb-1">
                                <i class="bi bi-clock me-1"></i>
                                Giờ mở cửa
                            </div>
                            <div>{{ $openTime }} - {{ $closeTime }}</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                            <div class="fw-bold mb-1">
                                <i class="bi bi-cash-coin me-1"></i>
                                Giá từ
                            </div>
                            <div>{{ number_format((float) $defaultPrice, 0, ',', '.') }}đ</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="bg-white bg-opacity-10 rounded-4 p-3 h-100">
                            <div class="fw-bold mb-1">
                                <i class="bi bi-check-circle me-1"></i>
                                Trạng thái
                            </div>
                            <div>Sẵn sàng đặt sân</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <img src="{{ $imageUrl }}"
                     alt="{{ $stadiumName }}"
                     class="img-fluid w-100 stadium-main-img shadow">
            </div>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card info-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Thông tin cơ sở sân
                    </h4>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Tên cơ sở</div>
                                    <div class="fw-bold">{{ $stadiumName }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Số điện thoại</div>
                                    <div class="fw-bold">{{ $stadiumPhone }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Địa chỉ</div>
                                    <div class="fw-bold">{{ $stadiumAddress }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex gap-3">
                                <div class="feature-icon">
                                    <i class="bi bi-card-text"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Mô tả</div>
                                    <div>{{ $stadiumDescription }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card info-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-grid-3x3-gap text-success me-2"></i>
                        Danh sách sân con
                    </h4>
                </div>

                <div class="card-body p-4">
                    @php
                        $fieldSummary = $fields->groupBy(function ($field) {
                            $players = $field->fieldType?->number_of_players;
                            return $players ? 'Sân ' . $players . ' người' : 'Sân khác';
                        })->map(function ($group, $label) {
                            return ['label' => $label, 'count' => $group->count()];
                        })->values();
                    @endphp

                    @if($fields->isNotEmpty())
                        <div class="mb-3">
                            <div class="fw-semibold mb-2">Tổng quan</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($fieldSummary as $item)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                        {{ $item['count'] }} {{ $item['label'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="row g-3">
                            @foreach($fields as $field)
                                @php
                                    $players = $field->fieldType?->number_of_players;
                                    $fieldLabel = $field->name ?: 'Sân ' . ($loop->iteration);
                                @endphp
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-3 h-100 bg-light">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div>
                                                <div class="fw-bold">{{ $fieldLabel }}</div>
                                                <div class="text-muted small">
                                                    {{ $players ? 'Sân ' . $players . ' người' : 'Loại sân chưa xác định' }}
                                                </div>
                                            </div>
                                            <span class="badge bg-white text-dark">#{{ $loop->iteration }}</span>
                                        </div>
                                        @if($field->description)
                                            <div class="text-muted small mt-2">{{ $field->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light rounded-3 border mb-0">
                            Hiện chưa có sân con nào được cấu hình cho cơ sở này.
                        </div>
                    @endif
                </div>
            </div>

            <div class="card info-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-clock-history text-success me-2"></i>
                        Bảng giá theo khung giờ
                    </h4>
                </div>

                <div class="card-body p-4">
                    @forelse($priceTable as $group)
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3">
                                {{ $group['session'] }}
                            </h5>

                            <div class="table-responsive border rounded-4">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Khung giờ</th>
                                            @foreach($fields as $field)
                                                <th class="text-end">
                                                    {{ $field->name ?: 'Sân #' . $field->id }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                @foreach($group['slots'] as $slot)
                                        <tr>
                                            <td class="ps-3 fw-semibold text-nowrap">
                                                <i class="bi bi-clock me-1 text-primary"></i>{{ $slot['time'] }}
                                            </td>
                                            @foreach($fields as $field)
                                                <td class="text-end text-success fw-bold text-nowrap">
                                                    {{ number_format((float) ($slot['prices'][$field->id] ?? 0), 0, ',', '.') }}đ
                                                </td>
                                            @endforeach
                                        </tr>
                                @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light rounded-3 border mb-0">Chưa có khung giờ nào được cấu hình.</div>
                    @endforelse
                </div>
            </div>

            <div class="card info-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-chat-left-text text-info me-2"></i>
                        Đánh giá khách hàng
                    </h4>
                </div>

                <div class="card-body p-4">
                    @auth
                        @if($eligibleBookings->isNotEmpty())
                        <form action="{{ route('stadiums.reviews.store', $stadium->id) }}" method="POST" class="mb-4">
                            @csrf

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Chọn sân</label>
                                    <select name="booking_id" class="form-select rounded-3 @error('booking_id') is-invalid @enderror" required>
                                        <option value="">-- Chọn lần đặt đã hoàn thành --</option>
                                        @foreach($eligibleBookings as $booking)
                                            @php
                                                $detail = $booking->bookingDetails->first();
                                            @endphp
                                            <option value="{{ $booking->id }}" @selected(old('booking_id') == $booking->id)>
                                                Đơn #{{ $booking->id }} - {{ $detail?->field?->name ?? 'Sân' }} ({{ $detail?->booking_date }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('booking_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
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
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nội dung đánh giá</label>
                                <textarea name="comment" rows="4" class="form-control rounded-3 @error('comment') is-invalid @enderror" placeholder="Chia sẻ ý kiến của bạn...">{{ old('comment') }}</textarea>
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
                            <div class="alert alert-info rounded-3 mb-4">
                                Bạn chỉ có thể đánh giá sau khi sử dụng sân xong. Hiện chưa có lần đặt sân hoàn thành nào chưa đánh giá tại cơ sở này.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning rounded-4">
                            <strong>Đăng nhập</strong> để gửi đánh giá cho cơ sở này.
                        </div>
                    @endauth

                    <div class="border-top pt-4">
                        @if($reviews->isNotEmpty())
                            @foreach($reviews as $review)
                                <div class="mb-4 pb-4 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>{{ $review->user?->name ?? 'Khách' }}</strong>
                                            <div class="text-muted small">
                                                {{ $review->field?->name ?? 'Sân' }} - {{ $review->field?->stadium?->name ?? '' }}
                                            </div>
                                        </div>
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                            {{ $review->rating }} sao
                                        </span>
                                    </div>
                                    <p class="mb-2">{{ $review->comment ?? 'Không có nhận xét.' }}</p>
                                    <div class="text-muted small">{{ $review->created_at?->format('d/m/Y H:i') }}</div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 text-muted">
                                Chưa có đánh giá nào cho sân bóng này.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card info-card">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-shield-check text-warning me-2"></i>
                        Quy định đặt sân
                    </h4>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Đặt sân trước
                                </div>
                                <div class="text-muted">
                                    Khách hàng nên đặt sân trước để giữ lịch thi đấu.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Xác nhận từ admin
                                </div>
                                <div class="text-muted">
                                    Đơn đặt sân sẽ có trạng thái chờ xác nhận sau khi gửi.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Hủy đơn
                                </div>
                                <div class="text-muted">
                                    User chỉ nên hủy đơn khi đơn còn ở trạng thái chờ xác nhận.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100">
                                <div class="fw-bold mb-1">
                                    <i class="bi bi-check-circle text-success me-1"></i>
                                    Liên hệ hỗ trợ
                                </div>
                                <div class="text-muted">
                                    Liên hệ cơ sở sân nếu cần hỗ trợ thêm về lịch đặt.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">

            <div class="card info-card booking-panel">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="fw-bold mb-0">
                        <i class="bi bi-calendar-check text-success me-2"></i>
                        Đặt sân ngay
                    </h4>
                    <p class="text-muted mb-0 mt-1">
                        Chọn ngày và khung giờ bạn muốn đặt
                    </p>
                </div>

                <div class="card-body p-4">
                    @auth
                        <form action="{{ route('user.bookings.store.from-stadium', $stadium->id) }}"
                              method="POST"
                              id="bookingForm">
                            @csrf

                            <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">
                            <input type="hidden" name="time_slot" id="selectedTimeSlot" value="{{ old('time_slot') }}">
                            <input type="hidden" name="total_price" id="selectedPrice" value="{{ old('total_price') }}">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Ngày đặt sân <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       id="bookingDate"
                                       name="booking_date"
                                       value="{{ old('booking_date', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}"
                                       class="form-control rounded-3 @error('booking_date') is-invalid @enderror"
                                       required>

                                @error('booking_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Số điện thoại liên hệ
                                </label>
                                <input type="text"
                                       name="customer_phone"
                                       value="{{ old('customer_phone', Auth::user()->phone ?? '') }}"
                                       class="form-control rounded-3"
                                       placeholder="Nhập số điện thoại liên hệ">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Chọn sân con <span class="text-danger">*</span>
                                </label>
                                <select name="field_id"
                                        id="fieldSelect"
                                        class="form-select rounded-3">
                                    <option value="">-- Chọn sân --</option>
                                    @foreach($fields as $field)
                                        @php $players = $field->fieldType?->number_of_players; @endphp
                                        <option value="{{ $field->id }}" @selected(old('field_id') == $field->id)>
                                            {{ $field->name ?: 'Sân ' . ($loop->iteration) }}{{ $players ? ' (Sân ' . $players . ' người)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Chọn khung giờ <span class="text-danger">*</span>
                                </label>

                                <div class="accordion" id="slotAccordion">
                                    @if(empty($timeSlots))
                                        <div class="alert alert-light rounded-3 border">Chưa có khung giờ nào được cấu hình cho sân này.</div>
                                    @else
                                    @foreach($timeSlots as $groupIndex => $group)
                                        <div class="accordion-item border rounded-4 mb-2 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button {{ $groupIndex !== 0 ? 'collapsed' : '' }}"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#slotGroup{{ $groupIndex }}">
                                                    {{ $group['session'] }}
                                                </button>
                                            </h2>

                                            <div id="slotGroup{{ $groupIndex }}"
                                                 class="accordion-collapse collapse {{ $groupIndex === 0 ? 'show' : '' }}"
                                                 data-bs-parent="#slotAccordion">
                                                <div class="accordion-body">
                                                    <div class="d-grid gap-2">
                                                        @foreach($group['slots'] as $slot)
                                                            <button type="button"
                                                                    class="slot-btn js-slot-btn"
                                                                    data-slot-id="{{ $slot['id'] ?? '' }}"
                                                                    data-time="{{ $slot['time'] }}"
                                                                    data-price="{{ $slot['price'] }}"
                                                                    data-available="true">
                                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                                    <div>
                                                                        <div class="slot-time">
                                                                            {{ $slot['time'] }}
                                                                        </div>
                                                                        <small class="text-muted">Bấm để chọn</small>
                                                                    </div>

                                                                    <div class="text-end">
                                                                        <div class="slot-price">
                                                                            {{ number_format((float) $slot['price'], 0, ',', '.') }}đ
                                                                        </div>
                                                                        <span class="slot-state badge rounded-pill mt-2 bg-success-subtle text-success">Còn trống</span>
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @endif
                                </div>

                                @error('time_slot')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                                @error('booking_time')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Ghi chú
                                </label>
                                <textarea name="note"
                                          rows="3"
                                          class="form-control rounded-3"
                                          placeholder="Nhập ghi chú nếu có">{{ old('note') }}</textarea>
                            </div>

                            <div class="bg-light rounded-4 p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Khung giờ</span>
                                    <strong id="summaryTime">Chưa chọn</strong>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Tổng tiền</span>
                                    <strong class="text-success fs-5" id="summaryPrice">0đ</strong>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success rounded-3 w-100 py-3 fw-bold">
                                <i class="bi bi-calendar-check me-1"></i>
                                Gửi đơn đặt sân
                            </button>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                 style="width: 72px; height: 72px;">
                                <i class="bi bi-lock fs-2"></i>
                            </div>

                            <h5 class="fw-bold">Bạn cần đăng nhập</h5>
                            <p class="text-muted">
                                Vui lòng đăng nhập để gửi đơn đặt sân.
                            </p>

                            <a href="{{ route('login') }}" class="btn btn-primary rounded-3 w-100 py-3">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Đăng nhập để đặt sân
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slotButtons = Array.from(document.querySelectorAll('.js-slot-btn'));
        const selectedTimeSlot = document.getElementById('selectedTimeSlot');
        const selectedPrice = document.getElementById('selectedPrice');
        const summaryTime = document.getElementById('summaryTime');
        const summaryPrice = document.getElementById('summaryPrice');
        const bookingForm = document.getElementById('bookingForm');
        const bookingDate = document.getElementById('bookingDate');
        const fieldSelect = document.getElementById('fieldSelect');
        const availabilityUrl = '{{ route('user.bookings.availability', $stadium->id) }}';

        function formatMoney(value) {
            value = Number(value || 0);
            return value.toLocaleString('vi-VN') + 'đ';
        }

        function resetSelection() {
            slotButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            if (selectedTimeSlot) {
                selectedTimeSlot.value = '';
            }

            if (selectedPrice) {
                selectedPrice.value = '';
            }

            if (summaryTime) {
                summaryTime.innerText = 'Chưa chọn';
            }

            if (summaryPrice) {
                summaryPrice.innerText = '0đ';
            }
        }

        function updateAvailability(response) {
            const slots = response.slots || [];
            const slotMap = new Map(slots.map(function (slot) {
                return [String(slot.id), slot];
            }));

            slotButtons.forEach(function (button) {
                const slotId = String(button.dataset.slotId || '');
                const slot = slotMap.get(slotId);
                const state = button.querySelector('.slot-state');

                if (!slot) {
                    return;
                }

                button.dataset.price = slot.price;
                const priceElement = button.querySelector('.slot-price');
                if (priceElement) {
                    priceElement.innerText = formatMoney(slot.price);
                }

                if (slot.available) {
                    button.disabled = false;
                    button.dataset.available = 'true';
                    button.classList.remove('is-occupied', 'active');
                    button.classList.add('available');
                    if (state) {
                        state.className = 'slot-state badge rounded-pill mt-2 bg-success-subtle text-success';
                        state.innerText = 'Còn trống';
                    }
                } else {
                    button.disabled = true;
                    button.dataset.available = 'false';
                    button.classList.remove('active', 'available');
                    button.classList.add('is-occupied');
                    if (state) {
                        state.className = 'slot-state badge rounded-pill mt-2 bg-danger-subtle text-danger';
                        state.innerText = 'Đã đặt';
                    }
                }

                if (button.classList.contains('active')) {
                    selectedPrice.value = slot.price;
                    summaryPrice.innerText = formatMoney(slot.price);
                }
            });

            if (selectedTimeSlot && selectedTimeSlot.value) {
                const selectedButton = slotButtons.find(function (item) {
                    return item.dataset.time === selectedTimeSlot.value;
                });

                if (selectedButton && selectedButton.disabled) {
                    resetSelection();
                }
            }
        }

        function fetchAvailability() {
            if (!bookingDate || !fieldSelect) {
                return;
            }

            const params = new URLSearchParams({
                field_id: fieldSelect.value,
                booking_date: bookingDate.value
            });

            fetch(availabilityUrl + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                updateAvailability(data);
            })
            .catch(function () {
                slotButtons.forEach(function (button) {
                    button.disabled = false;
                    button.dataset.available = 'true';
                    button.classList.remove('is-occupied');
                    button.querySelector('.slot-state').className = 'slot-state badge rounded-pill mt-2 bg-success-subtle text-success';
                    button.querySelector('.slot-state').innerText = 'Còn trống';
                });
            });
        }

        slotButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (button.disabled) {
                    return;
                }

                slotButtons.forEach(function (item) {
                    item.classList.remove('active');
                });

                button.classList.add('active');

                const time = button.dataset.time;
                const price = button.dataset.price;

                if (selectedTimeSlot) {
                    selectedTimeSlot.value = time;
                }

                if (selectedPrice) {
                    selectedPrice.value = price;
                }

                if (summaryTime) {
                    summaryTime.innerText = time;
                }

                if (summaryPrice) {
                    summaryPrice.innerText = formatMoney(price);
                }
            });
        });

        if (bookingDate) {
            bookingDate.addEventListener('change', fetchAvailability);
        }

        if (fieldSelect) {
            fieldSelect.addEventListener('change', fetchAvailability);
        }

        if (bookingForm) {
            bookingForm.addEventListener('submit', function (event) {
                if (!selectedTimeSlot || !selectedTimeSlot.value) {
                    event.preventDefault();
                    alert('Vui lòng chọn khung giờ đặt sân.');
                    return false;
                }

                const selectedButton = slotButtons.find(function (item) {
                    return item.dataset.time === selectedTimeSlot.value;
                });

                if (selectedButton && selectedButton.disabled) {
                    event.preventDefault();
                    alert('Khung giờ này vừa được người khác đặt. Vui lòng chọn khung giờ khác.');
                    return false;
                }
            });
        }

        fetchAvailability();
    });
</script>
@endpush
