@extends('admin.layouts.app')

@section('title', 'Quản lý sân con')
@section('page-title', 'Quản lý sân con')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Danh sách sân con</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.fields.store') }}" method="POST" class="row g-3 mb-4">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Cơ sở sân</label>
                    <select name="stadium_id" class="form-select" required>
                        <option value="">-- Chọn cơ sở --</option>
                        @foreach($stadiums as $stadium)
                            <option value="{{ $stadium->id }}">{{ $stadium->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tên sân con</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Sân 1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại sân</label>
                    <select name="field_type_id" class="form-select" required>
                        <option value="">-- Chọn loại sân --</option>
                        @foreach($fieldTypes as $fieldType)
                            <option value="{{ $fieldType->id }}">{{ $fieldType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1">Hoạt động</option>
                        <option value="0">Tạm khóa</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success">Thêm sân con</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên sân con</th>
                        <th>Cơ sở</th>
                        <th>Loại sân</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($fields as $field)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <form action="{{ route('admin.fields.update', $field->id) }}" method="POST" class="d-flex flex-column gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $field->name }}" required>
                            </td>
                            <td>
                                    <select name="stadium_id" class="form-select form-select-sm" required>
                                        @foreach($stadiums as $stadium)
                                            <option value="{{ $stadium->id }}" @selected($field->stadium_id == $stadium->id)>{{ $stadium->name }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td>
                                    <select name="field_type_id" class="form-select form-select-sm" required>
                                        @foreach($fieldTypes as $fieldType)
                                            <option value="{{ $fieldType->id }}" @selected($field->field_type_id == $fieldType->id)>{{ $fieldType->name }}</option>
                                        @endforeach
                                    </select>
                            </td>
                            <td>
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="1" @selected($field->status)>Hoạt động</option>
                                        <option value="0" @selected(!$field->status)>Tạm khóa</option>
                                    </select>
                            </td>
                            <td>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                                        <form action="{{ route('admin.fields.destroy', $field->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sân con này?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                        </form>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Chưa có sân con nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
