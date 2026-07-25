@extends('admin.layouts.app')

@section('title', 'Xem bài viết')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Xem bài viết</h3>
        <div>
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Quay lại</a>
            <a href="{{ route('admin.news.edit', $news->id) }}" class="btn btn-primary ms-2">Sửa</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="page-card p-4 mb-4">
                <div class="mb-3">
                    <small class="text-muted">{{ $news->published_at?->format('d/m/Y H:i') }}</small>
                    <h2 class="mt-2">{{ $news->title }}</h2>
                    @if($news->excerpt)
                        <p class="text-secondary">{{ $news->excerpt }}</p>
                    @endif
                </div>

                @if($news->image)
                    <div class="mb-3">
                        <img src="{{ $news->image }}" alt="{{ $news->title }}" style="width:100%; max-width:880px; border-radius:12px; object-fit:cover;" />
                    </div>
                @endif

                <div class="news-article">
                    {!! nl2br(e($news->content)) !!}
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="stat-card p-3 mb-3">
                <h6 class="mb-2">Thông tin</h6>
                <ul class="list-unstyled">
                    <li><strong>Đã đăng:</strong> {{ $news->is_published ? 'Có' : 'Chưa' }}</li>
                    <li><strong>Slug:</strong> {{ $news->slug }}</li>
                    <li><strong>Tạo lúc:</strong> {{ $news->created_at?->format('d/m/Y H:i') }}</li>
                    <li><strong>Cập nhật:</strong> {{ $news->updated_at?->format('d/m/Y H:i') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
