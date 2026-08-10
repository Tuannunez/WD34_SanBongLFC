@extends('admin.layouts.app')

@section('title', 'Quản lý khung giờ theo sân')
@section('page-title', 'Quản lý khung giờ theo sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold">Quản lý khung giờ và giá theo sân</h4>
                    <p class="text-muted mb-0">Thiết lập khung giờ đặt sân, giá cơ bản, và phụ phí cho từng cơ sở</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>

            <div class="alert alert-info mb-4" role="alert">
                <strong>📝 Hướng dẫn:</strong> 
                Bấm vào tên sân để quản lý khung giờ và giá. Bấm nút "⚙️ Cấu hình giá" để thiết lập giá cơ bản loại sân và phụ phí.
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Tên sân</th>
                            <th width="25%">Khung giờ cố định</th>
                            <th width="25%">Giá (VNĐ)</th>
                            <th width="20%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stadiums as $index => $stadium)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.time-slots.show', $stadium->id) }}" class="text-decoration-none fw-bold">
                                        {{ $stadium->name }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    @forelse($fixedSlots as $slot)
                                        <span class="badge bg-light text-dark me-1">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    @php
                                        $pricesForStadium = isset($slotPrices[$stadium->id]) ? $slotPrices[$stadium->id] : collect();
                                    @endphp
                                    @forelse($fixedSlots as $slot)
                                        @php
                                            $priceModel = $pricesForStadium->firstWhere('time_slot_id', $slot->id);
                                            $priceValue = $priceModel ? $priceModel->price : ($stadium->price ?? null);
                                        @endphp
                                        <div class="mb-1">
                                            {!! $priceValue !== null 
                                                ? '<span class="badge bg-success">' . number_format($priceValue, 0, ',', '.') . 'đ</span>' 
                                                : '<span class="badge bg-secondary">Chưa đặt</span>' 
                                            !!}
                                        </div>
                                    @empty
                                        <span class="text-muted">Chưa có khung giờ</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.time-slots.show', $stadium->id) }}" class="btn btn-primary btn-sm" title="Quản lý khung giờ và giá">
                                        ✏️ Quản lý
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Chưa có dữ liệu sân bóng. <a href="{{ route('admin.stadiums.index') }}">Hãy tạo sân trước</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-group {
        gap: 5px;
    }
    
    .table a {
        color: #0d6efd;
    }
    
    .table a:hover {
        text-decoration: underline;
    }
</style>
@endsection
