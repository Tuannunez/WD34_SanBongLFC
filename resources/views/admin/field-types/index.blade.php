@extends('admin.layouts.app')

@section('title', 'Quản lý loại sân')
@section('page-title', 'Quản lý loại sân')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Danh sách loại sân</h2>
        <a href="{{ route('admin.field-types.create') }}" class="btn btn-success">Thêm loại sân</a>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-success text-center">
                    <tr>
                        <th width="60">#</th>
                        <th>Tên loại sân</th>
                        <th>Số người</th>
                        <th>Trạng thái</th>
                        <th width="180">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fieldTypes as $key => $fieldType)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $fieldType->name }}</td>
                            <td>{{ $fieldType->number_of_players }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $fieldType->status ? 'success' : 'secondary' }}">
                                    {{ $fieldType->status ? 'Hoạt động' : 'Tạm dừng' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.field-types.edit', $fieldType) }}" class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('admin.field-types.destroy', $fieldType) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-danger">Chưa có loại sân.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
