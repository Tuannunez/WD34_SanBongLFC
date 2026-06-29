@extends('admin.layouts.app')

@section('title', 'Người dùng')
@section('page-title', 'Quản lý người dùng')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Danh sách người dùng</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tạo người dùng mới</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>{{ $user->status ? 'Kích hoạt' : 'Khóa' }}</td>
                                <td>{{ $user->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-secondary">Sửa</a>
                                    <a href="{{ route('admin.users.toggle-status', $user) }}" class="btn btn-sm {{ $user->status ? 'btn-warning' : 'btn-success' }}">
                                        {{ $user->status ? 'Khóa' : 'Mở' }}
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa người dùng này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
