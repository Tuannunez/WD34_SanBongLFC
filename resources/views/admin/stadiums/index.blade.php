@extends('admin.layouts.app')

@section('title', 'Quản lý sân bóng')
@section('page-title', 'Quản lý sân bóng')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="fw-bold">
            Quản lý thông tin cơ sở sân bóng
        </h2>

        <a href="{{ route('admin.stadiums.create') }}" class="btn btn-success">
            Thêm cơ sở sân
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body p-0">

            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-success text-center">

                    <tr>

                        <th width="60">#</th>

                        <th width="120">Hình ảnh</th>

                        <th>Tên sân</th>

                        
                        <th>Giờ hoạt động</th>

                        <th>Khung Giờ Đặc Biệt</th>
                        <th width="180">Thao tác</th>

                    </tr>

                </thead>

                <tbody>

                @forelse($stadiums as $key => $stadium)

                    <tr>

                        <td class="text-center">
                            {{ $key + 1 }}
                        </td>

                        <td class="text-center">
                            @php
                                $imageUrl = $stadium->image
                                    ? (str_starts_with($stadium->image, 'http') ? $stadium->image : asset('storage/' . $stadium->image))
                                    : asset('images/logo.png');
                            @endphp
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $stadium->name }}"
                                 class="rounded border"
                                 style="width: 96px; height: 64px; object-fit: cover;"
                                 onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">
                        </td>

                        

                        <td >

                            <strong>{{ $stadium->name }}</strong>

                        </td>

                        

                        <td class="text-center">

                            {{ \Carbon\Carbon::parse($stadium->open_time)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($stadium->close_time)->format('H:i') }}

                        </td>

                       
                       

                        <td class="text-center">
                            @php
                                $specialCount = 0;
                                try {
                                    if (\Illuminate\Support\Facades\Schema::hasTable('stadium_time_slot_prices')) {
                                        $specialCount = \App\Models\StadiumTimeSlotPrice::where('stadium_id', $stadium->id)->count();
                                    }
                                } catch (\Throwable $e) {
                                    $specialCount = 0;
                                }
                            @endphp

                            <a href="{{ url('admin/stadiums/' . $stadium->id . '/prices') }}" class="btn btn-primary btn-sm mb-1">
                                Quản lý Giá ({{ $specialCount }})
                            </a>

                        </td>

                        <td class="text-center">

                            <a href="{{ route('admin.stadiums.show',$stadium->id) }}"
                               class="btn btn-info btn-sm me-1">
                                Chi tiết
                            </a>

                            <a href="{{ route('admin.stadiums.edit',$stadium->id) }}"
                               class="btn btn-warning btn-sm me-1">
                                Sửa
                            </a>

                            <form action="{{ route('admin.stadiums.destroy',$stadium->id) }}"
                                  method="POST"
                                  class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    onclick="return confirm('Bạn chắc chắn muốn xóa?')"
                                    class="btn btn-danger btn-sm">

                                    Xóa

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="10" class="text-center text-danger">

                            Chưa có dữ liệu sân bóng.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
