@extends('admin.layouts.app')

@section('title', 'Sửa người dùng')
@section('page-title', 'Sửa thông tin người dùng')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Tên</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới (nếu cần)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Vai trò</label>
                    <select name="role" class="form-select" required>
                        @foreach($roles as $slug => $name)
                            <option value="{{ $slug }}" {{ old('role', $user->role) === $slug ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status', $user->status) ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Kích hoạt</label>
                </div>

                <button class="btn btn-primary">Lưu</button>
            </form>
        </div>
    </div>
@endsection
