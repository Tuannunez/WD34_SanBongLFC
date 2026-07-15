@extends('admin.layouts.app')

@section('title', 'Quản lý khung giờ theo sân')
@section('page-title', 'Quản lý khung giờ theo sân')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold">Khung giờ đặt sân theo từng cơ sở</h4>
                    <p class="text-muted mb-0">Hiển thị tên sân, giá khung giờ cố định và giá khung giờ đặc biệt.</p>
                </div>
                <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Quay lại danh sách sân</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Tên sân</th>
                            <th>Giờ đặt sân cố định</th>
                            <th>Giá cố định (VNĐ)</th>
                            <th>Giá khung giờ đặc biệt</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stadiums as $index => $stadium)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $stadium->name }}</td>
                                <td class="text-center">
                                    @foreach($fixedSlots as $slot)
                                        <div>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    @php
                                        $pricesForStadium = isset($slotPrices[$stadium->id]) ? $slotPrices[$stadium->id] : collect();
                                    @endphp
                                    @foreach($fixedSlots as $slot)
                                        @php
                                            $priceModel = $pricesForStadium->firstWhere('time_slot_id', $slot->id);
                                            $priceValue = $priceModel ? $priceModel->price : ($stadium->price ?? null);
                                        @endphp
                                        <div class="mb-1">{!! $priceValue !== null ? '<span class="text-success">' . number_format($priceValue, 0, ',', '.') . 'đ</span>' : '<span class="text-muted">-</span>' !!}</div>
                                    @endforeach
                                </td>
                                <td>
                                    @if(isset($specialSlots[$stadium->id]) && $specialSlots[$stadium->id]->count())
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                @foreach($specialSlots[$stadium->id] as $special)
                                                    <tr>
                                                        <td class="p-1">{{ \Carbon\Carbon::parse($special->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($special->end_time)->format('H:i') }}</td>
                                                        <td class="p-1 text-end text-success">{{ number_format($special->price, 0, ',', '.') }}đ</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-muted">Không có khung giờ đặc biệt</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.time-slots.show', $stadium->id) }}" class="btn btn-primary btn-sm">Thêm khung giờ</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Chưa có dữ liệu sân bóng.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
