@extends('admin.layouts.app')

@section('title','Dashboard')
@section('page-title','Dashboard')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
body {
    background: linear-gradient(180deg, #f8fafc 0%, #f3f6fb 100%);
    font-family: Inter, sans-serif;
}
.chart-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.chart-tab {
    padding: 9px 18px;
    border-radius: 10px;
    background: #f5f7fb;
    cursor: pointer;
    transition: .25s;
    font-weight: 600;
}
.chart-tab:hover,
.chart-tab.active {
    background: #047637;
    color: #fff;
    transform: translateY(-2px);
}
.chart-tabs {
    position: relative;
}
.custom-date-panel {
    display: none;
    position: absolute;
    top: 52px;
    right: 0;
    width: 360px;
    padding: 18px;
    background: #ffffff;
    border: 1px solid #e7edf6;
    border-radius: 22px;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
    z-index: 10;
}
.custom-date-panel.visible {
    display: block;
}
.custom-date-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
}
.custom-date-panel-title {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 4px;
    color: #102a43;
}
.custom-date-panel-subtitle {
    font-size: 12px;
    color: #627d98;
}
.custom-date-nav {
    display: flex;
    align-items: center;
    gap: 8px;
}
.custom-date-nav button {
    width: 34px;
    height: 34px;
    border: 1px solid #e7edf6;
    border-radius: 12px;
    background: #f8fafc;
    color: #1e3a8a;
    font-size: 18px;
    line-height: 1;
    transition: background .2s ease;
    cursor: pointer;
}
.custom-date-nav button:hover {
    background: #eef4ff;
}
.custom-date-calendar {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 14px;
}
.custom-date-weekday {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: #627d98;
    text-transform: uppercase;
}
.custom-date-day {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    border: 1px solid transparent;
    background: #f8fafc;
    color: #102a43;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .2s ease;
}
.custom-date-day:hover {
    background: #e2efff;
    transform: translateY(-1px);
}
.custom-date-day.is-muted {
    background: transparent;
    color: #cbd5e1;
    cursor: default;
    transform: none;
}
.custom-date-day.is-selected {
    background: linear-gradient(135deg, #047637, #035F2C);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 12px 28px rgba(4, 118, 55, .18);
}
.custom-date-day.is-range {
    background: #dbeafe;
    color: #102a43;
}
.custom-date-summary {
    padding: 12px 14px;
    border-radius: 16px;
    background: #f0f6ff;
    color: #102a43;
    font-size: 13px;
    margin-bottom: 12px;
}
.custom-date-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.custom-date-footer button {
    min-width: 110px;
}
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.stat-card {
    border: 0;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
    transition: .3s;
    overflow: hidden;
    position: relative;
}
.stat-card:hover {
    transform: translateY(-6px);
}
.stat-card .card-body {
    padding: 22px;
}
.stat-title {
    font-size: 14px;
    color: #6c757d;
}
.stat-value {
    font-size: 34px;
    font-weight: 800;
}
.stat-growth {
    color: #2fb344;
    font-size: 13px;
    font-weight: 700;
}
.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 24px;
}
.bg-blue { background: #047637; }
.bg-green { background: #047637; }
.bg-orange { background: #F59E0B; }
.bg-red { background: #d63939; }
.sparkline { height: 50px; margin-top: 15px; }
.dashboard-card {
    border: 0;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
    position: relative;
    margin-bottom: 30px;
}
.dashboard-card .card-header {
    padding: 22px 24px;
    background: white;
    font-size: 17px;
    font-weight: 700;
    border-bottom: 1px solid #eef2f7;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dashboard-card .card-body {
    padding: 25px;
}
.table td { vertical-align: middle; }
.progress { height: 8px; border-radius: 20px; }
.dark-mode { background: #111827; color: white; }
.dark-mode .card { background: #1e293b; color: white; }
.dark-mode .dashboard-card .card-header { background: #1e293b; }
.dark-mode table { color: white; }
.dark-mode .table-light { background: #243041; }
.dark-mode .btn-light { background: #334155; color: white; border: 0; }
.avatar-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #206bc4;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}
</style>
@endpush

@section('content')

<div class="dashboard-header">
    <div>
        <button class="btn btn-light" id="themeToggle">
            <i class="bi bi-moon"></i>
        </button>
        <button class="btn btn-light dashboard-btn ms-2">
            Refresh
        </button>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-title">Tổng sân</div>
                        <div class="stat-value">{{ $totalFields ?? 0 }}</div>
                        <div class="stat-growth"><i class="bi bi-arrow-up"></i> 12%</div>
                    </div>
                    <div class="stat-icon bg-blue"><i class="bi bi-grid"></i></div>
                </div>
                <div id="spark1" class="sparkline"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-title">Đơn hôm nay</div>
                        <div class="stat-value">{{ $todayBookings ?? 0 }}</div>
                        @php $revGrowth = $growth['revenue'] ?? 0; @endphp
                        <div class="stat-growth {{ $revGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="bi bi-arrow-{{ $revGrowth >= 0 ? 'up' : 'down' }}"></i> {{ abs($revGrowth) }}%
                        </div>
                    </div>
                    <div class="stat-icon bg-green"><i class="bi bi-calendar-check"></i></div>
                </div>
                <div id="spark2" class="sparkline"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-title">Doanh thu</div>
                        <div class="stat-value">{{ number_format($monthlyRevenue ?? 0,0,',','.') }} đ</div>
                        <div class="stat-growth">
                            @if($revGrowth >= 0)
                            <i class="bi bi-arrow-up"></i> {{ $revGrowth }}%
                            @else
                            <i class="bi bi-arrow-down"></i> {{ abs($revGrowth) }}%
                            @endif
                        </div>
                    </div>
                    <div class="stat-icon bg-orange"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div id="spark3" class="sparkline"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-title">Khách hàng</div>
                        <div class="stat-value">{{ $totalCustomers ?? 0 }}</div>
                        <div class="stat-growth"><i class="bi bi-arrow-up"></i> 9%</div>
                    </div>
                    <div class="stat-icon bg-red"><i class="bi bi-people"></i></div>
                </div>
                <div id="spark4" class="sparkline"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    {{-- Revenue Chart --}}
    <div class="col-lg-8">
        <div class="card dashboard-card h-100">
            <div class="card-header">
                <div>
                    <div class="fw-bold fs-5">Phân tích doanh thu</div>
                    <small class="text-muted">Thống kê doanh thu năm {{ now()->year }}</small>
                </div>
                <div class="chart-tabs">
                    <div class="chart-tab active" data-type="30">30 ngày</div>
                    <div class="chart-tab" data-type="7">7 ngày</div>
                    <div class="chart-tab" data-type="custom">Tùy chỉnh</div>
                    <div class="chart-tab" data-quarter="1">Quý 1</div>
                    <div class="chart-tab" data-quarter="2">Quý 2</div>
                    <div class="chart-tab" data-quarter="3">Quý 3</div>
                    <div class="custom-date-panel" id="customDatePanel">
                        <div class="custom-date-panel-header">
                            <div>
                                <div class="custom-date-panel-title">Chọn khoảng ngày</div>
                                <div class="custom-date-panel-subtitle">Chọn ngày đầu và ngày cuối để lọc doanh thu</div>
                            </div>
                            <div class="custom-date-nav">
                                <button type="button" id="prevMonthBtn" aria-label="Tháng trước">‹</button>
                                <button type="button" id="nextMonthBtn" aria-label="Tháng sau">›</button>
                            </div>
                        </div>
                        <div class="custom-date-calendar" id="customDateCalendar"></div>
                        <div class="custom-date-summary" id="customDateSummary">Chưa chọn khoảng ngày</div>
                        <div class="custom-date-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="resetCustomDate">Xóa</button>
                            <button type="button" class="btn btn-primary btn-sm" id="applyCustomDate">Áp dụng</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="revenueChart" style="height:360px"></div>
            </div>
        </div>
    </div>

    {{-- Booking Status --}}
    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header">Booking Overview</div>
            <div class="card-body">
                <div id="bookingChart" style="height:280px"></div>
                <hr>
                @php
                    $bookingStatusChart = $bookingStatusChart ?? [0,0,0];
                    $totalBooking = array_sum($bookingStatusChart);
                    $confirmed = $totalBooking ? round(($bookingStatusChart[0] / $totalBooking) * 100) : 0;
                    $pending = $totalBooking ? round(($bookingStatusChart[1] / $totalBooking) * 100) : 0;
                    $cancelled = $totalBooking ? round(($bookingStatusChart[2] / $totalBooking) * 100) : 0;
                @endphp
                <div class="d-flex justify-content-between">
                    <span>Hoàn thành</span>
                    <strong class="text-success">{{ $confirmed }}%</strong>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-success" style="width: {{ $confirmed }}%"></div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <span>Đang xử lý</span>
                    <strong class="text-warning">{{ $pending }}%</strong>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-warning" style="width: {{ $pending }}%"></div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <span>Đã huỷ</span>
                    <strong class="text-danger">{{ $cancelled }}%</strong>
                </div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-danger" style="width: {{ $cancelled }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Latest Bookings Table --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card dashboard-card">
            <div class="card-header">
                <div>
                    <div class="fw-bold fs-5">Latest Bookings</div>
                    <small class="text-muted">Danh sách đơn đặt sân gần đây</small>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Khách hàng</th>
                                <th>Sân</th>
                                <th>Ngày</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-4">Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestBookings ?? [] as $booking)
                            <tr>
                                <td class="ps-4"><strong>#{{ $booking->id }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            {{ strtoupper(substr($booking->user_name ?? 'K', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold"><a href="{{ route('admin.bookings.show', $booking->id) }}" class="text-decoration-none text-dark">{{ $booking->user_name ?? 'Khách lẻ' }}</a></div>
                                            <small class="text-muted">{{ $booking->user_email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $booking->field_name ?? 'Sân chính' }}</td>
                                <td>{{ !empty($booking->booking_date) ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @php $status = strtolower($booking->status ?? ''); @endphp
                                    @if(in_array($status, ['confirmed', 'paid', 'success', 'completed']))
                                        <span class="badge bg-success-subtle text-success">Hoàn thành</span>
                                    @elseif(in_array($status, ['pending', 'waiting']))
                                        <span class="badge bg-warning-subtle text-warning">Chờ xác nhận</span>
                                    @elseif(in_array($status, ['cancelled', 'cancel']))
                                        <span class="badge bg-danger-subtle text-danger">Đã huỷ</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $booking->status ?? 'Mới' }}</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success pe-4">
                                    {{ number_format($booking->display_total ?? 0, 0, ',', '.') }}đ
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center p-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    <span class="text-muted">Chưa có đơn đặt sân nào gần đây.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    {{-- Top Fields --}}
    <div class="col-lg-6">
        <div class="card dashboard-card">
            <div class="card-header">
                <span>Top sân được đặt nhiều</span>
                <span class="text-primary fw-bold">Top 5</span>
            </div>
            <div class="card-body">
                @foreach($topFieldsChart ?? [] as $field)
                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $field->name }}</strong>
                        <span>{{ $field->total }} đơn</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-primary" style="width:{{ min($field->total * 10, 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Revenue Goal --}}
    <div class="col-lg-6">
        <div class="card dashboard-card">
            <div class="card-header">Doanh thu tháng</div>
            <div class="card-body text-center">
                <div id="goalChart" style="height:260px"></div>
                <h3 class="fw-bold mt-2">{{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }} đ</h3>
                <small class="text-muted d-block mt-1">
                    Mục tiêu tháng: <strong>{{ number_format($monthlyTarget ?? 10000000, 0, ',', '.') }}đ</strong>
                </small>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const sparkData = {
        "#spark1": @json($fieldSpark ?? []),
        "#spark2": @json($bookingSpark ?? []),
        "#spark3": @json($revenueSpark ?? []),
        "#spark4": @json($customerSpark ?? [])
    };

    function sparkline(id, color) {
        if(document.querySelector(id)) {
            new ApexCharts(document.querySelector(id), {
                chart: { height: 55, type: 'area', sparkline: { enabled: true }, toolbar: { show: false } },
                stroke: { curve: 'smooth', width: 3 },
                fill: { opacity: .2 },
                colors: [color],
                series: [{ data: sparkData[id] || [] }]
            }).render();
        }
    }

    sparkline("#spark1", "#206bc4");
    sparkline("#spark2", "#2fb344");
    sparkline("#spark3", "#f59f00");
    sparkline("#spark4", "#d63939");
</script>
<script>
    @php
        $r30Labels = $revenue30Days['labels'] ?? [];
        $r30Series = $revenue30Days['series'] ?? [];
        $r7Labels = $revenue7Days['labels'] ?? [];
        $r7Series = $revenue7Days['series'] ?? [];
        $q1 = $quarter1Revenue ?? [0,0,0,0];
        $q2 = $quarter2Revenue ?? [0,0,0,0];
        $q3 = $quarter3Revenue ?? [0,0,0,0];
    @endphp

    if(document.querySelector("#revenueChart")) {
        const rev30Labels = @json($r30Labels);
        const rev30Series = @json($r30Series);
        const rev7Labels = @json($r7Labels);
        const rev7Series = @json($r7Series);
        const q1Data = @json($q1);
        const q2Data = @json($q2);
        const q3Data = @json($q3);
        const customRangeData = @json($customRevenueChart ?? ['dates' => [], 'labels' => [], 'series' => []]);

        let revenueChart = new ApexCharts(document.querySelector("#revenueChart"), {
            chart: { height: 360, type: "area", toolbar: { show: false }, zoom: { enabled: false } },
            colors: ["#206bc4"],
            stroke: { curve: "smooth", width: 4 },
            fill: { type: "gradient", gradient: { opacityFrom: .45, opacityTo: .05 } },
            series: [{ name: "Doanh thu", data: rev30Series }],
            xaxis: { categories: rev30Labels }
        });
        revenueChart.render();

        const customPanel = document.getElementById("customDatePanel");
        const customDateCalendar = document.getElementById("customDateCalendar");
        const customDateSummary = document.getElementById("customDateSummary");
        const applyCustomDate = document.getElementById("applyCustomDate");
        const resetCustomDate = document.getElementById("resetCustomDate");
        const prevMonthBtn = document.getElementById("prevMonthBtn");
        const nextMonthBtn = document.getElementById("nextMonthBtn");

        let calendarViewDate = new Date();
        let selectionStart = null;
        let selectionEnd = null;

        function toDateKey(date) {
            const d = new Date(date);
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        }

        function normalizeRange(start, end) {
            const s = new Date(start);
            const e = new Date(end);
            if (s > e) return { start: e, end: s };
            return { start: s, end: e };
        }

        function formatDate(date) {
            const d = new Date(date);
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
        }

        function updateCustomRevenueChart(startDate, endDate) {
            const start = new Date(startDate + "T00:00:00");
            const end = new Date(endDate + "T00:00:00");
            if (isNaN(start.getTime()) || isNaN(end.getTime()) || start > end) return;

            const labels = [];
            const series = [];
            (customRangeData.dates || []).forEach((date, index) => {
                const current = new Date(date + "T00:00:00");
                if (current >= start && current <= end) {
                    labels.push(customRangeData.labels?.[index] || date);
                    series.push(Number(customRangeData.series?.[index] || 0));
                }
            });
            if (!labels.length) {
                labels.push(...(rev30Labels || []));
                series.push(...(rev30Series || []));
            }
            revenueChart.updateOptions({ xaxis: { categories: labels } });
            revenueChart.updateSeries([{ name: "Doanh thu", data: series }]);
        }

        function renderCustomCalendar() {
            if (!customDateCalendar) return;
            const year = calendarViewDate.getFullYear();
            const month = calendarViewDate.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const weekdayStart = firstDay.getDay();
            const daysInMonth = lastDay.getDate();
            const todayKey = toDateKey(new Date());
            const selected = selectionStart && selectionEnd ? normalizeRange(selectionStart, selectionEnd) : null;

            const weekdays = ['CN','T2','T3','T4','T5','T6','T7'];
            let html = weekdays.map(d => `<div class="custom-date-weekday">${d}</div>`).join('');

            for (let i = 0; i < weekdayStart; i++) {
                html += `<button type="button" class="custom-date-day is-muted" disabled></button>`;
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const key = toDateKey(date);
                let classes = ['custom-date-day'];
                if (key === todayKey) classes.push('is-today');
                if (selected) {
                    if (date.getTime() === selected.start.getTime() || date.getTime() === selected.end.getTime()) classes.push('is-selected');
                    else if (date > selected.start && date < selected.end) classes.push('is-range');
                }
                html += `<button type="button" class="${classes.join(' ')}" data-date="${key}">${day}</button>`;
            }

            customDateCalendar.innerHTML = html;
            customDateCalendar.querySelectorAll('.custom-date-day:not(.is-muted)').forEach(btn => {
                btn.addEventListener('click', () => {
                    const date = new Date(btn.dataset.date + 'T00:00:00');
                    if (!selectionStart || (selectionStart && selectionEnd)) {
                        selectionStart = date;
                        selectionEnd = null;
                    } else {
                        selectionEnd = date;
                        const normalized = normalizeRange(selectionStart, selectionEnd);
                        selectionStart = normalized.start;
                        selectionEnd = normalized.end;
                    }
                    if (selectionStart && selectionEnd) {
                        customDateSummary.innerHTML = `Khoảng đã chọn: <strong>${formatDate(selectionStart)}</strong> → <strong>${formatDate(selectionEnd)}</strong>`;
                    } else {
                        customDateSummary.innerHTML = `Chọn ngày kết thúc...`;
                    }
                    renderCustomCalendar();
                });
            });
        }

        function resetCustomSelection() {
            selectionStart = null;
            selectionEnd = null;
            customDateSummary.innerHTML = 'Chưa chọn khoảng ngày';
            renderCustomCalendar();
        }

        prevMonthBtn?.addEventListener('click', () => {
            calendarViewDate = new Date(calendarViewDate.getFullYear(), calendarViewDate.getMonth() - 1, 1);
            renderCustomCalendar();
        });
        nextMonthBtn?.addEventListener('click', () => {
            calendarViewDate = new Date(calendarViewDate.getFullYear(), calendarViewDate.getMonth() + 1, 1);
            renderCustomCalendar();
        });
        resetCustomDate?.addEventListener('click', resetCustomSelection);
        applyCustomDate?.addEventListener('click', () => {
            if (selectionStart && selectionEnd) {
                updateCustomRevenueChart(toDateKey(selectionStart), toDateKey(selectionEnd));
                customPanel?.classList.remove('visible');
            }
        });

        document.querySelectorAll('.chart-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.chart-tab').forEach(item => item.classList.remove('active'));
                this.classList.add('active');

                const isCustom = this.dataset.type === 'custom';
                if (isCustom) {
                    const isVisible = customPanel?.classList.contains('visible');
                    customPanel?.classList.toggle('visible', !isVisible);
                } else {
                    customPanel?.classList.remove('visible');
                }

                if (this.dataset.type === '30') {
                    revenueChart.updateOptions({ xaxis: { categories: rev30Labels } });
                    revenueChart.updateSeries([{ name: 'Doanh thu', data: rev30Series }]);
                }
                if (this.dataset.type === '7') {
                    revenueChart.updateOptions({ xaxis: { categories: rev7Labels } });
                    revenueChart.updateSeries([{ name: 'Doanh thu', data: rev7Series }]);
                }
                if (this.dataset.quarter === '1') {
                    revenueChart.updateOptions({ xaxis: { categories: ['Th1','Th2','Th3','Th4'] } });
                    revenueChart.updateSeries([{ name: 'Doanh thu', data: q1Data }]);
                }
                if (this.dataset.quarter === '2') {
                    revenueChart.updateOptions({ xaxis: { categories: ['Th5','Th6','Th7','Th8'] } });
                    revenueChart.updateSeries([{ name: 'Doanh thu', data: q2Data }]);
                }
                if (this.dataset.quarter === '3') {
                    revenueChart.updateOptions({ xaxis: { categories: ['Th9','Th10','Th11','Th12'] } });
                    revenueChart.updateSeries([{ name: 'Doanh thu', data: q3Data }]);
                }
            });
        });

        resetCustomSelection();
        renderCustomCalendar();
    }
</script>
<script>
    if(document.querySelector("#bookingChart")) {
        new ApexCharts(document.querySelector("#bookingChart"), {
            chart: { type: "donut", height: 260 },
            series: @json($bookingStatusChart ?? [0,0,0]),
            labels: ["Hoàn thành", "Chờ xác nhận", "Đã huỷ"],
            colors: ["#2fb344", "#f59f00", "#d63939"],
            legend: { position: "bottom" },
            dataLabels: { enabled: false }
        }).render();
    }
</script>
<script>
    if(document.querySelector("#goalChart")) {
        const monthlyPercent = {{ $monthlyPercent ?? 0 }};
        new ApexCharts(document.querySelector("#goalChart"), {
            chart: { type: "radialBar", height: 260 },
            series: [monthlyPercent],
            labels: ["Hoàn thành"],
            colors: ["#206bc4"],
            plotOptions: {
                radialBar: {
                    hollow: { size: "65%" },
                    track: { background: "#edf2f7" },
                    dataLabels: { name: { fontSize: "14px" }, value: { fontSize: "28px", fontWeight: 700 } }
                }
            }
        }).render();
    }
</script>
<script>
    const themeBtn = document.getElementById("themeToggle");
    if (themeBtn) {
        themeBtn.onclick = function() {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", document.body.classList.contains("dark-mode"));
        };
    }
    if (localStorage.getItem("theme") == "true") {
        document.body.classList.add("dark-mode");
    }
</script>
@endpush