<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeSlot;
use App\Models\StadiumTimeSlotPrice;
use App\Models\StadiumSpecialTimeSlot;
use App\Models\Stadium;

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

        $priceTable = $timeSlots->map(function ($slot) use ($fields) {
            return [
                'slot' => $slot,
                'prices' => $fields->mapWithKeys(fn ($field) => [
                    $field->id => $this->calculateSlotPrice($field, $slot->start_time),
                ])->all(),
            ];
        });

        return view('admin.time-slots.show', compact('stadium', 'timeSlots', 'fields', 'priceTable'));
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

    // Kiểm tra khung giờ còn được sân khác sử dụng không
    $usedByOtherStadium = StadiumTimeSlotPrice::where('time_slot_id', $timeSlotId)
        ->exists();

    // Kiểm tra đã có đơn đặt chưa
    $hasBooking = \DB::table('booking_details')
        ->where('time_slot_id', $timeSlotId)
        ->exists();

    if (!$usedByOtherStadium && !$hasBooking) {
        TimeSlot::where('id', $timeSlotId)->delete();

        return redirect()
            ->route('admin.time-slots.show', $stadiumId)
            ->with('success', 'Đã xóa khung giờ.');
    }

    return redirect()
        ->route('admin.time-slots.show', $stadiumId)
        ->with('success', 'Đã xóa giá của sân. Khung giờ được giữ lại vì còn được sử dụng.');
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

        return redirect()->route('admin.time-slots.index')->with('success', 'Lưu giá mặc định thành công.');
    }

    /** Giá cho một ca 90 phút; ca bắt đầu từ 18:00 được cộng 100.000đ. */
    private function calculateSlotPrice($field, ?string $startTime): float
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

        return $basePrice + ((int) substr((string) $startTime, 0, 2) >= 18 ? 100000 : 0);
    }
}
