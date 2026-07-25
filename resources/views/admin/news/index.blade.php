@extends('admin.layouts.app')

@section('title', 'Quản lý Tin tức')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Quản lý Tin tức</h3>
        <a href="{{ route('admin.news.create') }}" class="btn btn-success">Thêm bài viết</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="list-group">
        @foreach($news as $item)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        @if($item->image)
                            <img src="{{ $item->image }}" alt="" style="width:96px; height:64px; object-fit:cover; border-radius:6px; margin-right:12px;" />
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $item->title }}</h5>
                            <small class="text-muted">{{ $item->published_at?->format('Y-m-d') }}</small>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('news.show', $item->id) }}" class="btn btn-outline-primary btn-sm me-2">Xem</a>
                        <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-primary btn-sm me-2">Sửa</a>
                        <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Xóa bài viết?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $news->links() }}
    </div>
</div>
@endsection
