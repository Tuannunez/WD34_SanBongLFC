@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tổng sân bóng</p>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-map"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Đơn đặt hôm nay</p>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Doanh thu tháng</p>
                        <h3 class="mb-0">0đ</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Khách hàng</p>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Đơn đặt sân mới nhất</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Sân</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Tổng tiền</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Chưa có dữ liệu đặt sân.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Trạng thái hệ thống</h5>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Cơ sở sân bóng</span>
                        <span class="badge text-bg-primary">0</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Loại sân</span>
                        <span class="badge text-bg-success">0</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Khung giờ</span>
                        <span class="badge text-bg-warning">0</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Dịch vụ</span>
                        <span class="badge text-bg-info">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection