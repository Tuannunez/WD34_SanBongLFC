@extends('layouts.home')

@section('content')

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* 1 Ảnh duy nhất bo góc chuẩn giao diện mới */
    .stadium-single-image {
        width: 100%;
        height: 380px;
        object-fit: cover;
        border-radius: 16px;
    }

    /* Phong cách Card nhẹ nhàng */
    .custom-card {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    
    .badge-type {
        background-color: #e6f7ed;
        color: #198754;
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .facility-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .facility-tag {
        background: #f8f9fa;
        border: 1px solid #f1f3f5;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.85rem;
        color: #212529;
    }

    /* Bảng giá / Nút khung giờ (Chia 2 cột cân đối) */
    .price-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 15px;
    }
    .time-slot-btn {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
        width: calc(50% - 6px);
    }
    .time-slot-btn:hover {
        border-color: #198754;
        background: #ffffff;
    }
    .time-slot-btn.active {
        background-color: #e6f7ed;
        border-color: #198754;
    }

    /* Giao diện Lịch chuẩn Việt Nam (T2 -> CN) */
    .calendar-container {
        max-width: 100%;
    }
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        text-align: center;
    }
    .calendar-day-name {
        font-weight: 600;
        font-size: 0.85rem;
        color: #6c757d;
        padding-bottom: 8px;
    }
    .calendar-day-name.sunday {
        color: #dc3545;
    }
    .calendar-day {
        padding: 12px 0;
        border-radius: 10px;
        font-size: 0.9rem;
        color: #212529;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        background: #fdfdfd;
        border: 1px solid #f1f3f5;
    }
    .calendar-day:hover:not(.empty) {
        background-color: #e9ecef;
        border-color: #ced4da;
    }
    .calendar-day.active {
        background-color: #198754 !important;
        color: #ffffff !important;
        font-weight: bold;
        border-color: #198754 !important;
    }
    .calendar-day.empty {
        cursor: default;
        background: transparent;
        border: none;
    }

    /* Sidebar Đặt sân */
    .booking-sidebar {
        position: sticky;
        top: 20px;
    }
    .info-row-gray {
        background: #f8f9fa;
        padding: 14px 16px;
        border-radius: 12px;
        margin-bottom: 15px;
        font-size: 0.9rem;
    }
    .btn-booking {
        padding: 14px;
        font-weight: bold;
        border-radius: 12px;
        transition: all 0.2s;
        border: none;
    }
</style>

