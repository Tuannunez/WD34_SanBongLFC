@extends('layouts.app')

@section('title', 'Thêm bài viết')

@section('content')
<div class="container py-4">
    <h3>Thêm bài viết</h3>

    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tóm tắt</label>
            <textarea name="excerpt" class="form-control">{{ old('excerpt') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea name="content" class="form-control" rows="8">{{ old('content') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh (tải lên)</label>
            <input type="file" name="image_file" class="form-control">
            <small class="text-muted">Định dạng: jpg, png, webp. Kích thước tối đa 5MB.</small>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_published" class="form-check-input" id="published" checked>
            <label class="form-check-label" for="published">Đăng bài</label>
        </div>

        <button class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
