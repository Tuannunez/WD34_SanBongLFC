@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-5">
            <h1 class="mb-3">{{ $news->title }}</h1>
            <p class="text-muted">{{ $news->published_at?->format('Y-m-d') }}</p>
            @if($news->image)
                <img src="{{ $news->image }}" class="img-fluid mb-3" alt="">
            @endif
            <div class="content">{!! nl2br(e($news->content)) !!}</div>
        </div>
    </div>
</div>
@endsection
