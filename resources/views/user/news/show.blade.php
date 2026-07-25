@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="news-detail-card card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="badge bg-success">Tin tức</span>
                        <small class="text-muted">{{ $news->published_at?->format('d/m/Y') }}</small>
                    </div>
                    <h1 class="h3 fw-bold mb-3">{{ $news->title }}</h1>
                    @if($news->excerpt)
                        <p class="text-secondary mb-0">{{ $news->excerpt }}</p>
                    @endif
                </div>
            </div>

            @if($news->image)
                <figure class="news-detail-figure card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#newsImageModal" class="news-detail-link">
                        <img src="{{ $news->image }}" alt="{{ $news->title }}" class="news-detail-figure-image">
                        <span class="news-detail-link-label">Xem ảnh đầy đủ</span>
                    </a>
                </figure>
            @endif

            <article class="card border-0 shadow-sm rounded-4 p-4 bg-white news-article">
                {!! nl2br(e($news->content)) !!}
            </article>

            @if($news->image)
                <div class="modal fade" id="newsImageModal" tabindex="-1" aria-labelledby="newsImageModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen-sm-down modal-dialog-centered">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-header border-0 p-3">
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0 d-flex justify-content-center align-items-center">
                                <img src="{{ $news->image }}" alt="{{ $news->title }}" class="w-100 news-detail-modal-image">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
