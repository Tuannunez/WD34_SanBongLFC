@extends('layouts.app')

@section('title', 'Chi tiết thông báo')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Chi tiết thông báo</h3>
        <a href="{{ route('user.notifications.index') }}" class="btn btn-outline-secondary">Quay lại</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <h5 class="fw-bold">{{ $notification->title }}</h5>
            <div class="text-muted small mb-3">{{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}</div>
            <div class="mt-2">{!! nl2br(e($notification->content)) !!}</div>
        </div>
    </div>
</div>
@endsection
