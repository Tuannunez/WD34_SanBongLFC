@extends('layouts.app')

@section('title', 'Tin tức')

@section('content')
<div class="container py-5">
    <div class="row gy-4">
        @foreach($news as $item)
            <div class="col-12">
                <article class="news-list-item shadow-sm rounded-4 overflow-hidden bg-white">
                    @if($item->image)
                        <div class="news-list-image">
                            <img src="{{ $item->image }}" alt="{{ $item->title }}">
                        </div>
                    @endif
                    <div class="news-list-content p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge bg-success">Tin tức</span>
                                <small class="text-muted">{{ $item->published_at?->format('d/m/Y') }}</small>
                            </div>
                            <h2 class="h4 fw-bold mb-2">{{ $item->title }}</h2>
                            <p class="mb-3 text-secondary">{{ $item->excerpt }}</p>
                        </div>
                        <a href="{{ route('news.show', $item->id) }}" class="text-success fw-semibold">Xem thêm →</a>
                    </div>
                </article>
            </div>
        @endforeach
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>
@endsection
