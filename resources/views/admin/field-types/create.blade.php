@extends('admin.layouts.app')

@section('title', 'Thêm loại sân')
@section('page-title', 'Thêm loại sân')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Thêm loại sân</h4>
            <div class="text-muted">Tạo loại sân mới để phân loại sân bóng trong hệ thống</div>
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
                <div class="rounded-4 bg-primary-subtle text-primary d-flex align-items-center justify-content-center mb-3"
                     style="width: 58px; height: 58px;">
                    <i class="bi bi-grid-3x3-gap fs-3"></i>
                </div>

                <h5 class="fw-bold mb-2">Thông tin loại sân</h5>

                <p class="text-muted mb-4">
                    Loại sân giúp phân loại sân theo số người chơi như sân 5, sân 7 hoặc sân 11.
                </p>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Tên loại sân nên ngắn gọn, dễ hiểu.</span>
                </div>

                <div class="d-flex align-items-start gap-2 mb-3">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Số người chơi giúp người dùng dễ chọn sân phù hợp.</span>
                </div>

                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                    <span class="text-muted">Có thể vô hiệu nếu tạm thời chưa sử dụng loại sân này.</span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="page-card">
                <div class="card-header bg-white border-0 px-4 py-4">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-primary me-1"></i>
                        Nhập thông tin loại sân
                    </h5>
                    <div class="text-muted small">Các trường có dấu * là bắt buộc</div>
                </div>

                <div class="card-body px-4 pb-4">
                    <form action="{{ route('admin.field-types.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Tên loại sân <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Ví dụ: Sân 5 người, Sân 7 người..."
                                       required>

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số người chơi</label>

                                <input type="number"
                                       name="number_of_players"
                                       value="{{ old('number_of_players') }}"
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
                                          placeholder="Nhập mô tả ngắn về loại sân...">{{ old('description') }}</textarea>

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
                                       {{ old('status', true) ? 'checked' : '' }}>

                                <label class="form-check-label fw-semibold" for="status">
                                    Kích hoạt loại sân
                                </label>
                            </div>

                            <div class="text-muted small mt-1">
                                Loại sân được kích hoạt sẽ có thể hiển thị và sử dụng trong hệ thống.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.field-types.index') }}" class="btn btn-light">
                                Hủy
                            </a>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>
                                Lưu loại sân
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection