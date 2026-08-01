@extends('admin.layouts.app')

@section('title','Dashboard')
@section('page-title','Dashboard')

@push('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
body {

    background:

        linear-gradient(180deg,

            #f8fafc 0%,

            #f3f6fb 100%);

    font-family: Inter, sans-serif;

}

.chart-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-tab {

    padding: 9px 18px;

    border-radius: 10px;

    background: #f5f7fb;

    cursor: pointer;

    transition: .25s;

    font-weight: 600;

}

.chart-tab:hover,
.chart-tab.active {

    background: #206bc4;

    color: #fff;

    transform: translateY(-2px);

}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.dashboard-title {
    font-size: 30px;
    font-weight: 800;
}

.dashboard-subtitle {
    color: #6c757d;
    margin-top: 5px;
}

.dashboard-btn {
    border-radius: 12px;
    padding: 10px 18px;
    font-weight: 600;
}

.stat-card {

    border: 0;

    border-radius: 18px;

    box-shadow: 0 8px 24px rgba(0, 0, 0, .06);

    transition: .3s;

    overflow: hidden;

    position: relative;

}

.stat-card:hover {

    transform: translateY(-6px);

}

.stat-card .card-body {

    padding: 22px;

}

.stat-title {

    font-size: 14px;

    color: #6c757d;

}

.stat-value {

    font-size: 34px;

    font-weight: 800;

}

.stat-growth {

    color: #2fb344;

    font-size: 13px;

    font-weight: 700;

}

.stat-icon {

    width: 56px;

    height: 56px;

    border-radius: 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #fff;

    font-size: 24px;

}

.bg-blue {

    background: #206bc4;

}

.bg-green {

    background: #2fb344;

}

.bg-orange {

    background: #f59f00;

}

.bg-red {

    background: #d63939;

}

.sparkline {

    height: 50px;

    margin-top: 15px;

}

.dashboard-card {

    border: 0;

    border-radius: 18px;

    box-shadow: 0 8px 24px rgba(0, 0, 0, .06);

}

.dashboard-card .card-header {

    padding: 22px 24px;

    background: white;

    font-size: 17px;

    font-weight: 700;

    border-bottom: 1px solid #eef2f7;

    display: flex;

    justify-content: space-between;

    align-items: center;

}

.dashboard-card .card-body {

    padding: 25px;

}

.table td {

    vertical-align: middle;

}

.badge-status {

    padding: 7px 12px;

    border-radius: 30px;

}

.status-success {

    background: #d3f9d8;

    color: #2b8a3e;

}

.status-warning {

    background: #fff3bf;

    color: #e67700;

}

.status-danger {

    background: #ffe3e3;

    color: #c92a2a;

}

.progress {

    height: 8px;

    border-radius: 20px;

}

.system-item {

    margin-bottom: 22px;

}

.system-item:last-child {

    margin-bottom: 0;

}

.system-number {

    font-weight: 700;

    font-size: 18px;

}

.dark-mode {

    background: #111827;

    color: white;

}

.dark-mode .card {

    background: #1e293b;

    color: white;

}

.dark-mode .dashboard-card .card-header {

    background: #1e293b;

}

.dark-mode table {

    color: white;

}

.dark-mode .table-light {

    background: #243041;

}

.dark-mode .btn-light {

    background: #334155;

    color: white;

    border: 0;

    .card {

        transition: .35s;

    }

    .card:hover {

        transform: translateY(-6px);

        box-shadow: 0 18px 35px rgba(0, 0, 0, .08);

    }

    .btn {

        transition: .25s;

    }

    .btn:hover {

        transform: translateY(-2px);

    }

    .progress-bar {

        transition: width 1.5s ease;

    }

    .table tbody tr {

        transition: .25s;

    }

    .table tbody tr:hover {

        background: #f8fbff;

        transform: scale(1.01);

    }

}

.avatar-circle {

    width: 44px;

    height: 44px;

    border-radius: 50%;

    background: #206bc4;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-weight: 700;

}

.skeleton {

    height: 20px;

    background: linear-gradient(90deg,

            #ececec,

            #f8f8f8,

            #ececec);

    background-size: 300%;

    animation: skeleton 1.5s infinite;

    border-radius: 10px;

}

@keyframes skeleton {

    0% {

        background-position: 100%;

    }

    100% {

        background-position: -100%;

    }

}

.floating-btn {

    position: fixed;

    right: 35px;

    bottom: 35px;

    width: 60px;

    height: 60px;

    border-radius: 50%;

    background: #206bc4;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 24px;

    box-shadow: 0 12px 30px rgba(0, 0, 0, .2);

    z-index: 999;

    transition: .3s;

}

.floating-btn:hover {

    transform: scale(1.08);

    color: white;

}

.stat-icon {

    transition: .35s;

}

.stat-card:hover .stat-icon {

    transform: rotate(-12deg) scale(1.1);

}

.stat-card::before {

    content: "";

    position: absolute;

    top: -80px;

    right: -80px;

    width: 180px;

    height: 180px;

    background:

        rgba(255, 255, 255, .12);

    border-radius: 50%;

}

.table-hover tbody tr {

    transition: .3s;

}

.table-hover tbody tr:hover {

    background: #f7fbff;

    transform: scale(1.01);

    box-shadow:

        0 10px 20px rgba(0, 0, 0, .04);

}

::-webkit-scrollbar {

    width: 8px;

}

::-webkit-scrollbar-thumb {

    background: #c8d2e1;

    border-radius: 20px;

}

::-webkit-scrollbar-thumb:hover {

    background: #9aa8bb;

}

.dashboard-card {

    overflow: hidden;

}

.dashboard-card::after {

    content: "";

    position: absolute;

    top: 0;

    left: 0;

    height: 3px;

    width: 100%;

    background: #206bc4;

}

.dashboard-card {

    position: relative;

}

.dashboard-card:hover {

    transform: translateY(-5px);

    box-shadow:

        0 25px 40px rgba(0, 0, 0, .08);

}

.loading {

    opacity: .6;

    pointer-events: none;

}

.loading::before {

    content: "";

    position: absolute;

    inset: 0;

    background:

        rgba(255, 255, 255, .6);

}

.badge {

    padding: 8px 14px;

    border-radius: 30px;

    font-weight: 600;

    font-size: 12px;

}

@keyframes fadeCard {

    from {

        opacity: 0;

        transform: translateY(20px);

    }

    to {

        opacity: 1;

        transform: none;

    }

}

.dashboard-card {

    animation: fadeCard .5s ease;


}

.card dashboard-card {
    margin-bottom: 28px;
}

.dashboard-card {
    margin-bottom: 30px;
}

.vip-badge {
    background: linear-gradient(135deg, #FFD54F, #F9A825);
    color: #6d4c00;
    font-size: 13px;
    font-weight: 700;
    padding: 7px 7px;
    border-radius: 99px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    box-shadow: 0 4px 12px rgba(255, 193, 7, .35);
    border: 1px solid rgba(255, 255, 255, .5);
    transition: .3s;
}

.vip-badge i {
    color: #ff9800;
    font-size: 14px;
}

.vip-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(255, 193, 7, .45);
}
</style>

@endpush

@section('content')

<div class="dashboard-header">

    <div>

        <button class="btn btn-light" id="themeToggle">

            <i class="bi bi-moon"></i>

        </button>

        <button class="btn btn-light dashboard-btn">

            Refresh

        </button>

    </div>

</div>


<div class="row g-4">

    <div class="col-lg-3">

        <div class="card stat-card">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="stat-title">

                            Tổng sân

                        </div>

                        <div class="stat-value">

                            {{ $totalFields }}

                        </div>

                        <div class="stat-growth">

                            <i class="bi bi-arrow-up"></i>

                            12%

                        </div>
                        <div id="sparkField"></div>

                    </div>

                    <div class="stat-icon bg-blue">

                        <i class="bi bi-grid"></i>

                    </div>

                </div>

                <div id="spark1" class="sparkline"></div>

            </div>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="card stat-card">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="stat-title">

                            Đơn hôm nay

                        </div>

                        <div class="stat-value">

                            {{ $todayBookings }}

                        </div>

                        <div class="stat-growth {{ $growth['revenue'] >= 0 ? 'text-success' : 'text-danger' }}">

                            <i class="bi bi-arrow-{{ $growth['revenue'] >= 0 ? 'up' : 'down' }}"></i>

                            {{ abs($growth['revenue']) }}%

                        </div>
                        <div id="sparkBooking"></div>

                    </div>

                    <div class="stat-icon bg-green">

                        <i class="bi bi-calendar-check"></i>

                    </div>

                </div>

                <div id="spark2" class="sparkline"></div>

            </div>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="card stat-card">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="stat-title">

                            Doanh thu

                        </div>

                        <div class="stat-value">

                            {{ number_format($monthlyRevenue,0,',','.') }} đ

                        </div>

                        <div class="stat-growth">

                            <i class="bi bi-arrow-up"></i>

                            @if($growth['revenue']>=0)

                            <i class="bi bi-arrow-up"></i>

                            {{ $growth['revenue'] }}%

                            @else

                            <i class="bi bi-arrow-down"></i>

                            {{ abs($growth['revenue']) }}%

                            @endif

                        </div>

                    </div>

                    <div class="stat-icon bg-orange">

                        <i class="bi bi-cash-stack"></i>

                    </div>

                </div>

                <div id="spark3" class="sparkline"></div>

            </div>

        </div>

    </div>

    <div class="col-lg-3">

        <div class="card stat-card">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="stat-title">

                            Khách hàng

                        </div>

                        <div class="stat-value">

                            {{ $totalCustomers }}

                        </div>

                        <div class="stat-growth">

                            <i class="bi bi-arrow-up"></i>

                            9%

                        </div>
                        <div id="sparkCustomer"></div>

                    </div>

                    <div class="stat-icon bg-red">

                        <i class="bi bi-people"></i>

                    </div>

                </div>

                <div id="spark4" class="sparkline"></div>

            </div>

        </div>

    </div>

</div>
<div class="row mt-4">

    {{-- Revenue Chart --}}
    <div class="col-lg-8">

        <div class="card dashboard-card h-100">

            <div class="card-header d-flex justify-content-between align-items-center">

                <div>

                    <div class="fw-bold fs-5">
                        Phân tích doanh thu
                    </div>

                    <small class="text-muted">
                        Thống kê doanh thu năm {{ now()->year }}
                    </small>

                </div>

                <div class="chart-tabs">

                    <div class="chart-tab active" data-type="30">
                        30 ngày
                    </div>

                    <div class="chart-tab" data-type="7">
                        7 ngày
                    </div>

                    <div class="chart-tab" data-quarter="1">
                        Quý 1
                    </div>

                    <div class="chart-tab" data-quarter="2">
                        Quý 2
                    </div>

                    <div class="chart-tab" data-quarter="3">
                        Quý 3
                    </div>

                </div>



            </div>

            <div class="card-body">

                <div id="revenueChart" style="height:360px"></div>

            </div>

        </div>

    </div>


    {{-- Booking Status --}}
    <div class="col-lg-4">

        <div class="card dashboard-card h-100">

            <div class="card-header">

                Booking Overview

            </div>

            <div class="card-body">

                <div id="bookingChart" style="height:300px"></div>

                <hr>

                @php
                $totalBooking = array_sum($bookingStatusChart);

                $confirmed = $totalBooking
                ? round(($bookingStatusChart[0] / $totalBooking) * 100)
                : 0;

                $pending = $totalBooking
                ? round(($bookingStatusChart[1] / $totalBooking) * 100)
                : 0;

                $cancelled = $totalBooking
                ? round(($bookingStatusChart[2] / $totalBooking) * 100)
                : 0;
                @endphp

                <div class="d-flex justify-content-between">
                    <span>Hoàn thành</span>
                    <strong class="text-success">{{ $confirmed }}%</strong>
                </div>

                <div class="progress mt-2">
                    <div class="progress-bar bg-success" style="width: {{ $confirmed }}%">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <span>Đang xử lý</span>
                    <strong class="text-warning">{{ $pending }}%</strong>
                </div>

                <div class="progress mt-2">
                    <div class="progress-bar bg-warning" style="width: {{ $pending }}%">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <span>Đã huỷ</span>
                    <strong class="text-danger">{{ $cancelled }}%</strong>
                </div>

                <div class="progress mt-2">
                    <div class="progress-bar bg-danger" style="width: {{ $cancelled }}%">
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>
<div class="row mt-5">

    <div class="col-12">

        <div class="card dashboard-card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <div>

                    <div class="fw-bold fs-5">
                        Latest Bookings
                    </div>

                    <small class="text-muted">
                        8 booking gần đây
                    </small>

                </div>

                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                    Xem tất cả
                </a>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>#</th>

                                <th>Khách hàng</th>

                                <th>Sân</th>

                                <th>Ngày</th>

                                <th>Trạng thái</th>

                                <th class="text-end">Tiền</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($latestBookings as $booking)

                            <tr>

                                <td>

                                    <strong>#{{ $booking->id }}</strong>

                                </td>

                                <td>

                                    <div class="fw-bold">

                                        <div class="d-flex align-items-center">

                                            <div class="avatar-circle me-3">

                                                {{ strtoupper(substr($booking->user_name ?? 'K',0,1)) }}

                                            </div>

                                            <div>

                                                <div class="fw-bold">

                                                    {{ $booking->user_name }}

                                                </div>

                                                <small class="text-muted">

                                                    {{ $booking->user_email }}

                                                </small>

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    {{ $booking->field_name }}

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}

                                </td>

                                <td>

                                    @php
                                    $status = strtolower($booking->status ?? '');
                                    @endphp

                                    @if(in_array($status,['confirmed','paid','success']))

                                    <span class="badge bg-success">
                                        Hoàn thành
                                    </span>

                                    @elseif(in_array($status,['pending','waiting']))

                                    <span class="badge bg-warning text-dark">
                                        Chờ xác nhận
                                    </span>

                                    @elseif(in_array($status,['cancelled','cancel']))

                                    <span class="badge bg-danger">
                                        Đã huỷ
                                    </span>

                                    @else

                                    <span class="badge bg-secondary">
                                        {{ $booking->status }}
                                    </span>

                                    @endif

                                </td>

                                <td class="text-end fw-bold">

                                    {{ number_format($booking->display_total,0,',','.') }}đ

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="6" class="text-center p-5">

                                    Không có dữ liệu

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    {{-- Top Fields --}}
    <div class="col-lg-6">

        <div class="card dashboard-card">

            <div class="card-header d-flex justify-content-between">

                <span>Top sân được đặt nhiều</span>

                <span class="text-primary fw-bold">Top 5</span>

            </div>

            <div class="card-body">


                @foreach($topFieldsChart as $field)

                <div class="mb-4">

                    <div class="d-flex justify-content-between">

                        <strong>

                            {{ $field->name }}

                        </strong>

                        <span>

                            {{ $field->total }}

                        </span>

                    </div>

                    <div class="progress mt-2">

                        <div class="progress-bar bg-primary" style="width:{{ min($field->total * 10, 100) }}%">
                        </div>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

    </div>

    {{-- Revenue Goal --}}

    <div class="col-lg-6">

        <div class="card dashboard-card">

            <div class="card-header">

                Doanh thu tháng

            </div>

            <div class="card-body text-center">

                <div id="goalChart" style="height:300px"></div>

                <h3 class="fw-bold">
                    {{ number_format($monthlyRevenue,0,',','.') }} đ
                </h3>

                <div id="sparkRevenue"></div>

                <div class="mt-3">

                    <small class="text-muted d-block mt-2">
                        Mục tiêu tháng:
                        <strong>{{ number_format($monthlyTarget,0,',','.') }}đ</strong>
                    </small>
                </div>

            </div>

        </div>

    </div>

</div>

<div class="row g-4 mt-1">

    <div class="col-md-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <small class="text-muted">

                    Booking tháng này

                </small>

                <h2 class="fw-bold mt-2">
                    {{ $bookingThisMonth }}
                </h2>

                <div class="{{ $bookingGrowth >= 0 ? 'text-success':'text-danger' }}">
                    <i class="bi bi-arrow-{{ $bookingGrowth >= 0 ? 'up':'down' }}"></i>
                    {{ abs($bookingGrowth) }}%
                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <small class="text-muted">
                    Doanh thu tháng này
                </small>

                <h2 class="fw-bold mt-2">
                    {{ number_format($monthlyRevenueCard, 0, ',', '.') }}đ
                </h2>

                <div class="{{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="bi bi-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($revenueGrowth) }}%
                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <small class="text-muted">

                    Khách mới

                </small>

                <h2 class="fw-bold mt-2">
                    {{ $newCustomers }}%
                </h2>

                <div class="{{ $customerGrowth >= 0 ? 'text-success':'text-danger' }}">
                    <i class="bi bi-arrow-{{ $customerGrowth >= 0 ? 'up':'down' }}"></i>
                    {{ abs($customerGrowth) }}%
                </div>
            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <small class="text-muted">

                    Tỷ lệ đặt sân

                </small>

                <h2 class="fw-bold mt-2">
                    {{ $occupancyRate }}%
                </h2>
                <div class="{{ $occupancyGrowth >= 0 ? 'text-success':'text-danger' }}">
                    <i class="bi bi-arrow-{{ $occupancyGrowth >= 0 ? 'up':'down' }}"></i>
                    {{ abs($occupancyGrowth) }}%
                </div>

                <div class="text-success">

                    +5%

                </div>

            </div>

        </div>

    </div>

