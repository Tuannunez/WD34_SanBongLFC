@extends('admin.layouts.app')

@section('title', 'Cơ sở sân bóng')
@section('page-title', 'Quản lý cơ sở sân bóng')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Quản lý cơ sở sân bóng</h4>
            <div class="text-muted">Danh sách các cơ sở sân bóng trong hệ thống SanBongLFC</div>
        </div>

        <a href="{{ route('admin.stadiums.create') }}" class="btn btn-primary px-4">
            <i class="bi bi-plus-circle me-1"></i>
            Thêm cơ sở sân
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        $stadiumCollection = collect(method_exists($stadiums, 'items') ? $stadiums->items() : $stadiums);
        $hasImageCount = $stadiumCollection->whereNotNull('image')->count();
        $hasEmailCount = $stadiumCollection->whereNotNull('email')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Tổng cơ sở</div>
                        <h4 class="fw-bold mb-0">
                            {{ method_exists($stadiums, 'total') ? $stadiums->total() : $stadiums->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-image"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Có hình ảnh</div>
                        <h4 class="fw-bold mb-0">{{ $hasImageCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Có email liên hệ</div>
                        <h4 class="fw-bold mb-0">{{ $hasEmailCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-card">
        <div class="card-header bg-white border-0 px-4 py-4">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-list-ul text-primary me-1"></i>
                    Danh sách cơ sở sân
                </h5>
                <div class="text-muted small">Quản lý thông tin cơ sở, địa chỉ, liên hệ và giờ hoạt động</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th style="width: 140px;">Hình ảnh</th>
                        <th>Cơ sở sân</th>
                        <th>Địa chỉ</th>
                        <th>Liên hệ</th>
                        <th style="width: 150px;">Giờ hoạt động</th>
                        <th style="width: 150px;">Loại sân</th>
                        <th class="text-end" style="width: 180px;">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($stadiums as $key => $stadium)
                        <tr>
                            <td class="fw-bold text-muted">
                                #{{ method_exists($stadiums, 'firstItem') ? $stadiums->firstItem() + $key : $key + 1 }}
                            </td>

                            <td>
                                <img src="{{ $stadium->image ? (str_starts_with($stadium->image, 'http') ? $stadium->image : asset('storage/' . $stadium->image)) : asset('images/logo.png') }}"
                                     alt="{{ $stadium->name }}"
                                     class="rounded-4 border"
                                     style="width: 110px; height: 74px; object-fit: cover;">
                            </td>

                            <td>
                                <div class="fw-bold mb-1">{{ $stadium->name }}</div>
                                <div class="text-muted small">
                                    {{ \Illuminate\Support\Str::limit($stadium->description, 55) ?: 'Chưa có mô tả' }}
                                </div>
                            </td>

                            <td>
                                <div class="text-muted">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i>
                                    {{ \Illuminate\Support\Str::limit($stadium->address, 60) }}
                                </div>
                            </td>

                            <td>
                                <div class="small mb-1">
                                    <i class="bi bi-telephone me-1 text-success"></i>
                                    {{ $stadium->phone ?: 'Chưa cập nhật' }}
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-envelope me-1 text-primary"></i>
                                    {{ $stadium->email ?: 'Chưa cập nhật' }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-clock me-1"></i>
                                    @if($stadium->open_time && $stadium->close_time)
                                        {{ \Carbon\Carbon::parse($stadium->open_time)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($stadium->close_time)->format('H:i') }}
                                    @else
                                        Chưa cập nhật
                                    @endif
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info">
                                    <i class="bi bi-grid-3x3-gap me-1"></i>
                                    {{ $stadium->fieldType?->name ?? 'Chưa chọn' }}
                                </span>
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.stadiums.show', $stadium->id) }}"
                                   class="btn btn-sm btn-outline-info"
                                   title="Chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.stadiums.edit', $stadium->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form action="{{ route('admin.stadiums.destroy', $stadium->id) }}"
                                      method="POST"
                                      class="d-inline-block"
                                      onsubmit="return confirm('Bạn chắc chắn muốn xóa cơ sở sân này?');">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Chưa có cơ sở sân bóng nào
                                </div>

                                <a href="{{ route('admin.stadiums.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Thêm cơ sở sân đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($stadiums, 'links'))
            <div class="card-footer bg-white border-0 px-4 py-3">
                {{ $stadiums->links() }}
            </div>
        @endif
    </div>
@endsection