@extends('layouts.app')

@section('title', 'Trang chủ')

@section('hero-bottom')
    @if($fields->isNotEmpty())
        <section class="hero-stadiums-section py-4">
            <div class="container">
                <div class="row align-items-center mb-4">
                    <div class="col-md-8">
                        <div class="text-start">
                            <span class="badge bg-success rounded-pill mb-2">Sân nổi bật</span>
                            <h2 class="section-title mb-1">Danh sách sân bóng</h2>
                            <p class="text-muted mb-0">Chọn sân theo loại và vị trí, xem giá tham khảo và đặt sân ngay.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('home') }}" class="d-flex gap-2 align-items-center justify-content-md-end">
                            <div class="flex-grow-1">
                                <select name="field_type" class="form-select form-select-sm">
                                    <option value="">Tất cả loại sân</option>
                                    @foreach($fieldTypes as $fieldType)
                                        <option value="{{ $fieldType->id }}" @if(request()->input('field_type') == $fieldType->id) selected @endif>{{ $fieldType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Lọc</button>
                        </form>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach($fields as $field)
                        @php
                            $stadium = $field->stadium;
                            $image = $field->images->first()?->image_path
                                ?? data_get($stadium, 'image')
                                ?? data_get($stadium, 'thumbnail')
                                ?? data_get($stadium, 'image_url');
                            $imageUrl = $image ? asset('storage/' . $image) : asset('images/banner1.png');
                            $fieldName = $field->name ?: 'Sân bóng';
                            $address = data_get($stadium, 'address', 'Đang cập nhật địa chỉ');
                            $price = $field->display_price;
                        @endphp

                        <div class="col-lg-4 col-md-6">
                            <div class="card stadium-card h-100 shadow-sm rounded-4 overflow-hidden">
                                <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $fieldName }}" style="height: 220px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="fw-bold mb-2">{{ $fieldName }}</h5>
                                    <p class="text-muted small mb-2">{{ $field->fieldType?->name ?? 'Loại sân không xác định' }}</p>
                                    <p class="text-muted mb-3"><i class="bi bi-geo-alt-fill me-1"></i> {{ $address }}</p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <div class="h5 mb-0 text-success">{{ number_format((float) $price, 0, ',', '.') }}đ</div>
                                                <small class="text-muted">Giá tham khảo</small>
                                            </div>
                                            <a href="{{ route('stadiums.show', ['id' => $stadium->id, 'field' => $field->id]) }}" class="btn btn-success rounded-3">Đặt sân</a>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $field->fieldType?->number_of_players ? $field->fieldType->number_of_players . ' người' : 'Số người chưa rõ' }}</span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $stadium->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@section('content')