<div class="container py-4">

    {{-- 1ẢNH DUY NHẤT TRÊN CÙNG --}}
    <div class="mb-4">
        <img src="{{ $stadium->image }}" alt="{{ $stadium->name }}" class="stadium-single-image">
    </div>

    <div class="row g-4">
        
        {{-- CỘT BÊN TRÁI: THÔNG TIN CHI TIẾT --}}
        <div class="col-lg-8">

            <div class="custom-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="fw-bold mb-2">{{ $stadium->name }}</h2>
                        <div class="text-muted small d-flex align-items-center gap-3">
                            <span>⭐ <strong class="text-dark">{{ $stadium->rating }}</strong> (128 đánh giá)</span>
                            <span>📍 {{ $stadium->address }}</span>
                        </div>
                    </div>
                    <span class="badge-type">Sân 7 người</span>
                </div>

                <div class="facility-inline mt-4">
                    <div class="facility-tag">🚿 Phòng thay đồ</div>
                    <div class="facility-tag">🚻 Nhà vệ sinh</div>
                    <div class="facility-tag">👕 Áo đấu</div>
                    <div class="facility-tag">🥤 Nước uống</div>
                    <div class="facility-tag">🚗 Bãi đỗ xe</div>
                    <div class="facility-tag">💡 Đèn chiếu sáng</div>
                </div>
            </div>

            <div class="custom-card">
                <h5 class="fw-bold mb-3">Mô tả sân</h5>
                <p class="text-muted mb-0">{{ $stadium->description ?? 'Chưa có mô tả chi tiết cho sân bóng này.' }}</p>
            </div>

            <div class="custom-card">
                <h5 class="fw-bold mb-1" id="price_title">Bảng giá hôm nay</h5>
                <span class="text-warning small fw-bold">● CHỌN KHUNG GIỜ TRỐNG ĐỂ ĐẶT</span>
                
                <div class="price-grid">
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('06:00 - 07:30', 150000, this)">
                        <span class="text-muted">🕒 06:00 - 07:30</span>
                        <strong class="text-success">150.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('07:30 - 09:00', 180000, this)">
                        <span class="text-muted">🕒 07:30 - 09:00</span>
                        <strong class="text-success">180.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('09:00 - 10:30', 200000, this)">
                        <span class="text-muted">🕒 09:00 - 10:30</span>
                        <strong class="text-success">200.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('12:00 - 13:30', 220000, this)">
                        <span class="text-muted">🕒 12:00 - 13:30</span>
                        <strong class="text-success">220.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('13:30 - 15:00', 250000, this)">
                        <span class="text-muted">🕒 13:30 - 15:00</span>
                        <strong class="text-success">250.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('15:00 - 16:30', 280000, this)">
                        <span class="text-muted">🕒 15:00 - 16:30</span>
                        <strong class="text-success">280.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('16:30 - 18:00', 300000, this)">
                        <span class="text-muted">🕒 16:30 - 18:00</span>
                        <strong class="text-success">300.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('18:00 - 19:30', 350000, this)">
                        <span class="text-muted">🕒 18:00 - 19:30</span>
                        <strong class="text-success">350.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('19:30 - 21:00', 400000, this)">
                        <span class="text-muted">🕒 19:30 - 21:00</span>
                        <strong class="text-success">400.000đ</strong>
                    </button>
                    <button type="button" class="time-slot-btn" onclick="selectTimeSlot('21:00 - 22:30', 320000, this)">
                        <span class="text-muted">🕒 21:00 - 22:30</span>
                        <strong class="text-success">320.000đ</strong>
                    </button>
                </div>
            </div>

            <div class="custom-card">
                <h5 class="fw-bold mb-4">Chọn lịch đặt sân</h5>
                <div class="calendar-container">
                    <div class="calendar-header">
                        <button type="button" class="btn btn-sm btn-light fw-bold" onclick="changeMonth(-1)">&lt;</button>
                        <h6 class="fw-bold mb-0" id="calendar_month_year">Tháng 1, 2026</h6>
                        <button type="button" class="btn btn-sm btn-light fw-bold" onclick="changeMonth(1)">&gt;</button>
                    </div>
                    
                    <div class="calendar-grid" id="calendar_days_grid">
                        </div>
                </div>
            </div>

        </div>

        {{-- CỘT BÊN PHẢI: CARD ĐẶT SÂN --}}
        <div class="col-lg-4">
            <form action="{{ route('user.bookings.store.from-stadium', $stadium->id) }}" method="POST" class="custom-card booking-sidebar">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <input type="hidden" name="stadium_id" value="{{ $stadium->id }}">
                <input type="hidden" name="booking_date" id="input_booking_date" value="">
                <input type="hidden" name="time_slot" id="input_time_slot" value="">
                <input type="hidden" name="total_price" id="input_total_price" value="0">

                <div class="mb-4">
                    <span class="fs-3 fw-bold text-success" id="main_price_display">{{ number_format($stadium->price ?? 150000) }}</span>
                    <span class="fs-3 fw-bold text-success">đ</span>
                    <span class="text-muted small">/ giờ</span>
                    <div class="text-muted small" style="font-size: 0.8rem; margin-top: -4px;">Giá từ</div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small fw-bold mb-1">📅 Ngày đã chọn</label>
                    <div class="info-row-gray fw-bold text-dark d-flex align-items-center" id="display_date">
                        Chưa chọn ngày đặt
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-muted small fw-bold mb-1">🕒 Khung giờ đã chọn</label>
                    <div class="info-row-gray text-muted d-flex align-items-center" id="display_time_slot">
                        Chưa chọn khung giờ nào
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold text-secondary">Tổng cộng</span>
                    <span class="fs-4 fw-bold text-dark" id="display_total_price">0đ</span>
                </div>

                <button type="submit" id="btn_submit_booking" class="btn btn-secondary btn-booking w-100 mb-2" disabled>
                    Đặt sân ngay
                </button>
                <p class="text-center text-muted small mb-0" style="font-size: 0.75rem;">Bạn chưa bị trừ tiền ở bước này</p>
            </form>
        </div>

    </div>
