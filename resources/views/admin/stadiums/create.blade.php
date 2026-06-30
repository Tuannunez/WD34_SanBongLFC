@extends('admin.layouts.app')

@section('title', 'Thêm cơ sở sân')
@section('page-title', 'Thêm cơ sở sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.stadiums.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên sân</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ảnh</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Loại sân</label>
                        <select name="field_type_id" class="form-select" required>
                            <option value="">Chọn loại sân</option>
                            @foreach($fieldTypes as $fieldType)
                                <option value="{{ $fieldType->id }}" {{ old('field_type_id') == $fieldType->id ? 'selected' : '' }}>
                                    {{ $fieldType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ mở</label>
                        <input type="time" name="open_time" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ đóng</label>
                        <input type="time" name="close_time" class="form-control" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
