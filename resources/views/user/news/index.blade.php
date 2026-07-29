@extends('layouts.app')

@section('title', 'Tin tức')

@section('content')
<div class="container py-5">
    <div class="row gy-4">
        <div class="col-xl-8">
            @if($news->count())
                @php
                    $newsCollection = $news->getCollection();
                    $featured = $newsCollection->first();
                    $sidebarItems = $newsCollection->slice(1, 5);
                    $moreItems = $newsCollection->slice(1, 4);
                @endphp

                <article class="news-featured-card card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    @php
                        $featuredImages = !empty($featured->images) && is_array($featured->images) ? $featured->images : ($featured->image ? [$featured->image] : []);
                    @endphp
                    @if(count($featuredImages))
                        <img src="{{ $featuredImages[0] }}" alt="{{ $featured->title }}" class="news-featured-image">
                    @endif
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <span class="badge bg-success">Tin tức</span>
                            <small class="text-muted">{{ $featured->published_at?->format('d/m/Y') }}</small>
                        </div>
                        <h1 class="h2 fw-bold mb-3">{{ $featured->title }}</h1>
                        <p class="text-muted mb-4">{{ $featured->excerpt }}</p>
                        <a href="{{ route('news.show', $featured->id) }}" class="btn btn-success rounded-pill px-4">Xem chi tiết</a>
                    </div>
                </article>

                <div class="row g-4">
                    @foreach($moreItems as $item)
                        @php
                            $itemImages = !empty($item->images) && is_array($item->images) ? $item->images : ($item->image ? [$item->image] : []);
                        @endphp
                        <div class="col-md-6">
                            <article class="news-card small-card h-100">
                                @if(count($itemImages))
                                    <img src="{{ $itemImages[0] }}" alt="{{ $item->title }}">
                                @endif
                                <div class="news-card-body">
                                    <p class="news-card-meta">{{ $item->published_at?->format('d/m/Y') }}</p>
                                    <h3 class="news-card-title">{{ $item->title }}</h3>
                                    <p class="news-card-text">{{ $item->excerpt }}</p>
                                    <a href="{{ route('news.show', $item->id) }}" class="text-success fw-semibold">Xem thêm →</a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                    <p class="mb-0">Hiện chưa có tin tức nào.</p>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Tin nhanh</h5>

                        @foreach($sidebarItems as $item)
                            <div class="news-sidebar-item pb-3 mb-3 border-bottom">
                                <a href="{{ route('news.show', $item->id) }}" class="text-dark text-decoration-none">
                                    <div class="d-flex align-items-start gap-3">
                                        @php
                                            $itemSidebarImages = !empty($item->images) && is_array($item->images) ? $item->images : ($item->image ? [$item->image] : []);
                                        @endphp
                                        @if(count($itemSidebarImages))
                                            <img src="{{ $itemSidebarImages[0] }}" alt="{{ $item->title }}" class="news-sidebar-thumb">
                                        @endif
                                        <div>
                                            <div class="mb-1 text-muted small">{{ $item->published_at?->format('d/m/Y') }}</div>
                                            <h6 class="mb-1 fw-semibold">{{ \Illuminate\Support\Str::limit($item->title, 60) }}</h6>
                                            <p class="mb-0 text-muted small">{{ \Illuminate\Support\Str::limit($item->excerpt ?: $item->content, 80) }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                        <a href="{{ route('news.index') }}" class="text-success fw-semibold">Xem thêm tin tức →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $news->links() }}
    </div>
</div>
@endsection
