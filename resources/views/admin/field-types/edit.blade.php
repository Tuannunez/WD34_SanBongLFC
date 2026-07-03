@extends('admin.layouts.app')

@section('title', 'Sửa loại sân')
@section('page-title', 'Sửa loại sân')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Sửa loại sân</h4>
            <div class="text-muted">Cập nhật thông tin loại sân trong hệ thống</div>
        </div>

        <a href="{{ route('admin.field-types.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4 shadow-sm">
            <div class="fw-bold mb-1">
                <i class="bi bi-exclamation-circle me-1"></i>
                Dữ liệu chưa hợp lệ
            </div>

            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="page-card p-4 h-100">
                <div class="rounded-4 bg-warning-subtle text-warning d-flex align-items-center justify-content-center mb-3"
                     style="width: 58px; height: 58px;">
                    <i class="bi bi-grid-3x3-gap-fill fs-3"></i>
                </div>

                <h5 class="fw-bold mb-2">{{ $fieldType->name }}</h5>

                <div class="mb-3">
                    @if($fieldType->number_of_players)
                        <span class="badge bg-info-subtle text-info">
                            <i class="bi bi-people me-1"></i>
                            {{ $fieldType->number_of_players }} người
                        </span>
                    @endif

                    @if($fieldType->status)
                        <span class="badge bg-success-subtle text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Hoạt động
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="bi bi-pause-circle me-1"></i>
                            Vô hiệu
                        </span>
                    @endif
                </div>

                <p class="text-muted">
                    Bạn đang chỉnh sửa loại sân này. Hãy kiểm tra kỹ thông tin trước khi lưu thay đổi.
                </p>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ID</span>
                        <span class="fw-bold">#{{ $fieldType->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Số người</span>
                        <span class="fw-bold">
                            {{ $fieldType->number_of_players ? $fieldType->number_of_players . ' người' : 'Chưa cập nhật' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>
                        Cập nhật thông tin loại sân
                    </h5>
                    <div class="text-muted small">Chỉnh sửa thông tin và bấm lưu thay đổi</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.field-types.update', $fieldType) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tên loại sân <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $fieldType->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Nhập tên loại sân"
                                       required>

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số người chơi</label>

                                <input type="number"
                                       name="number_of_players"
                                       value="{{ old('number_of_players', $fieldType->number_of_players) }}"
                                       class="form-control @error('number_of_players') is-invalid @enderror"
                                       placeholder="Ví dụ: 5, 7, 11"
                                       min="1">

                                @error('number_of_players')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Mô tả</label>

                                <textarea name="description"
                                          rows="4"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Nhập mô tả ngắn về loại sân...">{{ old('description', $fieldType->description) }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="p-3 rounded-4 bg-light border mt-4 mb-4">
                            <div class="form-check form-switch mb-0">
                                <input type="checkbox"
                                       name="status"
                                       value="1"
                                       class="form-check-input"
                                       id="status"
                                       {{ old('status', $fieldType->status) ? 'checked' : '' }}>

                                <label class="form-check-label fw-semibold" for="status">
                                    Kích hoạt loại sân
                                </label>
                            </div>

                            <div class="text-muted small mt-1">
                                Nếu tắt trạng thái này, loại sân sẽ được xem là vô hiệu.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.field-types.index') }}" class="btn btn-light">
                                Hủy
                            </a>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i>
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection