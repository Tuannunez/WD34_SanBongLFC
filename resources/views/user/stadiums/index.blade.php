@extends('layouts.home')

@section('content')

<main class="content">

    <div class="section-head">
        <h2>Sân nổi bật</h2>

        <a class="see-more" href="#">
            Xem tất cả →
        </a>
    </div>

    <div class="cards">

        @forelse($stadiums as $stadium)

            <div class="pitch-card">

                <img
                    src="{{ $stadium->image }}"
                    alt="{{ $stadium->name }}"
                    style="width:100%;height:180px;object-fit:cover;"
                >

                <div class="pitch-body">

                    <div class="pitch-title">
                        {{ $stadium->name }}
                    </div>

                    <div class="pitch-meta">
                        📍 {{ $stadium->address }}
                    </div>

                    <div class="pitch-meta">
                        🕒 {{ $stadium->open_time }}
                        -
                        {{ $stadium->close_time }}
                    </div>

                    <div class="pitch-footer">
                        <span>⭐ {{ $stadium->rating }}</span>

                        <span>
                            {{ number_format($stadium->price) }}đ/giờ
                        </span>
                    </div>

                    {{-- <a href="{{ route('stadiums.show', $stadium->id) }}"
                       class="btn btn-success mt-2">
                        Xem chi tiết
                    </a> --}}

                </div>

            </div>

        @empty

            <div class="alert alert-warning">
                Không có sân bóng nào.
            </div>

        @endforelse

    </div>

</main>

@endsection