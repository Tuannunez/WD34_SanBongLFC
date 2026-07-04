@extends('admin.layouts.app')

@section('title', 'Sửa vai trò')
@section('page-title', 'Sửa vai trò')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Tên vai trò</label>
                    <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ $role->slug }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control">{{ $role->description }}</textarea>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="status" class="form-check-input" id="status" {{ $role->status ? 'checked' : '' }}>
                    <label for="status" class="form-check-label">Hoạt động</label>
                </div>
                <button class="btn btn-primary">Lưu</button>
            </form>
        </div>
    </div>
@endsection
