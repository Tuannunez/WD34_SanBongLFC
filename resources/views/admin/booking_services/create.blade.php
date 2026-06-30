@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Thêm dịch vụ đặt sân</h3>
            <p class="text-muted mb-0">Gắn dịch vụ đi kèm vào đơn đặt sân</p>
        </div>

        <a href="{{ route('admin.booking-services.index') }}" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-semibold mb-0">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                Thông tin dịch vụ đặt sân
            </h5>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.booking-services.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Đơn đặt sân <span class="text-danger">*</span></label>
                        <select name="booking_id"
                                class="form-select rounded-3 @error('booking_id') is-invalid @enderror">
                            <option value="">-- Chọn đơn đặt sân --</option>

                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                    {{ $booking->booking_code ?? 'Đơn #' . $booking->id }}
                                    - {{ $booking->customer_name ?? 'Khách hàng' }}
                                </option>
                            @endforeach
                        </select>

                        @error('booking_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Dịch vụ <span class="text-danger">*</span></label>
                        <select name="service_id"
                                class="form-select rounded-3 @error('service_id') is-invalid @enderror">
                            <option value="">-- Chọn dịch vụ --</option>

                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                    - {{ number_format($service->price, 0, ',', '.') }}đ
                                    / {{ $service->unit }}
                                </option>
                            @endforeach
                        </select>

                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số lượng <span class="text-danger">*</span></label>
                        <input type="number"
                               name="quantity"
                               class="form-control rounded-3 @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', 1) }}"
                               min="1">

                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-save me-1"></i> Lưu
                    </button>

                    <a href="{{ route('admin.booking-services.index') }}" class="btn btn-light border rounded-3 px-4">
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection