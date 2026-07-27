@extends('admin.layouts.app')

@section('content')
<style>
    .refund-card {
        border: 1px solid #fee2e2;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.08);
        overflow: hidden;
    }
    .refund-header {
        background: linear-gradient(135deg, #fef2f2 0%, #ffe4e6 100%);
        border-bottom: 1px solid #fecdd3;
        padding: 24px 28px;
    }
    .bank-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
    }
    .bank-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .bank-item:last-child {
        border-bottom: none;
    }
    .card-equal-height {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
</style>

<div class="container-fluid py-4">

    {{-- HEADER TRANG --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h3 class="fw-bold text-dark mb-0">Đơn đặt sân #{{ $booking->id }}</h3>
                @if(!empty($booking->booking_code) || !empty($booking->code))
                    <span class="badge bg-secondary-subtle text-secondary px-3 py-1.5 rounded-pill small">
                        {{ $booking->booking_code ?? $booking->code }}
                    </span>
                @endif
            </div>
            <p class="text-muted small mb-0 mt-1">Quản lý thông tin chi tiết và xử lý yêu cầu hủy hoàn tiền</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-white border rounded-3 shadow-sm px-3 fw-medium">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 p-3 d-flex align-items-center gap-3" style="background: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div class="fw-medium">{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- 🔥 1. KHUNG HOÀN TIỀN CÓ PADDING RỘNG RÃI - KHÔNG BỊ DÍNH SÁT KHUNG --}}
    {{-- ========================================================================= --}}
    @if(($booking->status ?? '') === 'cancelled' && isset($booking->refund_amount) && $booking->refund_amount > 0)
        
        @php
            $rawNote = $booking->cancel_note ?? $booking->note ?? '';
            
            preg_match('/Ngân hàng:\s*([^\n]+)/u', $rawNote, $mBank);
            preg_match('/Số STK:\s*([^\n]+)/u', $rawNote, $mStk);
            preg_match('/Chủ STK:\s*([^\n]+)/u', $rawNote, $mHolder);
            preg_match('/Lý do hủy:\s*([^\n]+)/u', $rawNote, $mReason);

            $bankName = trim($mBank[1] ?? 'Chưa xác định');
            $stk = trim($mStk[1] ?? 'Chưa có STK');
            $holder = trim($mHolder[1] ?? 'Chưa có tên');
            $reason = trim($mReason[1] ?? 'Không có lý do');
            $rfStatus = $booking->refund_status ?? 'pending';
        @endphp

        <div class="refund-card mb-4">
            {{-- HEADER KHUNG REDUND (ĐÃ TĂNG PADDING) --}}
            <div class="refund-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px;">
                        <i class="bi bi-arrow-counterclockwise fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-danger mb-0 fs-6">Yêu cầu chuyển khoản hoàn tiền</h5>
                        <small class="text-danger-emphasis">Khách hàng đã gửi thông tin tài khoản nhận tiền hoàn</small>
                    </div>
                </div>
                <div class="text-end pe-2">
                    <span class="small text-muted d-block mb-1">Số tiền Cần Hoàn:</span>
                    <span class="fs-3 fw-bold text-danger">{{ number_format($booking->refund_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>

            {{-- BODY KHUNG REFUND (THÊM THONG THA PADDING 28PX) --}}
            <div class="p-4 p-md-4 px-lg-5 py-lg-4">
                <div class="row g-4 align-items-stretch">
                    
                    {{-- CỘT TRÁI: TÀI KHOẢN NGÂN HÀNG KHÁCH --}}
                    <div class="col-lg-6">
                        <div class="bank-info-box h-100">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2 border-bottom pb-2">
                                <i class="bi bi-bank text-primary fs-5"></i>
                                Tài khoản ngân hàng nhận tiền
                            </h6>

                            <div class="bank-item">
                                <span class="text-muted small">Ngân hàng:</span>
                                <span class="fw-bold text-dark">{{ $bankName }}</span>
                            </div>

                            <div class="bank-item">
                                <span class="text-muted small">Số tài khoản:</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-6 text-primary">{{ $stk }}</span>
                                    <button class="btn btn-sm btn-light border py-0 px-2" onclick="navigator.clipboard.writeText('{{ $stk }}'); alert('Đã sao chép STK!');" title="Copy STK">
                                        <i class="bi bi-copy small"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="bank-item">
                                <span class="text-muted small">Chủ tài khoản:</span>
                                <span class="fw-bold text-dark text-uppercase">{{ $holder }}</span>
                            </div>

                            <div class="bank-item">
                                <span class="text-muted small">Lý do hủy sân:</span>
                                <span class="fw-medium text-secondary text-end style-italic" style="max-width: 65%;">"{{ $reason }}"</span>
                            </div>
                        </div>
                    </div>

                    {{-- CỘT PHẢI: TẢI BILL --}}
                    <div class="col-lg-6">
                        @if($rfStatus === 'pending')
                            <div class="bg-light rounded-4 p-4 border h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">
                                        <i class="bi bi-cloud-arrow-up text-danger me-1"></i> Tải ảnh hóa đơn đã chuyển khoản
                                    </h6>
                                    <p class="small text-muted mb-3">Sau khi chuyển khoản <strong>{{ number_format($booking->refund_amount, 0, ',', '.') }}đ</strong>, tải ảnh Bill chụp màn hình vào đây:</p>
                                </div>

                                <form action="{{ route('admin.bookings.processRefund', $booking->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="file" name="refund_proof_image" class="form-control rounded-3" accept="image/*" required>
                                    </div>

                                    <div class="mb-3">
                                        <input type="text" name="refund_proof_note" class="form-control rounded-3" placeholder="Ghi chú thêm (VD: Đã CK từ MB Bank lúc 15:30)">
                                    </div>

                                    <button type="submit" class="btn btn-danger w-100 rounded-3 fw-bold py-2.5 shadow-sm">
                                        <i class="bi bi-send me-1"></i> Xác Nhận Đã CK & Gửi Bill Cho Khách
                                    </button>
                                </form>
                            </div>

                        @elseif($rfStatus === 'completed')
                            <div class="bg-success-subtle rounded-4 p-4 text-center border border-success-subtle h-100 d-flex flex-column align-items-center justify-content-center">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-2" style="width: 48px; height: 48px;">
                                    <i class="bi bi-check-lg fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-success mb-1">ĐÃ TẢI BILL CHUYỂN KHOẢN</h6>
                                <p class="small text-muted mb-3">Đã gửi bằng chứng hoàn tiền cho khách. Đang chờ khách bấm xác nhận.</p>
                                
                                @if(!empty($booking->refund_proof_image))
                                    <a href="{{ asset($booking->refund_proof_image) }}" target="_blank" class="btn btn-sm btn-white border rounded-3 shadow-sm fw-medium">
                                        <i class="bi bi-image me-1"></i> Xem ảnh Bill đã gửi
                                    </a>
                                @endif
                            </div>

                        @elseif($rfStatus === 'disputed')
                            <div class="bg-danger-subtle rounded-4 p-4 border border-danger h-100">
                                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> KHÁCH BÁO CHƯA NHẬN ĐƯỢC TIỀN</h6>
                                <p class="small text-dark mb-2">Khách phản hồi:</p>
                                <div class="p-3 bg-white rounded-3 border text-danger fw-bold small mb-3">
                                    "{{ $booking->user_dispute_reason }}"
                                </div>
                                <p class="small text-muted mb-0">👉 Vui lòng gọi điện hỗ trợ khách: <strong>{{ $booking->customer_phone ?? $booking->phone }}</strong></p>
                            </div>

                        @elseif($rfStatus === 'confirmed_by_user')
                            <div class="bg-primary-subtle rounded-4 p-4 text-center border border-primary-subtle h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="bi bi-patch-check-fill text-primary fs-1 mb-1"></i>
                                <h6 class="fw-bold text-primary mb-1">HOÀN TẤT GIAO DỊCH</h6>
                                <p class="small text-primary-emphasis mb-0">Khách hàng đã bấm xác nhận nhận đủ tiền.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endif

    {{-- ========================================================================= --}}
    {{-- 📑 2. BỐ CỤC 2 CỘT BẰNG NHAU (CARD-EQUAL-HEIGHT) --}}
    {{-- ========================================================================= --}}
    <div class="row g-4 align-items-stretch">
        {{-- CỘT TRÁI: DANH SÁCH SÂN ĐẶT --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 card-equal-height">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i> Khung giờ & Sân bóng đã đặt</h6>
                </div>
                <div class="card-body p-0 flex-grow-1">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">TÊN SÂN</th>
                                    <th class="py-3">NGÀY ĐÁ</th>
                                    <th class="py-3">KHUNG GIỜ</th>
                                    <th class="text-end pe-4 py-3">GIÁ TIỀN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookingDetails as $detail)
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $detail->field_name ?? 'Sân mặc định' }}</td>
                                        <td>{{ !empty($detail->booking_date) ? \Carbon\Carbon::parse($detail->booking_date)->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-3 py-1.5 fw-semibold">
                                                {{ $detail->slot_start_time ?? '-' }} - {{ $detail->slot_end_time ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-success">
                                            {{ number_format($detail->price ?? $detail->field_price_per_hour ?? 0, 0, ',', '.') }}đ
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có thông tin sân.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: THÔNG TIN KHÁCH HÀNG --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 card-equal-height">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-person-vcard me-2 text-primary"></i> Thông tin khách đặt</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Họ và tên:</small>
                            <span class="fw-bold text-dark fs-6">{{ $booking->user_name ?? $booking->customer_name ?? 'Khách hàng' }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Số điện thoại:</small>
                            <span class="fw-bold text-dark fs-6">{{ $booking->customer_phone ?? $booking->phone ?? 'Chưa có' }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Email:</small>
                            <span class="fw-medium text-dark">{{ $booking->user_email ?? $booking->customer_email ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <hr class="my-3">

                        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Cập nhật trạng thái đơn</label>
                                <select name="status" class="form-select rounded-3 fw-bold">
                                    <option value="pending" @selected($booking->status === 'pending')>Chờ xác nhận</option>
                                    <option value="confirmed" @selected($booking->status === 'confirmed')>Đã xác nhận</option>
                                    <option value="completed" @selected($booking->status === 'completed')>Hoàn thành</option>
                                    <option value="cancelled" @selected($booking->status === 'cancelled')>Đã hủy</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold py-2">
                                Cập Nhật Trạng Thái
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection