<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use App\Models\StadiumTimeSlotPrice;
use App\Models\StadiumSpecialTimeSlot;
use App\Models\TimeSlot;
use Illuminate\Http\Request;

class StadiumController extends Controller
{
    // Trang chủ
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $stadiums = Stadium::query()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->latest()
            ->get();

        return view('user.stadiums.index', compact('stadiums'));
    }

    public function show($id)
    {
        $stadium = Stadium::findOrFail($id);

        $fields = $stadium->fields()->where('status', true)->with('fieldType')->get();
        $selectedField = $fields->first();

        $reviews = $stadium->reviews()
            ->where('reviews.status', true)
            ->with(['user', 'field'])
            ->latest()
            ->get();


        $averageRating = $stadium->reviews()
            ->where('reviews.status', true)
            ->avg('rating');

        $averageRating = $averageRating ? round($averageRating, 1) : 0;

        $slotPrices = StadiumTimeSlotPrice::where('stadium_id', $stadium->id)
            ->pluck('price', 'time_slot_id')
            ->toArray();

        $fixedTimeSlots = TimeSlot::where('status', true)
            ->orderBy('start_time')
            ->get();

        $customSlots = StadiumSpecialTimeSlot::where('stadium_id', $stadium->id)
            ->orderBy('start_time')
            ->get();

        $timeSlots = [];
        $priceTable = [];

        foreach ($fixedTimeSlots as $slot) {
            $price = $selectedField
                ? $this->calculateSlotPrice($selectedField, $slot->start_time)
                : ($slotPrices[$slot->id] ?? $stadium->price ?? 0);

            $hour = \Carbon\Carbon::createFromFormat('H:i:s', $slot->start_time)->hour;
            $session = $hour >= 12 && $hour < 18 ? 'Buổi chiều' : ($hour >= 18 ? 'Buổi tối' : 'Buổi sáng');

            if (!isset($timeSlots[$session])) {
                $timeSlots[$session] = ['session' => $session, 'slots' => []];
            }

            $timeSlots[$session]['slots'][] = [
                'id' => $slot->id,
                'time' => substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5),
                'price' => (float) $price,
            ];

            if (!isset($priceTable[$session])) {
                $priceTable[$session] = ['session' => $session, 'slots' => []];
            }

            $fieldPrices = [];
            foreach ($fields as $field) {
                $fieldPrices[$field->id] = $this->calculateSlotPrice($field, $slot->start_time);
            }

            $priceTable[$session]['slots'][] = [
                'time' => substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5),
                'prices' => $fieldPrices,
            ];
        }

        if ($customSlots->isNotEmpty()) {
            $customGroup = ['session' => 'Khung giờ đặc biệt', 'slots' => []];

            foreach ($customSlots as $customSlot) {
                $customGroup['slots'][] = [
                    'id' => 'custom-' . $customSlot->id,
                    'time' => substr($customSlot->start_time, 0, 5) . ' - ' . substr($customSlot->end_time, 0, 5),
                    'price' => (float) $customSlot->price,
                ];
            }

            $priceTable['Khung giờ đặc biệt'] = [
                'session' => 'Khung giờ đặc biệt',
                'slots' => $customSlots->map(function ($customSlot) use ($fields) {
                    return [
                        'time' => substr($customSlot->start_time, 0, 5) . ' - ' . substr($customSlot->end_time, 0, 5),
                        'prices' => $fields->mapWithKeys(fn ($field) => [$field->id => (float) $customSlot->price])->all(),
                    ];
                })->all(),
            ];

            $timeSlots[] = $customGroup;
        }

        $timeSlots = array_values($timeSlots);
        $priceTable = array_values($priceTable);

        // "Giá từ" phải lấy từ sân con, không dùng giá chung cũ của cơ sở.
        $fieldBasePrices = $fields
            ->map(fn ($field) => $this->calculateSlotPrice($field, '06:00:00'))
            ->filter(fn ($price) => $price > 0);

        $defaultPrice = $fieldBasePrices->isNotEmpty()
            ? $fieldBasePrices->min()
            : (float) ($stadium->price ?? 0);

        return view('user.stadiums.show', compact(
            'stadium',
            'timeSlots',
            'priceTable',
            'fields',
            'reviews',
            'averageRating',
            'defaultPrice'
        ));
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
