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
                    <p class="text-muted mb-0">Giá được tính tự động theo từng sân con: sân 7 350.000đ, sân 9 400.000đ, sân 11 500.000đ/90 phút; ca từ 18:00 cộng 100.000đ.</p>
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
                            <button type="submit" class="btn btn-primary w-100">Thêm / Chỉnh thời gian</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-4">
                        <thead class="table-light text-center">
                            <tr>
                                <th>Khung giờ</th>
                                @foreach($fields as $field)
                                    <th>{{ $field->name ?: 'Sân #' . $field->id }}</th>
                                @endforeach
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceTable as $row)
                                @php($slot = $row['slot'])
                                <tr>
                                    <td class="text-center">
                                        <form id="slot-form-{{ $slot->id }}" action="{{ route('admin.time-slots.update', [$stadium->id, $slot->id]) }}" method="POST" class="row g-2 align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-5">
                                                <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="time" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}" required>
                                            </div>
                                        </form>
                                    </td>
                                    @foreach($fields as $field)
                                        <td class="text-end text-success fw-bold text-nowrap">
                                            {{ number_format((float) ($row['prices'][$field->id] ?? 0), 0, ',', '.') }}đ
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                            <button type="submit" form="slot-form-{{ $slot->id }}" class="btn btn-success btn-sm">Lưu</button>
                                            <form action="{{ route('admin.time-slots.destroy', [$stadium->id, $slot->id]) }}" method="POST" onsubmit="return confirm('Xác nhận xóa khung giờ này?');" style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                            </form>
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
