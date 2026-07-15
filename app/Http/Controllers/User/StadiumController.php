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

        $fields = $stadium->fields()->where('status', true)->get();

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
            ->where('stadium_id', $stadium->id)
            ->orderBy('start_time')
            ->get();

        $customSlots = StadiumSpecialTimeSlot::where('stadium_id', $stadium->id)
            ->orderBy('start_time')
            ->get();

        $timeSlots = [];

        foreach ($fixedTimeSlots as $slot) {
            $price = $slotPrices[$slot->id] ?? $stadium->price ?? 0;

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

            $timeSlots[] = $customGroup;
        }

        $timeSlots = array_values($timeSlots);

        $defaultPrice = $stadium->price;
        if (!$defaultPrice) {
            $slotPricesForDefault = [];

            foreach ($timeSlots as $group) {
                foreach ($group['slots'] as $slot) {
                    $slotPricesForDefault[] = $slot['price'];
                }
            }

            $defaultPrice = $slotPricesForDefault ? min($slotPricesForDefault) : 0;
        }

        return view('user.stadiums.show', compact(
            'stadium',
            'timeSlots',
            'fields',
            'reviews',
            'averageRating',
            'defaultPrice'
        ));
    }
}