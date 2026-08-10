<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeSlot;
use App\Models\StadiumTimeSlotPrice;
use App\Models\StadiumSpecialTimeSlot;
use App\Models\Stadium;
use App\Models\FieldTimeSlotPrice;
use App\Models\FieldTypeBasePrice;
use App\Models\TimeSlotSurcharge;
use App\Models\FieldType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;use Illuminate\Support\Facades\Validator;
class TimeSlotsController extends Controller
{
    public function index()
    {
        $stadiums = Stadium::orderBy('name')->get();
        $fixedSlots = TimeSlot::where('status', true)->orderBy('start_time')->get();

        $slotPrices = StadiumTimeSlotPrice::whereIn('stadium_id', $stadiums->pluck('id'))
            ->get()
            ->groupBy('stadium_id');

        $specialSlots = StadiumSpecialTimeSlot::whereIn('stadium_id', $stadiums->pluck('id'))
            ->orderBy('stadium_id')
            ->orderBy('start_time')
            ->get()
            ->groupBy('stadium_id');

        return view('admin.time-slots.index', compact(
            'stadiums',
            'fixedSlots',
            'slotPrices',
            'specialSlots'
        ));
    }

    public function show($stadiumId)
    {
        $stadium = Stadium::findOrFail($stadiumId);
        $timeSlots = TimeSlot::where('status', true)->orderBy('start_time')->get();
        $fields = $stadium->fields()->where('status', true)->with('fieldType')->get();

        // Load any field-specific prices for each time slot
        $fieldPricesRaw = FieldTimeSlotPrice::whereIn('field_id', $fields->pluck('id'))
            ->get()
            ->groupBy('field_id');

        $fieldPrices = [];
        foreach ($fieldPricesRaw as $fieldId => $items) {
            $fieldPrices[$fieldId] = $items->mapWithKeys(fn($it) => [$it->time_slot_id => $it->price])->all();
        }

        $priceTable = $timeSlots->map(function ($slot) use ($fields, $fieldPrices) {
            return [
                'slot' => $slot,
                'prices' => $fields->mapWithKeys(fn ($field) => [
                    $field->id => $fieldPrices[$field->id][$slot->id] ?? null,
                ])->all(),
            ];
        });

        return view('admin.time-slots.show', compact(
            'stadium', 
            'timeSlots', 
            'fields', 
            'priceTable', 
            'fieldPrices'
        ));
    }

