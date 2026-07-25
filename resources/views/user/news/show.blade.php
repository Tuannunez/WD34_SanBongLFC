@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="news-detail-header card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                @if($news->image)
                    <img src="{{ $news->image }}" alt="{{ $news->title }}" class="news-detail-image w-100">
                @endif
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-success">Tin tức</span>
                        <small class="text-muted">{{ $news->published_at?->format('d/m/Y') }}</small>
                    </div>
                    <h1 class="h2 fw-bold mb-3">{{ $news->title }}</h1>
                    @if($news->excerpt)
                        <p class="text-muted mb-0">{{ $news->excerpt }}</p>
                    @endif
                </div>
            </div>

            <article class="card border-0 shadow-sm rounded-4 p-4 bg-white news-article">
                {!! nl2br(e($news->content)) !!}
            </article>
        </div>
    </div>
</div>
@endsection
