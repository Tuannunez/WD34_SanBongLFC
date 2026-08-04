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
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Ảnh</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" placeholder="0xxxxxxxxx" pattern="^0(3|5|7|8|9)[0-9]{8}$" maxlength="10" required>
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="example@domain.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="form-control @error('address') is-invalid @enderror" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giá gốc (VNĐ/trận)</label>
                        <input type="number" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" min="1" step="1" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ mở</label>
                        <input type="time" name="open_time" value="{{ old('open_time') }}" class="form-control @error('open_time') is-invalid @enderror" required>
                        @error('open_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Giờ đóng</label>
                        <input type="time" name="close_time" value="{{ old('close_time') }}" class="form-control @error('close_time') is-invalid @enderror" required>
                        @error('close_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Nhập mô tả về cơ sở sân bóng...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var phoneInput = document.querySelector('input[name="phone"]');
        if (!phoneInput) return;

        phoneInput.addEventListener('invalid', function (event) {
            event.preventDefault();
            phoneInput.classList.add('is-invalid');
        });

        phoneInput.addEventListener('input', function () {
            if (phoneInput.validity.valid) {
                phoneInput.classList.remove('is-invalid');
            }
        });
    });
</script>
@endpush
@endsection