</div>


<div class="row mt-4">



    <div class="row mt-4">

        <div class="col-lg-6">

            <div class="card dashboard-card">

                <div class="card-header">

                    <div>

                        <div class="fw-bold">

                            Top khách hàng

                        </div>

                        <small class="text-muted">

                            Đặt sân nhiều nhất

                        </small>

                    </div>

                </div>

                <div class="card-body">


                    @foreach($topCustomers as $customer)

                    <div class="d-flex align-items-center mb-4">

                        <div class="avatar-circle me-3">

                            {{ strtoupper(substr($customer->name,0,1)) }}

                        </div>

                        <div class="flex-grow-1">

                            <div class="fw-bold">

                                {{ $customer->name }}

                            </div>

                            <small class="text-muted">

                                Đã đặt sân tổng {{ $customer->total }} lần 

                            </small>

                        </div>

                        <span class="badge vip-badge">
                            <i class="bi bi-crown-fill me-1"></i> VIP
                        </span>

                    </div>

                    @endforeach

                </div>

            </div>

        </div>

        <div class="col-lg-6">

            <div class="card dashboard-card">

                <div class="card-header">

                    <div>

                        <div class="fw-bold">

                            Tỷ lệ sử dụng sân

                        </div>

                        <small class="text-muted">

                            Theo từng sân

                        </small>

                    </div>

                </div>

                <div class="card-body">

                    <div id="occupancyChart"></div>

                </div>

            </div>

        </div>

    </div>
    @endsection
    @push('scripts')
    <script>
    const sparkData = {

        "#spark1": @json($fieldSpark),

        "#spark2": @json($bookingSpark),

        "#spark3": @json($revenueSpark),

        "#spark4": @json($customerSpark)

    };

    console.log(sparkData);

    function sparkline(id, color) {

        new ApexCharts(document.querySelector(id), {

            chart: {
                height: 55,
                type: 'area',
                sparkline: {
                    enabled: true
                },
                toolbar: {
                    show: false
                }
            },

            stroke: {
                curve: 'smooth',
                width: 3
            },

            fill: {
                opacity: .2
            },

            colors: [color],

            series: [{
                data: sparkData[id] ?? []
            }]

        }).render();

    }

    sparkline("#spark1", "#206bc4");
    sparkline("#spark2", "#2fb344");
    sparkline("#spark3", "#f59f00");
    sparkline("#spark4", "#d63939");
    </script>
    <script>
    let revenueChart = new ApexCharts(
        document.querySelector("#revenueChart"), {

            chart: {
                height: 360,
                type: "area",
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },

            colors: ["#206bc4"],

            stroke: {
                curve: "smooth",
                width: 4
            },

            fill: {
                type: "gradient",
                gradient: {
                    opacityFrom: .45,
                    opacityTo: .05
                }
            },

            series: [{
                name: "Doanh thu",
                data: @json($revenue30Days['series'])
            }],

            xaxis: {
                categories: @json($revenue30Days['labels'])
            }

        }
    );

    revenueChart.render();

    document.querySelectorAll(".chart-tab").forEach(tab => {

        tab.addEventListener("mouseenter", function() {

            // Active button
            document.querySelectorAll(".chart-tab").forEach(item => {
                item.classList.remove("active");
            });

            this.classList.add("active");

            // ==========================
            // 30 ngày
            // ==========================

            if (this.dataset.type == "30") {

                revenueChart.updateOptions({

                    xaxis: {
                        categories: @json($revenue30Days['labels'])
                    }

                });

                revenueChart.updateSeries([{

                    name: "Doanh thu",

                    data: @json($revenue30Days['series'])

                }]);

            }

            // ==========================
            // 7 ngày
            // ==========================

            if (this.dataset.type == "7") {

                revenueChart.updateOptions({

                    xaxis: {
                        categories: @json($revenue7Days['labels'])
                    }

                });

                revenueChart.updateSeries([{

                    name: "Doanh thu",

                    data: @json($revenue7Days['series'])

                }]);

            }

            // ==========================
            // Quý 1
            // ==========================

            if (this.dataset.quarter == "1") {

                revenueChart.updateOptions({

                    xaxis: {
                        categories: ["Th1", "Th2", "Th3", "Th4"]
                    }

                });

                revenueChart.updateSeries([{

                    name: "Doanh thu",

                    data: @json($quarter1Revenue)

                }]);

            }

            // ==========================
            // Quý 2
            // ==========================

            if (this.dataset.quarter == "2") {

                revenueChart.updateOptions({

                    xaxis: {
                        categories: ["Th5", "Th6", "Th7", "Th8"]
                    }

                });

                revenueChart.updateSeries([{

                    name: "Doanh thu",

                    data: @json($quarter2Revenue)

                }]);

            }

            // ==========================
            // Quý 3
            // ==========================

            if (this.dataset.quarter == "3") {

                revenueChart.updateOptions({

                    xaxis: {
                        categories: ["Th9", "Th10", "Th11", "Th12"]
                    }

                });

                revenueChart.updateSeries([{

                    name: "Doanh thu",

                    data: @json($quarter3Revenue)

                }]);

            }

        });

    });
    </script>
    <script>
    new ApexCharts(

        document.querySelector("#bookingChart"),

        {

            chart: {

                type: "donut",

                height: 280

            },

            series: @json($bookingStatusChart),

            labels: [
                "Hoàn thành",
                "Chờ xác nhận",
                "Đã huỷ"
            ],

            colors: [

                "#2fb344",

                "#f59f00",

                "#d63939"

            ],

            legend: {

                position: "bottom"

            },

            dataLabels: {

                enabled: false

            }

        }

    ).render();
    </script>
    <script>
    // ===== DARK MODE =====

    const btn = document.getElementById("themeToggle");

    if (btn) {

        btn.onclick = function() {

            document.body.classList.toggle("dark-mode");

            localStorage.setItem(
                "theme",
                document.body.classList.contains("dark-mode")
            );

        };

    }

    if (localStorage.getItem("theme") == "true") {

        document.body.classList.add("dark-mode");

    }
    </script>

    <script>
    // ===== Counter Animation =====

    document.querySelectorAll(".stat-value").forEach(function(el) {

        let target = parseInt(el.innerText.replace(/\D/g, '')) || 0;

        let current = 0;

        let step = Math.ceil(target / 50);

        let timer = setInterval(function() {

            current += step;

            if (current >= target) {

                current = target;

                clearInterval(timer);

            }

            el.innerText = current.toLocaleString('vi-VN');

        }, 20);

    });
    </script>
    <script>
    new ApexCharts(
        document.querySelector("#goalChart"), {
            chart: {
                type: "radialBar",
                height: 280
            },

           series: [{{ $monthlyPercent }}],

            labels: ["Hoàn thành"],

            colors: ["#206bc4"],

            plotOptions: {
                radialBar: {
                    hollow: {
                        size: "65%"
                    },
                    track: {
                        background: "#edf2f7"
                    },
                    dataLabels: {
                        name: {
                            fontSize: "16px"
                        },
                        value: {
                            fontSize: "32px",
                            fontWeight: 700
                        }
                    }
                }
            }
        }
    ).render();
    </script>

    <script>
    new ApexCharts(

        document.querySelector("#occupancyChart"),

        {

            chart: {

                type: "bar",

                height: 320,

                toolbar: {

                    show: false

                }

            },

            series: [{

                name: "Booking",

                data: @json($fieldOccupancy->pluck('total'))

            }],

            xaxis: {

                categories: @json($fieldOccupancy->pluck('name'))

            },

            plotOptions: {

                bar: {

                    borderRadius: 8,

                    columnWidth: "45%"

                }

            },

            colors: ["#206bc4"]

        }

    ).render();
    </script>

    @endpush