@extends('admin.layouts.app')

@section('title', 'Tạo thông báo')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1 fw-bold">Gửi thông báo cho người dùng</h3>
            <p class="text-muted mb-0">Tạo thông báo mới để gửi đến khách hàng.</p>
        </div>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">Quay lại</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Gửi đến</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient" id="recipient_all" value="all" checked>
                        <label class="form-check-label" for="recipient_all">Tất cả khách hàng</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="recipient" id="recipient_user" value="user">
                        <label class="form-check-label" for="recipient_user">Người dùng cụ thể</label>
                    </div>
                </div>

                <div class="mb-3" id="user-select-box" style="display:none;">
                    <label for="user_id" class="form-label">Chọn người dùng</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">-- Chọn người dùng --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Loại thông báo</label>
                    <select class="form-select" id="type" name="type">
                        <option value="system">Hệ thống</option>
                        <option value="promotion">Khuyến mãi</option>
                        <option value="booking">Đặt sân</option>
                        <option value="payment">Thanh toán</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung</label>
                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="6" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Gửi thông báo</button>
            </form>
        </div>
    </div>

    <script>
        const recipientAll = document.getElementById('recipient_all');
        const recipientUser = document.getElementById('recipient_user');
        const userSelectBox = document.getElementById('user-select-box');

        function toggleUserSelect() {
            userSelectBox.style.display = recipientUser.checked ? 'block' : 'none';
        }

        recipientAll.addEventListener('change', toggleUserSelect);
        recipientUser.addEventListener('change', toggleUserSelect);
    </script>
@endsection
