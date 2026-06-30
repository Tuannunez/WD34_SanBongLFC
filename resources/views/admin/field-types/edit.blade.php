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

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên loại</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $fieldType->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Số người</label>
                        <input type="number" name="number_of_players" class="form-control" value="{{ old('number_of_players', $fieldType->number_of_players) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $fieldType->description) }}</textarea>
                    </div>
                    <div class="col-12 form-check">
                        <input type="checkbox" name="status" class="form-check-input" id="status" {{ old('status', $fieldType->status) ? 'checked' : '' }}>
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
