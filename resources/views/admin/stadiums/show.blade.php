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
                    @if($stadium->description)
                        <p><strong>Mô tả:</strong></p>
                        <p>{{ $stadium->description }}</p>
                    @endif
                    <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>

            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quản lý sân con</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.stadiums.fields.store', $stadium->id) }}" method="POST" class="row g-3 mb-4">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">Tên sân con</label>
                            <input type="text" name="name" class="form-control" placeholder="Ví dụ: Sân 1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Loại sân</label>
                            <select name="field_type_id" class="form-select" required>
                                <option value="">-- Chọn loại sân --</option>
                                @foreach($fieldTypes as $fieldType)
                                    <option value="{{ $fieldType->id }}">{{ $fieldType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Giá/giờ</label>
                            <input type="number" name="price_per_hour" class="form-control" min="0" step="1000" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="1">Hoạt động</option>
                                <option value="0">Tạm khóa</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">Thêm sân</button>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Mô tả sân con"></textarea>
                        </div>
                    </form>

                    @if($fields->isNotEmpty())
                        <div class="row g-3">
                            @foreach($fields as $field)
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                            <div>
                                                <h6 class="fw-bold mb-1">{{ $field->name }}</h6>
                                                <div class="text-muted small">{{ $field->fieldType?->name ?? 'Chưa chọn loại sân' }}</div>
                                            </div>
                                            <span class="badge {{ $field->status ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $field->status ? 'Hoạt động' : 'Tạm khóa' }}
                                            </span>
                                        </div>

                                        <form action="{{ route('admin.stadiums.fields.update', [$stadium->id, $field->id]) }}" method="POST" class="row g-2">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-12">
                                                <label class="form-label small">Tên sân</label>
                                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $field->name }}" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Loại sân</label>
                                                <select name="field_type_id" class="form-select form-select-sm" required>
                                                    @foreach($fieldTypes as $fieldType)
                                                        <option value="{{ $fieldType->id }}" @selected($field->field_type_id == $fieldType->id)>{{ $fieldType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Giá/giờ</label>
                                                <input type="number" name="price_per_hour" class="form-control form-control-sm" min="0" step="1000" value="{{ $field->price_per_hour ?? 0 }}">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Trạng thái</label>
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="1" @selected($field->status)>Hoạt động</option>
                                                    <option value="0" @selected(!$field->status)>Tạm khóa</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small">Mô tả</label>
                                                <textarea name="description" class="form-control form-control-sm" rows="2">{{ $field->description }}</textarea>
                                            </div>
                                            <div class="col-12 d-flex gap-2 mt-2">
                                                <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                                                <form action="{{ route('admin.stadiums.fields.destroy', [$stadium->id, $field->id]) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sân con này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                                </form>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-light border mb-0">Chưa có sân con nào cho cơ sở này.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
