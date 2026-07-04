@extends('admin.layouts.app')

@section('title', 'Vai trò')
@section('page-title', 'Quản lý vai trò')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Danh sách vai trò</h5>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Tạo vai trò mới</a>
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
                            <th>Vai trò</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->slug }}</td>
                                <td>{{ $role->description }}</td>
                                <td>{{ $role->status ? 'Hoạt động' : 'Không hoạt động' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-secondary">Sửa</a>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Xóa vai trò này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Xóa</button>
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
