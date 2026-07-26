@extends('layouts.app')

@section('title', 'Trang chủ')

@section('hero-bottom')
    @if($fields->isNotEmpty())
        <section class="hero-schedule-section py-4">
            <div class="container">
                <div class="card shadow-sm rounded-4 border-0">
                    <div class="card-body py-3 px-3 px-lg-4">
                        <div class="hero-schedule-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                            <div class="hero-schedule-controls d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill hero-schedule-prev">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <div class="hero-schedule-range px-3 py-2 rounded-pill bg-white border">
                                    {{ $fields->first()->scheduleDates[0]['dayLabel'] ?? '' }} - {{ $fields->first()->scheduleDates[count($fields->first()->scheduleDates) - 1]['dayLabel'] ?? '' }}
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill hero-schedule-next">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>

                            <div class="hero-schedule-legend d-flex flex-wrap gap-2 align-items-center">
                                <div class="legend-item"><span class="legend-dot legend-available"></span>Trống</div>
                                <div class="legend-item"><span class="legend-dot legend-locked"></span>Đã khóa</div>
                                <div class="legend-item"><span class="legend-dot legend-booked"></span>Đã đặt</div>
                                <div class="legend-item"><span class="legend-dot legend-played"></span>Đã đá</div>
                            </div>
                        </div>

                        <div class="hero-day-tabs d-flex flex-wrap gap-2 mb-3">
                            @foreach($fields->first()->scheduleDates as $dayIndex => $day)
                                <button type="button" class="btn btn-outline-secondary hero-day-tab @if($dayIndex === 0) active @endif"
                                        data-day-index="{{ $dayIndex }}">
                                    <div class="small text-muted mb-1">{{ $day['weekday'] }}</div>
                                    <div class="fw-semibold">{{ $day['dayLabel'] }}</div>
                                </button>
                            @endforeach
                        </div>

                        <div class="hero-schedule-fields">
                            @foreach($fields as $field)
                                <div class="field-schedule-row mb-2 shadow-sm rounded-3 border">
                                    <div class="field-schedule-label d-flex align-items-center gap-2 px-3 py-2">
                                        <span class="field-icon d-inline-flex align-items-center justify-content-center rounded-circle bg-white border text-secondary">
                                            <i class="bi bi-grid-1x2-fill"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold mb-0">{{ $field->name ?: 'Sân bóng' }}</div>
                                            <div class="text-muted small">{{ $field->stadium?->name }}</div>
                                        </div>
                                    </div>
                                    <div class="field-schedule-day-wrapper px-2 py-2">
                                        @foreach($field->scheduleDates as $dayIndex => $day)
                                            <div class="field-schedule-day @if($dayIndex !== 0) d-none @endif" data-day-index="{{ $dayIndex }}">
                                                @foreach($day['slots'] as $slot)
                                                    <div class="schedule-slot {{ $slot['status'] }}" title="{{ $slot['time'] }} - {{ $slot['label'] }}">
                                                        <span class="slot-time">{{ $slot['time'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const tabs = Array.from(document.querySelectorAll('.hero-day-tab'));
                const dayBlocks = Array.from(document.querySelectorAll('.field-schedule-day'));
                const prevButton = document.querySelector('.hero-schedule-prev');
                const nextButton = document.querySelector('.hero-schedule-next');
                const rangeDisplay = document.querySelector('.hero-schedule-range');

                const days = tabs.map(tab => ({
                    index: parseInt(tab.dataset.dayIndex, 10),
                    weekday: tab.querySelector('.small').textContent.trim(),
                    label: tab.querySelector('.fw-semibold').textContent.trim(),
                }));

                function setActiveDay(index) {
                    tabs.forEach(tab => {
                        tab.classList.toggle('active', parseInt(tab.dataset.dayIndex, 10) === index);
                    });
                    dayBlocks.forEach(block => {
                        block.classList.toggle('d-none', parseInt(block.dataset.dayIndex, 10) !== index);
                    });

                    const day = days.find(d => d.index === index);
                    if (day && rangeDisplay) {
                        rangeDisplay.textContent = `${day.weekday} ${day.label}`;
                    }
                }

                function getActiveIndex() {
                    const activeTab = tabs.find(tab => tab.classList.contains('active'));
                    return activeTab ? parseInt(activeTab.dataset.dayIndex, 10) : 0;
                }

                tabs.forEach(tab => {
                    tab.addEventListener('click', function () {
                        const index = parseInt(this.dataset.dayIndex, 10);
                        setActiveDay(index);
                    });
                });

                prevButton?.addEventListener('click', function () {
                    const currentIndex = getActiveIndex();
                    const nextIndex = Math.max(0, currentIndex - 1);
                    setActiveDay(nextIndex);
                });

                nextButton?.addEventListener('click', function () {
                    const currentIndex = getActiveIndex();
                    const nextIndex = Math.min(days.length - 1, currentIndex + 1);
                    setActiveDay(nextIndex);
                });

                setActiveDay(0);
            });
        </script>
    @endif
@endsection

@section('content')
<section class="py-5" id="stadiums">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-1">Lịch đặt sân</h2>
                <p class="text-muted mb-0">Xem nhanh khung giờ trống và trạng thái đặt sân trong tuần.</p>
            </div>
            <a href="{{ route('stadiums.index') }}" class="btn btn-outline-success rounded-3">
                Xem tất cả sân
            </a>
        </div>

        @if(request()->filled('keyword') || request()->filled('city') || request()->filled('field_type') || request()->filled('booking_date'))
            <div class="alert alert-info rounded-4 border-0 shadow-sm">
                <i class="bi bi-search me-1"></i>
                Kết quả tìm kiếm theo bộ lọc hiện tại.
                <a href="{{ route('home') }}" class="alert-link">Xóa bộ lọc</a>
            </div>
        @endif

        <div class="row g-4 mt-2" id="about">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <span class="badge text-bg-dark rounded-pill mb-3">Giới thiệu</span>
                        <h5 class="fw-bold">Cơ sở sân bóng LFC – Chất lượng từ khung giờ đến trải nghiệm</h5>
                        <p class="text-muted mb-3">
                            SanBongLFC là cơ sở sân bóng chuyên nghiệp tại Hoài Đức, Hà Nội, hiện đang quản lý hệ thống sân 5, sân 7 và sân 11 đạt chuẩn. Chúng tôi mang đến sân cỏ nhân tạo chất lượng cao, hệ thống chiếu sáng đầy đủ, phòng thay đồ sạch sẽ và đội ngũ hỗ trợ luôn sẵn sàng.
                        </p>
                        <p class="text-muted mb-0">
                            Với nền tảng đặt sân trực tuyến, bạn có thể tìm sân phù hợp, chọn khung giờ và hoàn tất đơn chỉ trong vài phút. LFC cam kết mang đến trải nghiệm đặt sân nhanh chóng, an toàn và tiện lợi cho cả nhóm bạn và đội bóng của bạn.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-4 h-100">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <span class="badge text-bg-success rounded-pill mb-3">Tiện ích</span>
                                <h5 class="fw-bold">Tiện nghi đầy đủ cho cầu thủ</h5>
                                <p class="text-muted mb-0">Khu vực nghỉ ngơi, nước uống, phòng thay đồ an toàn và bãi đỗ xe rộng rãi giúp bạn tập trung vào trận đấu và thư giãn sau khi đá xong.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body">
                                <span class="badge text-bg-primary rounded-pill mb-3">Đặt sân</span>
                                <h5 class="fw-bold">Đặt sân nhanh chóng & linh hoạt</h5>
                                <p class="text-muted mb-0">Chọn sân, khung giờ và dịch vụ kèm theo dễ dàng trên SanBongLFC. Thanh toán online, xác nhận tức thì và thông tin đơn được cập nhật đầy đủ.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-3">
            <div class="col-lg-4" id="news">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body">
                        <span class="badge text-bg-success rounded-pill mb-3">Tin tức</span>
                        <h5 class="fw-bold">Lịch đặt sân tối ưu cho cuối tuần</h5>
                        <p class="text-muted mb-0">Khám phá khung giờ đẹp, sân mới và ưu đãi đặt trước để tối ưu trải nghiệm thể thao của bạn.</p>
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
    </div>
</section>
@endsection
