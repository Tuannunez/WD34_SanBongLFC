@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<section class="py-5" id="stadiums">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-1">Sân nổi bật</h2>
                <p class="text-muted mb-0">Danh sách các sân bóng đang có trên hệ thống</p>
            </div>

            <a href="{{ route('home') }}" class="btn btn-outline-success rounded-3">
                Xem tất cả
            </a>
        </div>

        @if(request()->filled('keyword') || request()->filled('city') || request()->filled('field_type') || request()->filled('booking_date'))
            <div class="alert alert-info rounded-4 border-0 shadow-sm">
                <i class="bi bi-search me-1"></i>
                Kết quả tìm kiếm theo bộ lọc hiện tại.
                <a href="{{ route('home') }}" class="alert-link">Xóa bộ lọc</a>
            </div>
        @endif

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
                            <h5 class="fw-bold mb-2">
                                {{ $fieldName }}
                            </h5>

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
                                    @auth
                                        <a href="{{ route('stadiums.show', ['id' => $stadiumId, 'field' => $field->id]) }}"
                                           class="btn btn-success rounded-3">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Đặt
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}"
                                           class="btn btn-success rounded-3">
                                            Đặt
                                        </a>
                                    @endauth
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
                            <p class="text-muted mb-0">
                                Hiện chưa có sân phù hợp với bộ lọc của bạn.
                            </p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="row g-4 mt-2" id="about">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <span class="badge text-bg-dark rounded-pill mb-3">Giới thiệu</span>
                        <h5 class="fw-bold">Nền tảng đặt sân bóng hiện đại</h5>
                        <p class="text-muted mb-0">SanBongLFC giúp bạn dễ dàng tìm kiếm sân bóng, đặt chỗ nhanh và quản lý lịch đá một cách thuận tiện.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" id="news">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <span class="badge text-bg-success rounded-pill mb-3">Tin tức</span>
                        <h5 class="fw-bold">Lịch đặt sân tối ưu cho cuối tuần</h5>
                        <p class="text-muted mb-0">Khám phá khung giờ đẹp, sân mới và ưu đãi đặt trước để tối ưu trải nghiệm thể thao của bạn.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" id="services">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <span class="badge text-bg-primary rounded-pill mb-3">Dịch vụ</span>
                        <h5 class="fw-bold">Dịch vụ hỗ trợ đặt sân trọn gói</h5>
                        <p class="text-muted mb-0">Gồm đặt sân trực tuyến, quản lý đơn, thanh toán linh hoạt và đội ngũ hỗ trợ nhanh chóng mọi lúc.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4" id="contact">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <span class="badge text-bg-warning rounded-pill mb-3">Liên hệ</span>
                        <h5 class="fw-bold">Đội ngũ hỗ trợ 24/7</h5>
                        <p class="text-muted mb-0">Bạn cần tư vấn giờ mở, sân phù hợp hay thanh toán? Hãy liên hệ với SanBongLFC để được hỗ trợ nhanh chóng.</p>
                    </div>
                </div>
            </div>
        </div>

        @if(method_exists($fields, 'links'))
            <div class="mt-4">
                {{ $fields->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
