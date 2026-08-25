@extends('admin.layouts.app')

@section('title', 'Tạo vai trò')
@section('page-title', 'Tạo vai trò mới')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Tên vai trò</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" name="status" value="1" class="form-check-input" id="status" checked>
                    <label for="status" class="form-check-label">Hoạt động</label>
                </div>
                <button class="btn btn-primary">Tạo</button>
            </form>
        </div>
    </div>
@endsection
