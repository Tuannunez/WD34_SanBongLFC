@extends('admin.layouts.app')

@section('title', 'Quản lý giá theo khung giờ')
@section('page-title', 'Quản lý giá khung giờ')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold">Quản lý giá: {{ $stadium->name }}</h4>
                    <p class="text-muted mb-0">Thiết lập giá theo khung giờ cho cơ sở này.</p>
                </div>

                <a href="{{ url('admin/stadiums') }}" class="btn btn-secondary">Quay lại</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="mb-3">Thêm Khung Giờ Mới</h5>
                            <form action="{{ url('admin/stadiums/' . $stadium->id . '/prices/custom') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Giờ Bắt Đầu</label>
                                    <input type="time" name="start_time" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Giờ Kết Thúc</label>
                                    <input type="time" name="end_time" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Giá Thuê Mới (VNĐ/trận)</label>
                                    <input type="text" name="price" class="form-control">
                                </div>

                                <button class="btn btn-success w-100">Thêm Cấu Hình Điểm Bán</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Khung giờ đặc biệt</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Khung Giờ</th>
                                            <th>Giá Áp Dụng (VNĐ/trận)</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if(isset($customSlots) && $customSlots->count())
                                            @foreach($customSlots as $c)
                                                <tr>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($c->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($c->end_time)->format('H:i') }}</td>
                                                    <td class="text-center">{{ number_format($c->price, 0, ',', '.') }}</td>
                                                    <td class="text-center">
                                                        <form action="{{ url('admin/stadiums/' . $stadium->id . '/prices/custom/' . $c->id) }}" method="POST" onsubmit="return confirm('Xóa khung giờ đặc biệt này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger">Xóa</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Chưa có khung giờ đặc biệt nào. Sân đang áp dụng Giá gốc cho toàn bộ thời gian.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
