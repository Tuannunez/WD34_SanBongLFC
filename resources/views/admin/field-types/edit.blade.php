@extends('admin.layouts.app')

@section('title', 'Sửa loại sân')
@section('page-title', 'Sửa loại sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.field-types.update', $fieldType) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên loại sân</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $fieldType->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số người</label>
                        <input type="number" name="number_of_players" class="form-control" min="1" value="{{ old('number_of_players', $fieldType->number_of_players) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $fieldType->description) }}</textarea>
                    </div>
                    <div class="col-md-12 form-check mt-2">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $fieldType->status) ? 'checked' : '' }}>
                        <label class="form-check-label" for="status">Hoạt động</label>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.field-types.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
