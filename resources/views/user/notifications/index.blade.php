@extends('layouts.app')

@section('title', 'Thông báo của tôi')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Thông báo của tôi</h3>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Quay về</a>
    </div>

    @if($notifications->isNotEmpty())
        <div class="list-group">
            @foreach($notifications as $n)
                <a href="{{ route('user.notifications.show', $n->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start @if(!$n->is_read) list-group-item-warning @endif">
                    <div>
                        <div class="fw-semibold">{{ $n->title }}</div>
                        <div class="small text-muted">{{ \Carbon\Carbon::parse($n->created_at)->format('d/m/Y H:i') }}</div>
                        <div class="mt-2 text-muted">{{ \Illuminate\Support\Str::limit($n->content, 180) }}</div>
                    </div>
                    <div class="text-end">
                        @if(!$n->is_read)
                            <span class="badge bg-danger">Mới</span>
                        @endif
                        <i class="bi bi-chevron-right ms-2"></i>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-3">{{ $notifications->links() }}</div>
    @else
        <div class="alert alert-light">Bạn chưa có thông báo nào.</div>
    @endif
</div>
@endsection
