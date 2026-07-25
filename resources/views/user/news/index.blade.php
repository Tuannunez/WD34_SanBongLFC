@extends('layouts.app')

@section('title', 'Tin tức')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Tin tức</h2>

    <div class="row g-4">
        @foreach($news as $item)
            <div class="col-md-6">
                <div class="card h-100">
                    @if($item->image)
                        <img src="{{ $item->image }}" class="card-img-top" alt="">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="text-muted">{{ $item->published_at?->format('Y-m-d') }}</p>
                        <p class="card-text">{{ $item->excerpt }}</p>
                        <a href="{{ route('news.show', $item->id) }}" class="stretched-link">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $news->links() }}
    </div>
</div>
@endsection