</div>

{{-- LOGIC CHỌN LỊCH VÀ XỬ LÝ KHUNG GIỜ --}}
<script>
    let currentYear = 2026;
    let currentMonth = 0; // Mặc định hiển thị Tháng 1 theo ảnh mẫu của bạn
    
    const dayNames = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
    const vietnameseDays = ['Chủ Nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];

    function renderCalendar(month, year) {
        const grid = document.getElementById('calendar_days_grid');
        grid.innerHTML = ''; 

        // 1. Render Header tuần từ Thứ 2 đến Chủ Nhật
        dayNames.forEach((day, index) => {
            const dayNameEl = document.createElement('div');
            dayNameEl.classList.add('calendar-day-name');
            if(index === 6) dayNameEl.classList.add('sunday'); 
            dayNameEl.innerText = day;
            grid.appendChild(dayNameEl);
        });

        document.getElementById('calendar_month_year').innerText = `Tháng ${month + 1}, ${year}`;

        // Xác định vị trí ô trống đầu tháng
        let firstDayIndex = new Date(year, month, 1).getDay(); 
        let blankSpaces = firstDayIndex === 0 ? 6 : firstDayIndex - 1;
        let totalDays = new Date(year, month + 1, 0).getDate();

        // 2. Điền các ô trống
        for (let i = 0; i < blankSpaces; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('calendar-day', 'empty');
            grid.appendChild(emptyCell);
        }

        // 3. Điền các ô số ngày
        for (let day = 1; day <= totalDays; day++) {
            const dayCell = document.createElement('div');
            dayCell.classList.add('calendar-day');
            dayCell.innerText = day;

            let specificDate = new Date(year, month, day);
            let dayOfWeekText = vietnameseDays[specificDate.getDay()];
            let fullDateString = `${dayOfWeekText}, ${day}/${month + 1}/${year}`;

            dayCell.onclick = function() {
                document.querySelectorAll('.calendar-grid .calendar-day').forEach(el => el.classList.remove('active'));
                dayCell.classList.add('active');

                document.getElementById('display_date').innerText = fullDateString;
                document.getElementById('input_booking_date').value = fullDateString;
                document.getElementById('price_title').innerText = `Bảng giá - ${dayOfWeekText}`;
            };

            // Mặc định chọn sẵn ngày 29 như trong ảnh thiết kế của bạn
            if (day === 29 && month === 0 && year === 2026) {
                dayCell.classList.add('active');
                document.getElementById('display_date').innerText = fullDateString;
                document.getElementById('input_booking_date').value = fullDateString;
                document.getElementById('price_title').innerText = `Bảng giá - ${dayOfWeekText}`;
            }

            grid.appendChild(dayCell);
        }
    }

    function changeMonth(direction) {
        currentMonth += direction;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    }

    // Hàm xử lý chọn khung giờ từ bảng giá
    function selectTimeSlot(timeString, price, element) {
        document.querySelectorAll('.time-slot-btn').forEach(el => el.classList.remove('active'));
        element.classList.add('active');

        const displayTimeEl = document.getElementById('display_time_slot');
        displayTimeEl.innerText = timeString;
        displayTimeEl.classList.remove('text-muted');
        displayTimeEl.classList.add('fw-bold', 'text-dark');
        
        document.getElementById('display_total_price').innerText = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
        document.getElementById('main_price_display').innerText = new Intl.NumberFormat('vi-VN').format(price);

        document.getElementById('input_time_slot').value = timeString;
        document.getElementById('input_total_price').value = price;

        const submitBtn = document.getElementById('btn_submit_booking');
        submitBtn.removeAttribute('disabled');
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-success');
    }

    window.onload = function() {
        renderCalendar(currentMonth, currentYear);
    };
</script>

@endsection