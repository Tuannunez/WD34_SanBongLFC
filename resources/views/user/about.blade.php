@extends('layouts.app')

@section('title', 'Giới thiệu LFC')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="card-body">
                        <span class="badge bg-success rounded-pill mb-3">Giới thiệu</span>
                        <h1 class="mb-3">Cơ sở sân bóng LFC</h1>
                        <p class="lead text-muted">
                            SanBongLFC là nền tảng đặt sân bóng trực tuyến dành cho cơ sở sân bóng LFC tại Hoài Đức, Hà Nội.
                            Chúng tôi cung cấp dịch vụ đặt sân 5, sân 7 và sân 11 chuyên nghiệp với hệ thống cỏ nhân tạo chất lượng cao,
                            đèn chiếu sáng đầy đủ và tiện ích đi kèm cho người chơi.
                        </p>

                        <h3 class="mt-4">Tầm nhìn của chúng tôi</h3>
                        <p class="text-muted">
                            LFC hướng tới mục tiêu trở thành địa chỉ đặt sân đáng tin cậy cho các đội bóng, nhóm bạn và gia đình.
                            Chúng tôi chú trọng xây dựng trải nghiệm đặt sân nhanh chóng, minh bạch và an toàn, đồng thời hỗ trợ
                            khách hàng tối đa khi lên kế hoạch thi đấu.
                        </p>

                        <h3 class="mt-4">Tiện ích nổi bật</h3>
                        <ul class="list-unstyled text-muted">
                            <li>• Sân bóng đạt chuẩn với cỏ nhân tạo chất lượng cao</li>
                            <li>• Hệ thống đèn chiếu sáng và phòng thay đồ sạch sẽ</li>
                            <li>• Bãi đậu xe rộng rãi và khu vực nghỉ ngơi cho cầu thủ</li>
                            <li>• Thanh toán linh hoạt với hỗ trợ đặt sân online</li>
                            <li>• Hỗ trợ tư vấn giờ chơi và chọn sân phù hợp nhanh chóng</li>
                        </ul>

                        <h3 class="mt-4">Cam kết của LFC</h3>
                        <p class="text-muted">
                            Với SanBongLFC, bạn luôn được đặt sân bóng nhanh chóng, nhận thông tin booking chi tiết và có đội ngũ
                            chăm sóc khách hàng sẵn sàng giải đáp mọi thắc mắc. Chúng tôi cam kết mang đến trải nghiệm thể thao tốt nhất
                            cho mọi khách hàng.
                        </p>

                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-success rounded-3">
                                Quay lại trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
