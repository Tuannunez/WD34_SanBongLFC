@extends('admin.layouts.app')

@section('title', 'Quản lý khung giờ và giá')
@section('page-title', 'Quản lý khung giờ và giá')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold">Quản lý khung giờ và giá: {{ $stadium->name }}</h4>
                    <p class="text-muted mb-0">Thêm/chỉnh sửa khung giờ, cấu hình giá linh hoạt cho từng sân con</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.stadiums.index') }}" class="btn btn-secondary">Quay lại</a>
                    <a href="{{ route('admin.time-slots.export', $stadium->id) }}" class="btn btn-success">Xuất Excel</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Lỗi:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-2">Quản lý khung giờ</h5>
                    <p class="text-muted mb-0">Thêm, sửa và thiết lập giá cho từng khung giờ.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-primary" onclick="openAddSlotModal()">➕ Thêm khung giờ</button>
                </div>
            </div>

            <div class="card mb-4 border-secondary">
                <div class="card-body py-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Sân</label>
                            <select id="filter-field" class="form-select form-select-sm" onchange="applyFilters()">
                                <option value="all">Tất cả</option>
                                @foreach($fields as $field)
                                    <option value="field-{{ $field->id }}">{{ $field->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Trạng thái</label>
                            <select id="filter-status" class="form-select form-select-sm" onchange="applyFilters()">
                                <option value="all">Tất cả</option>
                                <option value="active">Hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Buổi</label>
                            <select id="filter-session" class="form-select form-select-sm" onchange="applyFilters()">
                                <option value="all">Tất cả</option>
                                <option value="morning">Sáng</option>
                                <option value="afternoon">Chiều</option>
                                <option value="evening">Tối</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng giá -->
            <div class="card">
                <div class="card-header bg-light">
                    <small class="text-uppercase text-secondary fw-semibold">Bảng giá theo khung giờ</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th style="background-color: #f8f9fa; min-width: 180px; max-width: 240px;">Khung giờ</th>
                                @foreach($fields as $field)
                                    <th class="field-col field-{{ $field->id }}" style="width: 100px; min-width: 90px; max-width: 120px;">
                                        <small class="text-muted d-block text-truncate" style="max-width: 110px;">{{ $field->fieldType?->name ?? 'Loại sân' }}</small>
                                        <strong class="d-block text-truncate" style="max-width: 110px;">{{ $field->name ?: 'Sân #' . $field->id }}</strong>
                                    </th>
                                @endforeach
                                <th style="width: 120px; min-width: 120px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($priceTable as $row)
                                @php
                                    $slot = $row['slot'];
                                    $slotDisplay = \Carbon\Carbon::parse($slot->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($slot->end_time)->format('H:i');
                                @endphp
                                        @php
                                    $hour = \Carbon\Carbon::parse($slot->start_time)->hour;
                                    $session = $hour < 12 ? 'morning' : ($hour < 18 ? 'afternoon' : 'evening');
                                @endphp
                                <tr class="slot-row" data-field="all" data-session="{{ $session }}" data-status="active">
                                    <td class="text-start" style="background-color: #f8f9fa; min-width: 180px; max-width: 240px;">
                                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                                        @if($slot->is_peak_hour)
                                            <small class="text-warning">Giờ cao điểm</small>
                                        @elseif($slot->is_evening)
                                            <small class="text-danger">Giờ tối</small>
                                        @else
                                            <small class="text-muted">&nbsp;</small>
                                        @endif
                                    </td>
                                    
                                    @foreach($fields as $field)
                                        @php
                                            $value = $fieldPrices[$field->id][$slot->id] ?? null;
                                            $display = $value !== null ? number_format($value, 0, ',', '.') . 'đ' : '—';
                                            $label = $value !== null ? 'Đã thiết lập' : 'Chưa thiết lập';
                                        @endphp
                                        <td class="text-center field-col field-{{ $field->id }}">
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $display }}</div>
                                                <small class="text-muted">{{ $label }}</small>
                                            </div>
                                        </td>
                                    @endforeach
                                    
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Thao tác khung giờ">
                                            <button type="button" class="btn btn-outline-secondary btn-sm edit-slot-button" title="Chỉnh khung giờ và giá" data-slot-id="{{ $slot->id }}" data-slot-start="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}" data-slot-end="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}" data-slot-prices='@json($row['prices'])'>✏️</button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Xóa khung giờ" onclick="deleteSlot({{ $slot->id }})">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($fields) + 2 }}" class="text-center text-muted py-4">
                                        Chưa có khung giờ nào. Hãy thêm khung giờ ở trên.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <!-- Modal sửa/ thêm khung giờ và giá -->
            <div class="modal fade" id="slot-modal" tabindex="-1" aria-labelledby="slot-modal-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <form id="slot-form" method="POST">
                            @csrf
                            <input type="hidden" id="slot-id" name="slot_id" value="">
                            <input type="hidden" id="slot-form-method" name="_method" value="">
                            <div class="modal-header">
                                <h5 class="modal-title" id="slot-modal-title">Thêm khung giờ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="slot-start" class="form-label">Giờ bắt đầu</label>
                                        <input id="slot-start" name="start_time" type="time" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="slot-end" class="form-label">Giờ kết thúc</label>
                                        <input id="slot-end" name="end_time" type="time" class="form-control" required>
                                    </div>
                                </div>
                                <div id="slot-price-inputs"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-primary">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.timeSlotFields = @json($fields->map(fn($field) => ['id' => $field->id, 'name' => $field->name]));

function openAddSlotModal() {
    const modal = document.getElementById('slot-modal');
    document.getElementById('slot-modal-title').innerText = 'Thêm khung giờ';
    document.getElementById('slot-form').action = '{{ route('admin.time-slots.add', $stadium->id) }}';
    document.getElementById('slot-form-method').value = '';
    document.getElementById('slot-id').value = '';
    document.getElementById('slot-start').name = 'start_time';
    document.getElementById('slot-end').name = 'end_time';
    document.getElementById('slot-start').value = '';
    document.getElementById('slot-end').value = '';
    document.getElementById('slot-price-inputs').innerHTML = buildSlotPriceInputs(null, {});
    new bootstrap.Modal(modal).show();
}

function openEditSlotModal(button) {
    const slotId = button.dataset.slotId;
    const startTime = button.dataset.slotStart;
    const endTime = button.dataset.slotEnd;
    let prices = {};

    if (button.dataset.slotPrices) {
        try {
            prices = JSON.parse(button.dataset.slotPrices);
        } catch (error) {
            console.warn('Không thể parse giá slot:', error);
            prices = {};
        }
    }

    const modal = document.getElementById('slot-modal');
    document.getElementById('slot-modal-title').innerText = 'Chỉnh khung giờ và giá';
    document.getElementById('slot-form').action = '{{ route('admin.time-slots.bulk-update-prices', $stadium->id) }}';
    document.getElementById('slot-form-method').value = '';
    document.getElementById('slot-id').value = slotId;
    document.getElementById('slot-start').name = `slots[${slotId}][start_time]`;
    document.getElementById('slot-end').name = `slots[${slotId}][end_time]`;
    document.getElementById('slot-start').value = startTime;
    document.getElementById('slot-end').value = endTime;
    document.getElementById('slot-price-inputs').innerHTML = buildSlotPriceInputs(slotId, prices);
    new bootstrap.Modal(modal).show();
}

document.addEventListener('click', function (event) {
    const button = event.target.closest('.edit-slot-button');
    if (button) {
        openEditSlotModal(button);
    }
});

function buildSlotPriceInputs(slotId, prices) {
    const fields = window.timeSlotFields || [];
    return fields.map(field => {
        const value = prices?.[field.id] ?? '';
        const name = slotId ? `prices[${field.id}][${slotId}]` : `prices[${field.id}]`;
        return `
            <div class="mb-3">
                <label class="form-label">Giá ${field.name}</label>
                <input type="number" min="0" step="1000" class="form-control" name="${name}" value="${value}" placeholder="Nhập giá cho ${field.name}">
            </div>
        `;
    }).join('');
}

function applyFilters() {
    const selectedField = document.getElementById('filter-field').value;
    const status = document.getElementById('filter-status').value;
    const session = document.getElementById('filter-session').value;
    const rows = document.querySelectorAll('.slot-row');
    const fieldColumns = document.querySelectorAll('.field-col');

    fieldColumns.forEach(col => {
        if (selectedField === 'all') {
            col.style.display = '';
        } else {
            col.style.display = col.classList.contains(selectedField) ? '' : 'none';
        }
    });

    rows.forEach(row => {
        let visible = true;

        if (status !== 'all') {
            visible = row.dataset.status === status;
        }

        if (visible && session !== 'all') {
            visible = row.dataset.session === session;
        }

        row.style.display = visible ? '' : 'none';
    });
}

function deleteSlot(slotId) {
    if (!confirm('Xác nhận xóa khung giờ này?')) {
        return;
    }

    const baseUrl = "{{ url('admin/time-slots') }}";
    const action = baseUrl + '/' + {{ $stadium->id }} + '/' + slotId;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    form.style.display = 'none';

    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = '{{ csrf_token() }}';

    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';

    form.appendChild(tokenInput);
    form.appendChild(methodInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

<style>
.table td {
    vertical-align: middle;
    padding: 0.75rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}

.btn-group {
    gap: 5px;
}
</style>
@endsection
