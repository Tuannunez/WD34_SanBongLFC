@extends('admin.layouts.app')

@section('title', 'Sửa cơ sở sân')
@section('page-title', 'Sửa cơ sở sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.stadiums.update', $stadium) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên sân</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $stadium->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ảnh</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $stadium->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $stadium->email) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $stadium->address) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Loại sân</label>
                        <select name="field_type_id" class="form-select" required>
                            <option value="">Chọn loại sân</option>
                            @foreach($fieldTypes as $fieldType)
                                <option value="{{ $fieldType->id }}" {{ old('field_type_id', $stadium->field_type_id) == $fieldType->id ? 'selected' : '' }}>
                                    {{ $fieldType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ mở</label>
                        <input type="time" name="open_time" class="form-control" value="{{ old('open_time', $stadium->open_time) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ đóng</label>
                        <input type="time" name="close_time" class="form-control" value="{{ old('close_time', $stadium->close_time) }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả về cơ sở sân bóng...">{{ old('description', $stadium->description) }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