<section class="py-5" id="stadiums">
    <div class="container">
        @if(request()->filled('keyword') || request()->filled('city') || request()->filled('field_type') || request()->filled('booking_date'))
            <div class="alert alert-info rounded-4 border-0 shadow-sm">
                <i class="bi bi-search me-1"></i>
                Kết quả tìm kiếm theo bộ lọc hiện tại.
                <a href="{{ route('home') }}" class="alert-link">Xóa bộ lọc</a>
            </div>
        @endif

        <section class="py-5" id="about">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden d-flex align-items-center justify-content-center p-4 bg-white">
                            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="SanBongLFC Logo" style="max-height:220px; object-fit:contain;" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <span class="badge text-bg-dark rounded-pill mb-3">Giới thiệu</span>
                        <h2 class="section-title mb-3">SanBongLFC - Đặt sân nhanh, chơi nhiệt</h2>
                        <p class="text-muted mb-4">SanBongLFC giúp bạn tìm sân bóng phù hợp và đặt lịch chỉ trong vài bước. Hệ thống sân 5, sân 7 và sân 11 chất lượng cao được cập nhật liên tục với giá rõ ràng và thông tin đầy đủ.</p>
                        <ul class="list-unstyled feature-list mb-0">
                            <li><i class="bi bi-check-circle-fill text-success"></i>Hệ thống sân chuẩn, có sẵn ảnh và chi tiết giờ.</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i>Thanh toán nhanh, xác nhận tức thì qua SMS/Zalo.</li>
                            <li><i class="bi bi-check-circle-fill text-success"></i>Hỗ trợ khách hàng 24/7 và đổi lịch linh hoạt.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5" id="amenities">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="badge text-bg-success rounded-pill mb-3">Tiện ích</span>
                    <h2 class="section-title">Tiện ích nổi bật tại LFC</h2>
                    <p class="text-muted">Tất cả tiện nghi cần thiết để bạn có buổi đá bóng thoải mái và an toàn.</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <div class="feature-icon rounded-circle bg-success bg-opacity-10 text-success mb-3">
                                    <i class="bi bi-droplet-half fs-3"></i>
                                </div>
                                <h5 class="fw-bold">Nước uống & phục vụ</h5>
                                <p class="text-muted mb-0">Nước suối, nước ngọt và dịch vụ order nhanh tại sân.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <div class="feature-icon rounded-circle bg-success bg-opacity-10 text-success mb-3">
                                    <i class="bi bi-people-fill fs-3"></i>
                                </div>
                                <h5 class="fw-bold">Phòng thay đồ</h5>
                                <p class="text-muted mb-0">Phòng thay đồ sạch sẽ, khóa an toàn và tiện nghi cho đội nhóm.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <div class="feature-icon rounded-circle bg-success bg-opacity-10 text-success mb-3">
                                    <i class="bi bi-car-front-fill fs-3"></i>
                                </div>
                                <h5 class="fw-bold">Bãi đỗ xe rộng</h5>
                                <p class="text-muted mb-0">Bãi đỗ xe rộng rãi, an ninh 24/7 và vị trí thuận tiện.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <div class="feature-icon rounded-circle bg-success bg-opacity-10 text-success mb-3">
                                    <i class="bi bi-lightning-charge-fill fs-3"></i>
                                </div>
                                <h5 class="fw-bold">Chiếu sáng chất lượng</h5>
                                <p class="text-muted mb-0">Đèn sáng đều, phù hợp thi đấu buổi tối và tối thiểu bóng chói.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5" id="booking-process">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="badge text-bg-primary rounded-pill mb-3">Đặt sân</span>
                    <h2 class="section-title">Cách đặt sân trong 3 bước</h2>
                    <p class="text-muted">Quy trình rõ ràng giúp bạn đặt sân nhanh chóng và dễ dàng.</p>
                </div>

                <div class="process-cards">
                    <div class="process-card">
                        <div class="process-step">1</div>
                        <h5 class="fw-bold">Chọn sân & ngày</h5>
                        <p class="text-muted mb-0">Duyệt danh sách sân theo loại và vị trí, chọn ngày phù hợp với kế hoạch của bạn.</p>
                    </div>
                    <div class="process-card">
                        <div class="process-step">2</div>
                        <h5 class="fw-bold">Chọn khung giờ</h5>
                        <p class="text-muted mb-0">Xem lịch trống ngay, chọn khung giờ còn trống và kiểm tra giá chi tiết.</p>
                    </div>
                    <div class="process-card">
                        <div class="process-step">3</div>
                        <h5 class="fw-bold">Xác nhận & thanh toán</h5>
                        <p class="text-muted mb-0">Hoàn tất đơn ngay trên web. Nhận xác nhận và thông tin liên hệ để đến sân đúng giờ.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5" id="news-contact">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6" id="news">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <span class="badge text-bg-success rounded-pill mb-3">Tin tức</span>
                                @if(!empty($news) && $news->isNotEmpty())
                                    <div class="row g-3">
                                        @foreach($news->take(2) as $item)
                                            <div class="col-12">
                                                <article class="card news-card small-card border-0 shadow-sm overflow-hidden">
                                                    @if($item->image)
                                                        <img src="{{ $item->image }}" alt="{{ $item->title }}" class="w-100">
                                                    @endif
                                                    <div class="card-body p-3">
                                                        <p class="news-card-meta mb-1">{{ $item->published_at?->format('d/m/Y') }}</p>
                                                        <h5 class="news-card-title mb-2">{{ $item->title }}</h5>
                                                        <p class="news-card-text mb-2">{{ \Illuminate\Support\Str::limit($item->excerpt ?: $item->content, 100) }}</p>
                                                        <a href="{{ route('news.show', $item->id) }}" class="text-success fw-semibold">Xem thêm →</a>
                                                    </div>
                                                </article>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('news.index') }}" class="text-success fw-semibold">Xem tất cả tin tức →</a>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">Hiện chưa có tin tức nào. Vui lòng quay lại sau.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6" id="contact">
                        <div class="card contact-card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <span class="badge text-bg-warning rounded-pill mb-3">Liên hệ</span>
                                <h5 class="fw-bold">Hỗ trợ khách hàng</h5>
                                <p class="text-muted mb-4">Bạn cần tư vấn giờ mở, sân phù hợp hoặc phương thức thanh toán? Liên hệ với đội ngũ SanBongLFC để được hỗ trợ tận tình.</p>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <span class="contact-icon bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center"><i class="bi bi-telephone-fill"></i></span>
                                            <div>
                                                <div class="fw-semibold">Hotline</div>
                                                <div class="text-muted">1900 1234</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <span class="contact-icon bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center"><i class="bi bi-envelope-fill"></i></span>
                                            <div>
                                                <div class="fw-semibold">Email</div>
                                                <div class="text-muted">support@sanbonglfc.vn</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <span class="contact-icon bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center"><i class="bi bi-geo-alt-fill"></i></span>
                                            <div>
                                                <div class="fw-semibold">Địa chỉ</div>
                                                <div class="text-muted">Hoài Đức, Hà Nội</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-start gap-3">
                                            <span class="contact-icon bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center"><i class="bi bi-clock-fill"></i></span>
                                            <div>
                                                <div class="fw-semibold">Giờ làm việc</div>
                                                <div class="text-muted">07:00 - 22:00 mọi ngày</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if(method_exists($fields, 'links'))
            <div class="mt-4">
                {{ $fields->links() }}
            </div>
        @endif
        <div class="container-fluid bg-light py-4 mt-4" id="services-full">
            <div class="container">
                <div class="mb-3">
                    <span class="badge text-bg-primary rounded-pill mb-1">Dịch vụ</span>
                    <h4 class="fw-bold mb-0">Dịch vụ hỗ trợ đặt sân trọn gói</h4>
                    <p class="text-muted mb-0">Các dịch vụ kèm theo, giá cả và đơn vị tính.</p>
                </div>

                @if(!empty($services) && $services->isNotEmpty())
                    <div class="row g-3">
                        @foreach($services as $service)
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-white rounded-3 h-100 border">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ $service->name }}</div>
                                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($service->description ?? '-', 80) }}</div>
                                        </div>
                                        <div class="text-end ms-2">
                                            <div class="text-success fw-bold">{{ number_format((float) $service->price, 0, ',', '.') }}đ</div>
                                            <small class="text-muted">/{{ $service->unit ?? 'lượt' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light">Chưa có dịch vụ nào.</div>
                @endif
            </div>
        </div>

        <section class="py-5" id="review-homepage">
            <div class="container">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <span class="badge text-bg-warning rounded-pill mb-3">Đánh giá</span>
                        <h2 class="section-title mb-3">Đánh giá từ khách hàng</h2>
                        <p class="text-muted mb-4">Những phản hồi gần đây giúp bạn lựa chọn sân tốt hơn.</p>

                        @if(!empty($reviews) && $reviews->isNotEmpty())
                            <div class="row g-3">
                                @foreach($reviews as $review)
                                    <div class="col-md-4">
                                        <div class="card h-100 border-0 shadow-sm rounded-4">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <strong>{{ $review->user?->name ?? 'Khách' }}</strong>
                                                        <div class="text-muted small">{{ $review->field?->name ?? 'Sân' }}@if($review->field?->stadium), {{ $review->field->stadium->name }}@endif</div>
                                                    </div>
                                                    <span class="badge bg-warning-subtle text-warning">{{ $review->rating }} sao</span>
                                                </div>

                                                <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($review->comment ?? 'Không có nhận xét.', 120) }}</p>
                                                <div class="text-muted small">{{ $review->created_at?->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light rounded-4">Hiện chưa có đánh giá nào. Hãy quay lại sau để xem thêm phản hồi.</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>
@endsection
