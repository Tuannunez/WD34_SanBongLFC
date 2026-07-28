@extends('admin.layouts.app')

@section('title', 'Thông báo')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1 fw-bold">Thông báo</h3>
            <p class="text-muted mb-0">Danh sách thông báo mới nhất từ hệ thống và người dùng.</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Tiêu đề</th>
                            <th scope="col">Nội dung</th>
                            <th scope="col">Người dùng</th>
                            <th scope="col">Loại</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>{{ $notification->id }}</td>
                                <td>{{ $notification->title }}</td>
                                <td>{{ Str::limit($notification->content, 80) }}</td>
                                <td>{{ $notification->user_name ?? 'Khách' }}</td>
                                <td>{{ ucfirst($notification->type ?? 'system') }}</td>
                                <td>
                                    @if($notification->is_read)
                                        <span class="badge bg-secondary">Đã đọc</span>
                                    @else
                                        <span class="badge bg-success">Mới</span>
                                    @endif
                                </td>
                                <td>{{ date('d/m/Y H:i', strtotime($notification->created_at)) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Chưa có thông báo nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
@endsection
