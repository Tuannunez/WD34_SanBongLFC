@extends('admin.layouts.app')

@section('title', 'Chi tiết loại sân')
@section('page-title', 'Chi tiết loại sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-12">
                    <h3>{{ $fieldType->name }}</h3>
                    <p><strong>Số người:</strong> {{ $fieldType->number_of_players ?? '-' }}</p>
                    <p><strong>Mô tả:</strong> {{ $fieldType->description ?? '-' }}</p>
                    <p><strong>Trạng thái:</strong> {{ $fieldType->status ? 'Hoạt động' : 'Vô hiệu' }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $fieldType->created_at?->format('Y-m-d H:i') }}</p>
                    <p><strong>Cập nhật:</strong> {{ $fieldType->updated_at?->format('Y-m-d H:i') }}</p>

                    <a href="{{ route('admin.field-types.index') }}" class="btn btn-secondary">Quay lại</a>
                    <a href="{{ route('admin.field-types.edit', $fieldType) }}" class="btn btn-warning">Sửa</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
