@extends('admin.layouts.app')

@section('title', 'Chi tiết cơ sở sân')
@section('page-title', 'Chi tiết cơ sở sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <img src="{{ $stadium->image ? (str_starts_with($stadium->image, 'http') ? $stadium->image : asset('storage/' . $stadium->image)) : asset('images/logo.png') }}" class="img-fluid rounded" alt="{{ $stadium->name }}">
                </div>
                <div class="col-md-8">
                    <h3 class="fw-bold">{{ $stadium->name }}</h3>
                    <p><strong>Địa chỉ:</strong> {{ $stadium->address }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $stadium->phone }}</p>
                    <p><strong>Email:</strong> {{ $stadium->email }}</p>
                    <p><strong>Loại sân:</strong> {{ $stadium->fieldType?->name ?? 'Chưa chọn' }}</p>
                    <p><strong>Giờ hoạt động:</strong> {{ $stadium->open_time }} - {{ $stadium->close_time }}</p>
                    <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
