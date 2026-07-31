@extends('admin.layouts.app')

@section('title','Dashboard')
@section('page-title','Dashboard')

@push('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
body{

background:

linear-gradient(

180deg,

#f8fafc 0%,

#f3f6fb 100%

);

font-family:Inter,sans-serif;

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

.dashboard-card .card-header{

padding:22px 24px;

background:white;

font-size:17px;

font-weight:700;

border-bottom:1px solid #eef2f7;

display:flex;

justify-content:space-between;

align-items:center;

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
.dark-mode{

background:#111827;

color:white;

}

.dark-mode .card{

background:#1e293b;

color:white;

}

.dark-mode .dashboard-card .card-header{

background:#1e293b;

}

.dark-mode table{

color:white;

}

.dark-mode .table-light{

background:#243041;

}

.dark-mode .btn-light{

background:#334155;

color:white;

border:0;

.card{

transition:.35s;

}

.card:hover{

transform:translateY(-6px);

box-shadow:0 18px 35px rgba(0,0,0,.08);

}

.btn{

transition:.25s;

}

.btn:hover{

transform:translateY(-2px);

}

.progress-bar{

transition:width 1.5s ease;

}

.table tbody tr{

transition:.25s;

}

.table tbody tr:hover{

background:#f8fbff;

transform:scale(1.01);

}

}

.avatar-circle{

width:44px;

height:44px;

border-radius:50%;

background:#206bc4;

color:white;

display:flex;

align-items:center;

justify-content:center;

font-weight:700;

}

.skeleton{

height:20px;

background:linear-gradient(

90deg,

#ececec,

#f8f8f8,

#ececec

);

background-size:300%;

animation:skeleton 1.5s infinite;

border-radius:10px;

}

@keyframes skeleton{

0%{

background-position:100%;

}

100%{

background-position:-100%;

}

}

.floating-btn{

position:fixed;

right:35px;

bottom:35px;

width:60px;

height:60px;

border-radius:50%;

background:#206bc4;

color:white;

display:flex;

align-items:center;

justify-content:center;

font-size:24px;

box-shadow:0 12px 30px rgba(0,0,0,.2);

z-index:999;

transition:.3s;

}

.floating-btn:hover{

transform:scale(1.08);

color:white;

}

.stat-icon{

transition:.35s;

}

.stat-card:hover .stat-icon{

transform:rotate(-12deg) scale(1.1);

}

.stat-card::before{

content:"";

position:absolute;

top:-80px;

right:-80px;

width:180px;

height:180px;

background:

rgba(255,255,255,.12);

border-radius:50%;

}
</style>

@endpush

@section('content')

<div class="dashboard-header">

    <div>

        <div class="dashboard-title">

            Dashboard

        </div>

        <div class="dashboard-subtitle">

            Xin chào Admin 👋

        </div>

    </div>

    <div>

        <button class="btn btn-light" id="themeToggle">

<i class="bi bi-moon"></i>

</button>

<button class="btn btn-light dashboard-btn">

Refresh

</button>

<button class="btn btn-primary dashboard-btn">

Export

</button>

    </div>

</div>

<div class="row mb-4">

    <div class="col-lg-12">

        <div class="card border-0 rounded-4 shadow-sm overflow-hidden">

            <div class="card-body p-4">

                <div class="row align-items-center">

                    <div class="col-lg-8">

                        <h2 class="fw-bold mb-2">
                            👋 Chào mừng trở lại, Admin
                        </h2>

                        <p class="text-muted mb-4">
                            Quản lý sân bóng, theo dõi doanh thu và các đơn đặt sân
                            ngay trên Dashboard.
                        </p>

                        <div class="d-flex gap-3">

                            <a href="#" class="btn btn-primary px-4">

                                <i class="bi bi-plus-circle me-2"></i>

                                Thêm sân

                            </a>

                            <a href="#" class="btn btn-outline-primary px-4">

                                <i class="bi bi-graph-up me-2"></i>

                                Xem báo cáo

                            </a>

                        </div>

                    </div>

                    <div class="col-lg-4 text-center">

                        <img src="{{ asset('images/dashboard.svg') }}"
                             style="max-height:180px">

                    </div>

                </div>

            </div>

        </div>

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

                        <div class="stat-growth">

                            <i class="bi bi-arrow-up"></i>

                            6%

                        </div>

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

                            {{ number_format($monthlyRevenue,0,',','.') }}

                        </div>

                        <div class="stat-growth">

                            <i class="bi bi-arrow-up"></i>

                            18%

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

                <span>Revenue Analytics</span>

                <div>

                    <button class="btn btn-sm btn-light">7 ngày</button>
                    <button class="btn btn-sm btn-primary">30 ngày</button>

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

                <div class="d-flex justify-content-between">

                    <span>Hoàn thành</span>

                    <strong class="text-success">68%</strong>

                </div>

                <div class="progress mt-2">

                    <div class="progress-bar bg-success" style="width:68%"></div>

                </div>

                <div class="d-flex justify-content-between mt-4">

                    <span>Đang xử lý</span>

                    <strong class="text-warning">21%</strong>

                </div>

                <div class="progress mt-2">

                    <div class="progress-bar bg-warning" style="width:21%"></div>

                </div>

                <div class="d-flex justify-content-between mt-4">

                    <span>Đã huỷ</span>

                    <strong class="text-danger">11%</strong>

                </div>

                <div class="progress mt-2">

                    <div class="progress-bar bg-danger" style="width:11%"></div>

                </div>

            </div>

        </div>

    </div>

</div>
<div class="row mt-4">

    {{-- Revenue Chart --}}
    <div class="col-lg-8">

        <div class="card dashboard-card h-100">

            <div class="card-header d-flex justify-content-between align-items-center">

                <span>Revenue Analytics</span>

                <div>

                    <button class="btn btn-sm btn-light">7 ngày</button>
                    <button class="btn btn-sm btn-primary">30 ngày</button>

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

                <div class="d-flex justify-content-between">

                    <span>Hoàn thành</span>

                    <strong class="text-success">68%</strong>

                </div>

                <div class="progress mt-2">

                    <div class="progress-bar bg-success" style="width:68%"></div>

                </div>

                <div class="d-flex justify-content-between mt-4">

                    <span>Đang xử lý</span>

                    <strong class="text-warning">21%</strong>

                </div>

                <div class="progress mt-2">

                    <div class="progress-bar bg-warning" style="width:21%"></div>

                </div>

                <div class="d-flex justify-content-between mt-4">

                    <span>Đã huỷ</span>

                    <strong class="text-danger">11%</strong>

                </div>

                <div class="progress mt-2">

                    <div class="progress-bar bg-danger" style="width:11%"></div>

                </div>

            </div>

        </div>

    </div>

</div>
<div class="row mt-4">

    <div class="col-lg-8">

        <div class="card dashboard-card">

            <div class="card-header">

                Đơn đặt sân mới nhất

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

                                    <small class="text-muted">

                                        {{ $booking->user_email }}

                                    </small>

                                </td>

                                <td>

                                    {{ $booking->field_name }}

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}

                                </td>

                                <td>

                                    @if(($booking->status ?? '')=='completed')

                                    <span class="badge badge-status status-success">

                                        Hoàn thành

                                    </span>

                                    @elseif(($booking->status ?? '')=='pending')

                                    <span class="badge badge-status status-warning">

                                        Đang xử lý

                                    </span>

                                    @else

                                    <span class="badge badge-status status-danger">

                                        {{ $booking->status ?? 'Huỷ' }}

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
    <div class="col-lg-4">

        <div class="card dashboard-card">

            <div class="card-header">

                System Status

            </div>

            <div class="col-lg-4">

<div class="card dashboard-card">

<div class="card-header">

Thông báo

</div>

<div class="card-body">

<div class="d-flex mb-3">

<i class="bi bi-bell-fill text-warning me-3 fs-4"></i>

<div>

<strong>Có booking mới</strong>

<div class="text-muted">

5 phút trước

</div>

</div>

</div>

<div class="d-flex mb-3">

<i class="bi bi-cash-stack text-success me-3 fs-4"></i>

<div>

<strong>Thanh toán thành công</strong>

<div class="text-muted">

20 phút trước

</div>

</div>

</div>

<div class="d-flex">

<i class="bi bi-exclamation-circle text-danger me-3 fs-4"></i>

<div>

<strong>1 booking bị huỷ</strong>

<div class="text-muted">

1 giờ trước

</div>

</div>

</div>

</div>

</div>

</div>

<div class="card dashboard-card mt-4">

<div class="card-header">

Lịch hôm nay

</div>

<div class="card-body">

<div class="d-flex justify-content-between mb-3">

<div>

<strong>07:00 - 08:30</strong>

<div class="text-muted">

Sân số 1

</div>

</div>

<span class="badge bg-success">

Đã đặt

</span>

</div>

<div class="d-flex justify-content-between mb-3">

<div>

<strong>09:00 - 10:30</strong>

<div class="text-muted">

Sân số 2

</div>

</div>

<span class="badge bg-warning">

Đang chờ

</span>

</div>

<div class="d-flex justify-content-between">

<div>

<strong>18:00 - 19:30</strong>

<div class="text-muted">

Sân số 5

</div>

</div>

<span class="badge bg-primary">

Sắp tới

</span>

</div>

</div>

</div>

            <div class="card-body">

                <div class="system-item">

                    <div class="d-flex justify-content-between">

                        <span>Cơ sở</span>

                        <span class="system-number">

                            {{ $totalStadiums }}

                        </span>

                    </div>

                    <div class="progress mt-2">

                        <div class="progress-bar bg-primary" style="width:100%"></div>

                    </div>

                </div>

                <div class="system-item">

                    <div class="d-flex justify-content-between">

                        <span>Loại sân</span>

                        <span class="system-number">

                            {{ $totalFieldTypes }}

                        </span>

                    </div>

                    <div class="progress mt-2">

                        <div class="progress-bar bg-success" style="width:80%"></div>

                    </div>

                </div>

                <div class="system-item">

                    <div class="d-flex justify-content-between">

                        <span>Khung giờ</span>

                        <span class="system-number">

                            {{ $totalTimeSlots }}

                        </span>

                    </div>

                    <div class="progress mt-2">

                        <div class="progress-bar bg-warning" style="width:70%"></div>

                    </div>

                </div>

                <div class="system-item">

                    <div class="d-flex justify-content-between">

                        <span>Dịch vụ</span>

                        <span class="system-number">

                            {{ $totalServices }}

                        </span>

                    </div>

                    <div class="progress mt-2">

                        <div class="progress-bar bg-info" style="width:60%"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<div class="row mt-4">

    {{-- Top Fields --}}
    <div class="col-lg-6">

        <div class="card dashboard-card">

            <div class="card-header d-flex justify-content-between">

                <span>Top sân được đặt nhiều</span>

                <span class="text-primary fw-bold">Top 5</span>

            </div>

            <div class="card-body">

                @php
                    $topFields = [
                        ['name'=>'Sân A','percent'=>95],
                        ['name'=>'Sân B','percent'=>82],
                        ['name'=>'Sân C','percent'=>71],
                        ['name'=>'Sân D','percent'=>56],
                        ['name'=>'Sân E','percent'=>43],
                    ];
                @endphp

                @foreach($topFields as $field)

                <div class="mb-4">

                    <div class="d-flex justify-content-between">

                        <strong>

                            {{ $field['name'] }}

                        </strong>

                        <span>

                            {{ $field['percent'] }}%

                        </span>

                    </div>

                    <div class="progress mt-2">

                        <div

                            class="progress-bar bg-primary"

                            style="width:{{$field['percent']}}%"

                        ></div>

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

                <div

                    id="goalChart"

                    style="height:300px"

                ></div>

                <h3 class="fw-bold">

                    {{ number_format($monthlyRevenue,0,',','.') }}

                    đ

                </h3>

                <p class="text-muted">

                    76% mục tiêu tháng

                </p>

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

                    238

                </h2>

                <div class="text-success">

                    <i class="bi bi-arrow-up"></i>

                    +18%

                </div>

            </div>

        </div>

    </div>

    <div class="col-md-3">

        <div class="card dashboard-card">

            <div class="card-body">

                <small class="text-muted">

                    Doanh thu hôm nay

                </small>

                <h2 class="fw-bold mt-2">

                    8.4M

                </h2>

                <div class="text-success">

                    +12%

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

                    21

                </h2>

                <div class="text-danger">

                    -2%

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

                    84%

                </h2>

                <div class="text-success">

                    +5%

                </div>

            </div>

        </div>

    </div>

</div>

<div class="row mt-4">

<div class="col-lg-12">

<div class="card dashboard-card">

<div class="card-header">

Thao tác nhanh

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3">

<a href="#" class="btn btn-primary w-100 py-3">

<i class="bi bi-plus-circle"></i>

<br>

Thêm sân

</a>

</div>

<div class="col-md-3">

<a href="#" class="btn btn-success w-100 py-3">

<i class="bi bi-calendar2-plus"></i>

<br>

Đặt sân

</a>

</div>

<div class="col-md-3">

<a href="#" class="btn btn-warning w-100 py-3">

<i class="bi bi-people"></i>

<br>

Khách hàng

</a>

</div>

<div class="col-md-3">

<a href="#" class="btn btn-dark w-100 py-3">

<i class="bi bi-graph-up"></i>

<br>

Báo cáo

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-lg-6">

<div class="card dashboard-card">

<div class="card-header">

Hoạt động gần đây

</div>

<div class="card-body">

<div class="timeline">

<div class="d-flex mb-4">

<div class="me-3">

<span class="badge bg-primary rounded-circle p-3">

<i class="bi bi-check"></i>

</span>

</div>

<div>

<strong>

Nguyễn Văn A

</strong>

<br>

Đặt sân số 3

<br>

<small class="text-muted">

10 phút trước

</small>

</div>

</div>

<div class="d-flex mb-4">

<div class="me-3">

<span class="badge bg-success rounded-circle p-3">

<i class="bi bi-cash"></i>

</span>

</div>

<div>

<strong>

Thanh toán thành công

</strong>

<br>

650.000đ

<br>

<small class="text-muted">

25 phút trước

</small>

</div>

</div>

<div class="d-flex">

<div class="me-3">

<span class="badge bg-warning rounded-circle p-3">

<i class="bi bi-calendar"></i>

</span>

</div>

<div>

<strong>

Có booking mới

</strong>

<br>

Sân số 5

<br>

<small class="text-muted">

1 giờ trước

</small>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-6">

<div class="card dashboard-card">

<div class="card-header">

Lịch hôm nay

</div>

<div class="card-body">

<div id="calendar"></div>

</div>

</div>

</div>

</div>

<a

href="#"

class="floating-btn"

>

<i class="bi bi-plus-lg"></i>

</a>
@endsection
@push('scripts')
<script>

function sparkline(id,color){

new ApexCharts(document.querySelector(id),{

chart:{
height:55,
type:'area',
sparkline:{enabled:true},
toolbar:{show:false}
},

stroke:{
curve:'smooth',
width:3
},

fill:{
opacity:.2
},

colors:[color],

series:[{
data:[
12,
18,
14,
22,
17,
29,
31,
27,
35,
40,
38,
44
]
}]

}).render();

}

sparkline("#spark1","#206bc4");

sparkline("#spark2","#2fb344");

sparkline("#spark3","#f59f00");

sparkline("#spark4","#d63939");

</script>
<script>

new ApexCharts(

document.querySelector("#revenueChart"),

{

chart:{

height:360,

type:"area",

toolbar:{

show:false

},

zoom:{

enabled:false

}

},

colors:["#206bc4"],

stroke:{

curve:"smooth",

width:4

},

fill:{

type:"gradient",

gradient:{

opacityFrom:.45,

opacityTo:.05

}

},

series:[{
    name:"Revenue",
    data:@json($monthlyRevenueChart)
}],

xaxis:{

categories:[

"Jan",

"Feb",

"Mar",

"Apr",

"May",

"Jun",

"Jul",

"Aug",

"Sep",

"Oct",

"Nov",

"Dec"

]

},

grid:{

borderColor:"#eef2f7"

},

tooltip:{

theme:"light"

}

}

).render();

</script>
<script>

new ApexCharts(

document.querySelector("#bookingChart"),

{

chart:{

type:"donut",

height:280

},

series:[

65,

20,

15

],

labels:[

"Completed",

"Pending",

"Cancelled"

],

colors:[

"#2fb344",

"#f59f00",

"#d63939"

],

legend:{

position:"bottom"

},

dataLabels:{

enabled:false

}

}

).render();

</script>
<script>

// ===== DARK MODE =====

const btn = document.getElementById("themeToggle");

if(btn){

    btn.onclick = function(){

        document.body.classList.toggle("dark-mode");

        localStorage.setItem(
            "theme",
            document.body.classList.contains("dark-mode")
        );

    };

}

if(localStorage.getItem("theme")=="true"){

    document.body.classList.add("dark-mode");

}

</script>

<script>

// ===== Counter Animation =====

document.querySelectorAll(".stat-value").forEach(function(el){

    let target = parseInt(el.innerText.replace(/\D/g,'')) || 0;

    let current = 0;

    let step = Math.ceil(target / 50);

    let timer = setInterval(function(){

        current += step;

        if(current >= target){

            current = target;

            clearInterval(timer);

        }

        el.innerText=current.toLocaleString('vi-VN');

    },20);

});

</script>
<script>
new ApexCharts(
    document.querySelector("#goalChart"),
    {
        chart:{
            type:"radialBar",
            height:280
        },

        series:[76],

        labels:["Hoàn thành"],

        colors:["#206bc4"],

        plotOptions:{
            radialBar:{
                hollow:{
                    size:"65%"
                },
                track:{
                    background:"#edf2f7"
                },
                dataLabels:{
                    name:{
                        fontSize:"16px"
                    },
                    value:{
                        fontSize:"32px",
                        fontWeight:700
                    }
                }
            }
        }
    }
).render();
</script>

@endpush