    public function storeForStadium(Request $request, $stadiumId)
    {
        $stadium = Stadium::findOrFail($stadiumId);

        $data = $request->validate([
            'prices' => 'required|array',
            'prices.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($data['prices'] as $timeSlotId => $value) {
            $price = (float) preg_replace('/[^0-9.]/', '', (string) $value);

            StadiumTimeSlotPrice::updateOrCreate(
                ['stadium_id' => $stadium->id, 'time_slot_id' => $timeSlotId],
                ['price' => $price]
            );
        }

        return redirect()->route('admin.time-slots.show', $stadium->id)
            ->with('success', 'Lưu giá cố định theo sân thành công.');
    }

    public function storeFieldSlot(Request $request, $stadiumId, $fieldId, $timeSlotId)
    {
        $stadium = Stadium::findOrFail($stadiumId);
        $field = \App\Models\Field::where('stadium_id', $stadium->id)->where('id', $fieldId)->firstOrFail();

        $data = $request->validate([
            'price' => 'nullable',
        ]);

        $price = isset($data['price']) ? (float) preg_replace('/[^0-9.]/', '', (string) $data['price']) : null;

        if ($price === null) {
            // delete override if exists
            FieldTimeSlotPrice::where('field_id', $field->id)->where('time_slot_id', $timeSlotId)->delete();
        } else {
            FieldTimeSlotPrice::updateOrCreate(
                ['field_id' => $field->id, 'time_slot_id' => $timeSlotId],
                ['price' => $price]
            );
        }

        return redirect()->route('admin.time-slots.show', $stadium->id)->with('success', 'Lưu giá khung giờ cho sân con thành công.');
    }

    public function update(Request $request, $stadiumId, $timeSlotId)
    {
        $stadium = Stadium::findOrFail($stadiumId);
        $timeSlot = TimeSlot::findOrFail($timeSlotId);

        $data = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $start = $data['start_time'];
        $end = $data['end_time'];
        $startFull = strlen($start) === 5 ? $start . ':00' : $start;
        $endFull = strlen($end) === 5 ? $end . ':00' : $end;

        if ($this->hasOverlappingTimeSlot($startFull, $endFull, $timeSlot->id)) {
            return redirect()->route('admin.time-slots.show', $stadium->id)
                ->withInput()
                ->withErrors(['start_time' => 'Khung giờ không được trùng hoặc chồng lên khung giờ hiện có.']);
        }

        $timeSlot->update([
            'start_time' => $startFull,
            'end_time' => $endFull,
            'status' => true,
        ]);

        return redirect()->route('admin.time-slots.show', $stadium->id)
            ->with('success', 'Đã cập nhật khung giờ thành công.');
    }

    public function addForStadium(Request $request, $stadiumId)
    {
        $stadium = Stadium::findOrFail($stadiumId);

        $data = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $start = $data['start_time'];
        $end = $data['end_time'];

        // Normalize to full seconds
        $startFull = strlen($start) === 5 ? $start . ':00' : $start;
        $endFull = strlen($end) === 5 ? $end . ':00' : $end;

        // Try find existing time slot by full or short times
        $timeSlot = TimeSlot::where(function ($q) use ($startFull, $start) {
            $q->where('start_time', $startFull)->orWhere('start_time', $start);
        })->where(function ($q) use ($endFull, $end) {
            $q->where('end_time', $endFull)->orWhere('end_time', $end);
        })->first();

        if ($this->hasOverlappingTimeSlot($startFull, $endFull)) {
            return redirect()->route('admin.time-slots.show', $stadium->id)
                ->withInput()
                ->withErrors(['start_time' => 'Khung giờ không được trùng hoặc chồng lên khung giờ hiện có.']);
        }

        if (!$timeSlot) {
            $timeSlotId = \Illuminate\Support\Facades\DB::table('time_slots')->insertGetId([
                'start_time' => $startFull,
                'end_time' => $endFull,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $timeSlot = TimeSlot::find($timeSlotId);
        }

        return redirect()->route('admin.time-slots.show', $stadium->id)
            ->with('success', 'Đã thêm khung giờ thành công.');
    }

    public function destroy($stadiumId, $timeSlotId)
    {
        $stadium = Stadium::findOrFail($stadiumId);

        // Xóa giá riêng của sân nếu có
        $price = StadiumTimeSlotPrice::where('stadium_id', $stadiumId)
            ->where('time_slot_id', $timeSlotId)
            ->first();

        if ($price) {
            $price->delete();
        }

        TimeSlot::where('id', $timeSlotId)->delete();

        return redirect()
            ->route('admin.time-slots.show', $stadiumId)
            ->with('success', 'Đã xóa khung giờ và giá.');
    }

    public function storeDefaults(Request $request)
    {
        $data = $request->input('defaults', []);

        foreach ($data as $timeSlotId => $value) {
            $price = (float) preg_replace('/[^0-9.]/', '', (string) $value);

            StadiumTimeSlotPrice::updateOrCreate(
                ['stadium_id' => null, 'time_slot_id' => $timeSlotId],
                ['price' => $price]
            );
        }

        return redirect()->route('admin.stadiums.index')->with('success', 'Lưu giá mặc định thành công.');
    }

    /**
     * Kiểm tra khung giờ mới có chồng lên khung giờ đã tồn tại không.
     *
     * @param string $start
     * @param string $end
     * @param int|null $excludeId
     * @return bool
     */
    private function hasOverlappingTimeSlot(string $start, string $end, int $excludeId = null): bool
    {
        $timeSlots = TimeSlot::where('status', true)
            ->when($excludeId !== null, fn ($query) => $query->where('id', '<>', $excludeId))
            ->orderBy('start_time')
            ->get();

        foreach ($timeSlots as $slot) {
            $slotStart = strlen($slot->start_time) === 5 ? $slot->start_time . ':00' : $slot->start_time;
            $slotEnd = strlen($slot->end_time) === 5 ? $slot->end_time . ':00' : $slot->end_time;

            if ($this->intervalsOverlap($slotStart, $slotEnd, $start, $end)) {
                return true;
            }
        }

        return false;
    }

    private function intervalsOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        return $startA < $endB && $endA > $startB;
    }

    /** 
     * Tính giá cho một khung giờ dựa trên:
     * 1. Giá cơ bản của loại sân (nếu có)
     * 2. Phụ phí khung giờ (giờ tối, giờ cao điểm, v.v.)
     * 3. Giá riêng của sân (override)
     */
    private function calculateSlotPrice($field, TimeSlot $slot, $fieldTypeBasePrices): float
    {
        // Nếu sân có giá cơ bản theo loại sân
        if ($field->field_type_id && isset($fieldTypeBasePrices[$field->field_type_id])) {
            $basePrice = (float)$fieldTypeBasePrices[$field->field_type_id]->base_price;
        } else {
            // Fallback: sử dụng giá cũ (hardcoded)
            $basePrice = $this->getLegacyFieldPrice($field);
        }

        // Thêm phụ phí khung giờ nếu có
        $surcharge = 0;
        if ($slot->peak_hour_surcharge) {
            $surcharge += (float)$slot->peak_hour_surcharge;
        }

        return $basePrice + $surcharge;
    }

    /** 
     * Lấy giá cũ dựa trên công thức hardcoded (cho tương thích ngược)
     * Sân 7: 350k, Sân 9: 400k, Sân 11: 500k, + 100k nếu từ 18:00
     */
    private function getLegacyFieldPrice($field): float
    {
        $players = null;

        foreach ([$field->name ?? '', $field->fieldType?->name ?? ''] as $label) {
            if (preg_match('/(?<!\d)(7|9|11)(?!\d)/u', (string) $label, $matches)) {
                $players = (int) $matches[1];
                break;
            }
        }

        $players ??= $field->fieldType?->number_of_players ?? null;

        $basePrice = [7 => 350000, 9 => 400000, 11 => 500000][$players]
            ?? (float) ($field->price_per_hour ?? 0);

        return $basePrice;
    }

    /**
     * Trang quản lý cấu hình giá linh hoạt
     */
    public function pricing()
    {
        $fieldTypes = FieldType::orderBy('name')->get();
        $fieldTypeBasePrices = Schema::hasTable('field_type_base_prices')
            ? FieldTypeBasePrice::with('fieldType')->get()->keyBy('field_type_id')
            : collect();
        $timeSlots = TimeSlot::where('status', true)->orderBy('start_time')->get();
        $timeSlotsWithSurcharges = $timeSlots->map(function ($slot) {
            return [
                'slot' => $slot,
                'surcharges' => $slot->surcharges,
            ];
        });

        return view('admin.time-slots.pricing', compact(
            'fieldTypes',
            'fieldTypeBasePrices',
            'timeSlots',
            'timeSlotsWithSurcharges'
        ));
    }

    /**
     * Lưu giá cơ bản cho loại sân
     */
    public function storeFieldTypePrice(Request $request, $fieldTypeId)
    {
        if (! Schema::hasTable('field_type_base_prices')) {
            return response()->json([
                'success' => false,
                'message' => 'Missing pricing table. Please run migrations or restore the database schema.',
            ], 500);
        }

        $fieldType = FieldType::findOrFail($fieldTypeId);

        $data = $request->validate([
            'base_price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $price = (float) preg_replace('/[^0-9.]/', '', (string) $data['base_price']);

        FieldTypeBasePrice::updateOrCreate(
            ['field_type_id' => $fieldTypeId],
            [
                'base_price' => $price,
                'description' => $data['description'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Đã lưu giá cho loại sân: {$fieldType->name}",
        ]);
    }

    /**
     * Thêm phụ phí cho khung giờ
     */
    public function addTimeSurcharge(Request $request, $timeSlotId)
    {
        $timeSlot = TimeSlot::findOrFail($timeSlotId);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'surcharge_amount' => 'required|numeric|min:0',
            'type' => 'required|in:fixed,percentage',
        ]);

        $amount = (float) preg_replace('/[^0-9.]/', '', (string) $data['surcharge_amount']);

        $surcharge = TimeSlotSurcharge::create([
            'time_slot_id' => $timeSlotId,
            'name' => $data['name'],
            'surcharge_amount' => $amount,
            'type' => $data['type'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã thêm phụ phí: {$data['name']}",
            'surcharge' => $surcharge,
        ]);
    }

    /**
     * Xóa phụ phí
     */
    public function deleteTimeSurcharge($surchargeId)
    {
        $surcharge = TimeSlotSurcharge::findOrFail($surchargeId);
        $timeSlot = $surcharge->timeSlot;
        $surcharge->delete();

        return response()->json([
            'success' => true,
            'message' => "Đã xóa phụ phí",
        ]);
    }

    /**
     * Cập nhật thông tin khung giờ (thêm tên, đánh dấu giờ cao điểm, giờ tối)
     */
    public function updateTimeSlotInfo(Request $request, $timeSlotId)
    {
        $timeSlot = TimeSlot::findOrFail($timeSlotId);

        $data = $request->validate([
            'name' => 'nullable|string|max:100',
            'duration_minutes' => 'nullable|integer|min:30|max:240',
            'is_peak_hour' => 'boolean',
            'is_evening' => 'boolean',
            'peak_hour_surcharge' => 'nullable|numeric|min:0',
        ]);

        $surcharge = isset($data['peak_hour_surcharge']) 
            ? (float) preg_replace('/[^0-9.]/', '', (string) $data['peak_hour_surcharge'])
            : null;

        $timeSlot->update([
            'name' => $data['name'] ?? $timeSlot->name,
            'duration_minutes' => $data['duration_minutes'] ?? $timeSlot->duration_minutes,
            'is_peak_hour' => $data['is_peak_hour'] ?? $timeSlot->is_peak_hour,
            'is_evening' => $data['is_evening'] ?? $timeSlot->is_evening,
            'peak_hour_surcharge' => $surcharge,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật khung giờ",
            'timeSlot' => $timeSlot,
        ]);
    }

    /**
     * Bulk update giá cho nhiều trường cùng một lúc
     */
    public function bulkUpdatePrices(Request $request, $stadiumId)
    {
        $stadium = Stadium::findOrFail($stadiumId);

        $validator = Validator::make($request->all(), [
            'slots' => 'sometimes|array',
            'slots.*.start_time' => 'required_with:slots.*.end_time|date_format:H:i',
            'slots.*.end_time' => 'required_with:slots.*.start_time|date_format:H:i',
            'prices' => 'sometimes|array',
            'prices.*.*' => 'nullable|numeric|min:0',
        ]);

        $validator->after(function ($validator) use ($request) {
            $slots = $request->input('slots', []);
            $processed = [];
            $slotErrors = [];

            foreach ($slots as $slotId => $slotData) {
                $start = $slotData['start_time'] ?? null;
                $end = $slotData['end_time'] ?? null;

                if (! $start || ! $end) {
                    continue;
                }

                if ($end <= $start) {
                    $slotErrors[$slotId] = [
                        'key' => "slots.$slotId.end_time",
                        'message' => 'Giờ kết thúc phải sau giờ bắt đầu.',
                    ];
                    continue;
                }

                $startFull = strlen($start) === 5 ? $start . ':00' : $start;
                $endFull = strlen($end) === 5 ? $end . ':00' : $end;

                if ($this->hasOverlappingTimeSlot($startFull, $endFull, $slotId)) {
                    $slotErrors[$slotId] = [
                        'key' => "slots.$slotId.start_time",
                        'message' => 'Khung giờ không được trùng hoặc chồng lên khung giờ hiện có.',
                    ];
                }

                $processed[$slotId] = ['start' => $startFull, 'end' => $endFull];
            }

            foreach ($processed as $slotId => $interval) {
                foreach ($processed as $otherId => $otherInterval) {
                    if ($slotId === $otherId) {
                        continue;
                    }

                    if ($this->intervalsOverlap($interval['start'], $interval['end'], $otherInterval['start'], $otherInterval['end'])) {
                        if (! isset($slotErrors[$slotId])) {
                            $slotErrors[$slotId] = [
                                'key' => "slots.$slotId.start_time",
                                'message' => 'Khung giờ không được chồng lên nhau.',
                            ];
                        }

                        if (! isset($slotErrors[$otherId])) {
                            $slotErrors[$otherId] = [
                                'key' => "slots.$otherId.start_time",
 'message' => 'Khung giờ không được chồng lên nhau.',
                            ];
                        }
                    }
                }
            }

            foreach ($slotErrors as $slotError) {
                $validator->errors()->add($slotError['key'], $slotError['message']);
            }
        });

        if ($validator->fails()) {
            return redirect()->route('admin.time-slots.show', $stadium->id)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        DB::beginTransaction();

        try {
            if (! empty($data['slots'])) {
                foreach ($data['slots'] as $slotId => $slotData) {
                    $timeSlot = TimeSlot::findOrFail($slotId);
                    $timeSlot->update([
                        'start_time' => strlen($slotData['start_time']) === 5 ? $slotData['start_time'] . ':00' : $slotData['start_time'],
                        'end_time' => strlen($slotData['end_time']) === 5 ? $slotData['end_time'] . ':00' : $slotData['end_time'],
                        'status' => true,
                    ]);
                }
            }

            if (! empty($data['prices'])) {
                foreach ($data['prices'] as $fieldId => $slotPrices) {
                    foreach ($slotPrices as $slotId => $price) {
                        $priceValue = $price === '' || $price === null ? null : (float) preg_replace('/[^0-9.]/', '', (string) $price);

                        if ($priceValue === null) {
                            FieldTimeSlotPrice::where('field_id', $fieldId)
                                ->where('time_slot_id', $slotId)
                                ->delete();
                        } else {
                            FieldTimeSlotPrice::updateOrCreate(
                                [
                                    'field_id' => $fieldId,
                                    'time_slot_id' => $slotId,
                                ],
                                ['price' => $priceValue]
                            );
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.time-slots.show', $stadium->id)
                ->with('success', 'Đã lưu tất cả thay đổi.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.time-slots.show', $stadium->id)
                ->with('error', 'Có lỗi khi lưu thay đổi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Export giá hiện tại dưới dạng CSV/Excel
     */
    public function exportPrices($stadiumId)
    {
        $stadium = Stadium::findOrFail($stadiumId);
        $timeSlots = TimeSlot::where('status', true)->orderBy('start_time')->get();
        $fields = $stadium->fields()->where('status', true)->with('fieldType')->get();

        $fileName = 'gia-' . str_slug($stadium->name) . '-' . now()->format('Y-m-d-Hi') . '.csv';
        
        $callback = function () use ($stadium, $timeSlots, $fields) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, array_merge(['Khung giờ'], $fields->pluck('name')->toArray()));
            
            // Data
            foreach ($timeSlots as $slot) {
                $row = [\Carbon\Carbon::parse($slot->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($slot->end_time)->format('H:i')];
                
                foreach ($fields as $field) {
                    $price = FieldTimeSlotPrice::where('field_id', $field->id)
                        ->where('time_slot_id', $slot->id)
                        ->first();
                    
                    $row[] = $price ? $price->price : '';
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }
}
