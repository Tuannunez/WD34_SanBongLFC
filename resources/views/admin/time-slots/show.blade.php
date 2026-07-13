@extends('admin.layouts.app')

@section('title', 'Giá cố định theo sân')
@section('page-title', 'Giá cố định theo sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold">Giá cố định theo sân: {{ $stadium->name }}</h4>
                    <p class="text-muted mb-0">Cập nhật giá cố định cho từng khung giờ của sân.</p>
                </div>
                <a href="{{ route('admin.time-slots.index') }}" class="btn btn-secondary">Quay lại danh sách sân</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row mb-4">
                <div class="col-md-8">
                    <form action="{{ route('admin.time-slots.add', $stadium->id) }}" method="POST" class="row g-2 align-items-center">
                        @csrf
                        <div class="col-md-3">
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="price" class="form-control text-end" placeholder="Giá (VNĐ)" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Thêm / Chỉnh thời gian</button>
                        </div>
                    </form>
                </div>
            </div>

            <form id="prices-form" action="{{ route('admin.time-slots.store', $stadium->id) }}" method="POST" style="display:none">
                @csrf
            </form>

            <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-4">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Khung giờ</th>
                                <th>Giá cố định (VNĐ/trận)</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeSlots as $slot)
                                <tr>
                                    <td class="text-center">
                                        <form action="{{ route('admin.time-slots.update', [$stadium->id, $slot->id]) }}" method="POST" class="row g-2 align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-5">
                                                <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="time" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}" required>
                                            </div>
                                    </td>
                                    <td>
                                            <input type="text" name="price" class="form-control text-end" value="{{ isset($existing[$slot->id]) ? number_format($existing[$slot->id], 0, ',', '.') : '' }}" placeholder="Giá (VNĐ)">
                                    </td>
                                    <td class="text-center">
                                            <button type="submit" class="btn btn-success btn-sm">Lưu</button>
                                        </form>
                                        @if(isset($existing[$slot->id]))
                                            <form action="{{ route('admin.time-slots.destroy', [$stadium->id, $slot->id]) }}" method="POST" onsubmit="return confirm('Xác nhận xóa giá cố định cho khung giờ này?');" style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
</div>
@endsection
