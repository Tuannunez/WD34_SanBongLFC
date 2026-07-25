@extends('layouts.app')

@section('title', 'Sân bóng')

@section('content')
<section class="py-5" id="stadiums">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-1">Sân bóng</h2>
                <p class="text-muted mb-0">Danh sách các sân bóng hiện có trong hệ thống</p>
            </div>
        </div>

        <div class="row g-4">
            @forelse($fields as $field)
                @php
                    $stadium = $field->stadium;
                    $stadiumId = data_get($stadium, 'id');
                    $fieldName = $field->name ?: 'Sân bóng';
                    $address = data_get($stadium, 'address', 'Đang cập nhật địa chỉ');
                    $phone = data_get($stadium, 'phone', 'Đang cập nhật');
                    $openTime = data_get($stadium, 'open_time', '07:00');
                    $closeTime = data_get($stadium, 'close_time', '22:00');

                    $image = $field->images->first()?->image_path
                        ?? data_get($stadium, 'image')
                        ?? data_get($stadium, 'thumbnail')
                        ?? data_get($stadium, 'image_url');

                    $imageUrl = $image
                        ? asset('storage/' . $image)
                        : asset('images/banner1.png');

                    $price = $field->display_price;
                @endphp

                <div class="col-lg-4 col-md-6">
                    <div class="card stadium-card">
                        <img src="{{ $imageUrl }}"
                             class="card-img-top stadium-img"
                             alt="{{ $fieldName }}">

                        <div class="card-body">
                            <h5 class="fw-bold mb-2">{{ $fieldName }}</h5>

                            <p class="text-muted mb-2">
                                <i class="bi bi-geo-alt text-danger me-1"></i>
                                {{ $address }}
                            </p>

                            <p class="text-muted mb-2">
                                <i class="bi bi-clock text-primary me-1"></i>
                                {{ $openTime }} - {{ $closeTime }}
                            </p>

                            <p class="text-muted mb-3">
                                <i class="bi bi-telephone text-success me-1"></i>
                                {{ $phone }}
                            </p>


                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="price-text">
                                        {{ number_format((float) $price, 0, ',', '.') }}đ
                                    </div>
                                    <small class="text-muted">Giá tham khảo</small>
                                </div>

                                <div>
                                    <a href="{{ route('stadiums.show', ['id' => $stadiumId, 'field' => $field->id]) }}"
                                       class="btn btn-success rounded-3">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        Đặt sân ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                            <h5 class="fw-bold">Chưa có sân bóng nào</h5>
                            <p class="text-muted mb-0">Hiện chưa có sân phù hợp với bộ lọc của bạn.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
