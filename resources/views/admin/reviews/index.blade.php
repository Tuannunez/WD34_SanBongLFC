@extends('admin.layouts.app')

@section('title', 'Đánh giá')
@section('page-title', 'Quản lý đánh giá')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Quản lý đánh giá</h3>
            <p class="text-muted mb-0">Danh sách đánh giá từ khách hàng của các sân bóng.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="60">#</th>
                            <th>Người đánh giá</th>
                            <th>Sân</th>
                            <th>Điểm</th>
                            <th>Nội dung</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th width="140">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="text-center">{{ $review->id }}</td>
                                <td>
                                    <strong>{{ $review->user?->name ?? 'Khách' }}</strong>
                                    <div class="text-muted small">{{ $review->user?->email ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $review->field?->name ?? '-' }}</div>
                                    <div class="text-muted small">{{ $review->field?->stadium?->name ?? '-' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2">{{ $review->rating }}</span>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($review->comment, 120) }}</td>
                                <td class="text-center">
                                    @if($review->status)
                                        <span class="badge bg-success-subtle text-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Ẩn</span>
                                    @endif
                                </td>
                                <td>{{ $review->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Hiện chưa có đánh giá nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($reviews, 'links'))
            <div class="card-footer bg-white border-0 py-3">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
