<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Stadium;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\StadiumTimeSlotPrice;
use App\Models\StadiumSpecialTimeSlot;
use App\Models\TimeSlot;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StadiumController extends Controller
{
    // Trang chủ
    public function index(Request $request)
    {
        $data = $this->loadFieldsWithSchedule($request);

        return view('user.stadiums.index', $data);
    }

    public function list(Request $request)
    {
        $data = $this->loadFieldsWithSchedule($request);

        return view('user.stadiums.list', $data);
    }

    private function loadFieldsWithSchedule(Request $request): array
    {
        $keyword = $request->keyword;

        $fields = Field::query()
            ->with([
                'stadium',
                'fieldType',
                'images' => fn ($query) => $query->orderByDesc('is_main')->orderBy('id'),
            ])
            ->where('status', true)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhereHas('stadium', function ($stadiumQuery) use ($keyword) {
                            $stadiumQuery->where('name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->latest()
            ->get();

        $timeSlots = TimeSlot::where('status', true)
            ->orderBy('start_time')
            ->get();

        $dates = collect(range(0, 6))->map(fn ($offset) => Carbon::today()->addDays($offset));

        $bookingMap = DB::table('booking_details as bd')
            ->join('bookings as b', 'bd.booking_id', '=', 'b.id')
            ->whereBetween('bd.booking_date', [$dates->first()->toDateString(), $dates->last()->toDateString()])
            ->where('b.status', '!=', 'cancelled')
            ->select('bd.field_id', 'bd.time_slot_id', 'bd.booking_date', 'b.status')
            ->get()
            ->groupBy(fn ($item) => $item->field_id . '-' . $item->booking_date . '-' . $item->time_slot_id);

        $fields->each(function (Field $field) use ($timeSlots, $bookingMap, $dates) {
            $field->setAttribute(
                'display_price',
                $this->calculateSlotPrice($field, '06:00:00')
            );

            $field->setAttribute('scheduleDates', $dates->map(function (Carbon $date) use ($field, $timeSlots, $bookingMap) {
                $dayLabel = $date->format('d/m');
                $weekday = match ($date->dayOfWeek) {
                    0 => 'CN',
                    1 => 'Thứ 2',
                    2 => 'Thứ 3',
                    3 => 'Thứ 4',
                    4 => 'Thứ 5',
                    5 => 'Thứ 6',
                    6 => 'Thứ 7',
                };

                $slots = $timeSlots->map(function ($slot) use ($field, $date, $bookingMap) {
                    $key = $field->id . '-' . $date->toDateString() . '-' . $slot->id;
                    $booking = $bookingMap[$key][0] ?? null;
                    $startTime = is_string($slot->start_time) ? $slot->start_time : data_get($slot, 'start_time');
                    $slotDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->toDateString() . ' ' . substr($startTime, 0, 8));
                    $isPast = $slotDateTime->isPast();

                    if ($booking) {
                        $status = $isPast ? 'played' : 'booked';
                        $label = $isPast ? 'Đã chơi' : 'Đã đặt';
                    } elseif ($isPast) {
                        $status = 'locked';
                        $label = 'Đã khóa';
                    } else {
                        $status = 'available';
                        $label = 'Trống';
                    }

                    return [
                        'status' => $status,
                        'label' => $label,
                        'time' => substr($startTime, 0, 5),
                    ];
                })->toArray();

                return [
                    'dayLabel' => $dayLabel,
                    'weekday' => $weekday,
                    'slots' => $slots,
                ];
            })->toArray());
        });

        $services = Service::query()
            ->where('status', true)
            ->whereNotIn('name', ['Thuê sân bóng', 'Phòng tắm'])
            ->orderBy('name')
            ->get();

        if ($services->isEmpty()) {
            $fallbackServices = [
                ['name' => 'Nước uống', 'description' => 'Nước suối, nước ngọt', 'price' => 10000, 'unit' => 'chai'],
                ['name' => 'Thuê bóng', 'description' => 'Bóng thi đấu chất lượng', 'price' => 50000, 'unit' => 'trận'],
                ['name' => 'Áo bib', 'description' => 'Áo phân đội', 'price' => 20000, 'unit' => 'bộ'],
                ['name' => 'Thuê găng tay', 'description' => 'Găng tay thủ môn', 'price' => 30000, 'unit' => 'cặp'],
                ['name' => 'Bãi gửi xe', 'description' => 'Gửi xe cho khách', 'price' => 5000, 'unit' => 'xe'],
            ];

            $services = collect($fallbackServices)->map(fn ($item) => (object) $item);
        }

        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $reviews = Review::with(['user', 'field.stadium'])
            ->where('status', true)
            ->latest()
            ->take(3)
            ->get();

        return compact('fields', 'services', 'news', 'reviews');
    }

    public function show(Request $request, $id)
    {
        $stadium = Stadium::findOrFail($id);

        $fields = $stadium->fields()->where('status', true)->with('fieldType')->get();
        $selectedField = $fields->firstWhere('id', $request->integer('field'))
            ?? $fields->first();

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
        $fields->each(function ($field) {
            $field->setAttribute('display_price', $this->calculateSlotPrice($field, '06:00:00'));
        });

        $fieldBasePrices = $fields
            ->map(fn ($field) => $field->display_price)
            ->filter(fn ($price) => $price > 0);

        $defaultPrice = $fieldBasePrices->isNotEmpty()
            ? $fieldBasePrices->min()
            : (float) ($stadium->price ?? 0);

        $services = Service::query()
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        $eligibleBookings = collect();

        if (Auth::check()) {
            $eligibleBookings = Booking::query()
                ->where('user_id', Auth::id())
                ->where('status', 'completed')
                ->whereDoesntHave('review')
                ->whereHas('bookingDetails.field', fn ($query) => $query->where('stadium_id', $stadium->id))
                ->with(['bookingDetails' => function ($query) use ($stadium) {
                    $query->whereHas('field', fn ($fieldQuery) => $fieldQuery->where('stadium_id', $stadium->id))
                        ->with('field');
                }])
                ->latest()
                ->get();
        }

        return view('user.stadiums.show', compact(
            'stadium',
            'timeSlots',
            'priceTable',
            'fields',
            'selectedField',
            'reviews',
            'averageRating',
            'defaultPrice',
            'eligibleBookings',
            'services'
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
