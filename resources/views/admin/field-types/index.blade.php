@extends('admin.layouts.app')

@section('title', 'Loại sân')
@section('page-title', 'Quản lý loại sân')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Danh sách loại sân</h2>
        <a href="{{ route('admin.field-types.create') }}" class="btn btn-success">Thêm loại sân</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-success text-center">
                    <tr>
                        <th>#</th>
                        <th>Tên loại</th>
                        <th>Số người</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fieldTypes as $key => $fieldType)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $fieldType->name }}</td>
                            <td class="text-center">{{ $fieldType->number_of_players ?? '-' }}</td>
                            <td>{{ $fieldType->description }}</td>
                            <td class="text-center">{{ $fieldType->status ? 'Hoạt động' : 'Vô hiệu' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.field-types.edit', $fieldType) }}" class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('admin.field-types.destroy', $fieldType) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa loại sân này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">Chưa có loại sân nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
