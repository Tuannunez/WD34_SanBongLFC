@extends('layouts.app')

@section('title', 'Sửa bài viết')

@section('content')
<div class="container py-4">
    <h3>Sửa bài viết</h3>

    <form method="POST" action="{{ route('admin.news.update', $news->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title', $news->title) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tóm tắt</label>
            <textarea name="excerpt" class="form-control">{{ old('excerpt', $news->excerpt) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea name="content" class="form-control" rows="8">{{ old('content', $news->content) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Ảnh hiện tại</label>
            @if($news->image)
                <div class="mb-2">
                    <img src="{{ $news->image }}" alt="" style="max-width:200px; height:auto;" />
                </div>
            @endif
            <label class="form-label">Thay ảnh (tải lên)</label>
            <input type="file" name="image_file" class="form-control">
            <small class="text-muted">Để trống nếu không muốn thay ảnh. Định dạng: jpg, png, webp. Kích thước tối đa 5MB.</small>
        </div>

        <div class="mb-3 form-check">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" class="form-check-input" id="published" {{ old('is_published', $news->is_published) ? 'checked' : '' }}>
            <label class="form-check-label" for="published">Đăng bài</label>
        </div>

        <button class="btn btn-success">Lưu</button>
        <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
