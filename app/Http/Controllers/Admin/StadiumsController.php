<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use App\Models\Field;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Models\StadiumTimeSlotPrice;
use App\Models\StadiumSpecialTimeSlot;
use App\Models\FieldType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StadiumsController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('stadiums')
            ->orderByDesc('id');

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");

                if (Schema::hasColumn('stadiums', 'address')) {
                    $q->orWhere('address', 'like', "%{$keyword}%");
                }

                if (Schema::hasColumn('stadiums', 'phone')) {
                    $q->orWhere('phone', 'like', "%{$keyword}%");
                }
            });
        }

        $stadiums = $query->paginate(10)->withQueryString();

        return view('admin.stadiums.index', compact('stadiums'));
    }

    public function create()
    {
        return view('admin.stadiums.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|max:255',
            'open_time' => 'required',
            'close_time' => 'required',
            'description' => 'nullable|string',
        ]);

        if ($request->filled('price')) {
            $data['price'] = (float) preg_replace('/[^0-9.]/', '', (string) $request->input('price'));
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('stadiums', 'public');
        }

        Stadium::create($data);

        return redirect()
            ->route('admin.stadiums.index')
            ->with('success', 'Thêm cơ sở sân thành công.');
    }

    public function show($id)
    {
        $stadium = Stadium::findOrFail($id);

        $fields = Field::where('stadium_id', $stadium->id)
            ->with('fieldType')
            ->orderBy('name')
            ->get();

        $services = Service::query()->get();

        // Khung giờ được dùng chung cho toàn hệ thống; bảng time_slots không có stadium_id.
        $slots = TimeSlot::query()
            ->where('status', true)
            ->orderBy('start_time')
            ->get();

        $timeSlots = [];
        
        foreach ($slots as $slot) {
            // Parse time string
            $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $slot->start_time);
            $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $slot->end_time);
            $hour = $startTime->hour;
            
            // Xác định session dựa trên start_time
            if ($hour >= 6 && $hour < 12) {
                $session = 'Sáng';
            } elseif ($hour >= 12 && $hour < 18) {
                $session = 'Chiều';
            } else {
                $session = 'Tối';
            }

            // Nếu session chưa tồn tại trong mảng, tạo mới
            if (!isset($timeSlots[$session])) {
                $timeSlots[$session] = [
                    'session' => $session,
                    'slots' => []
                ];
            }

            // Thêm slot vào session
            $timeSlots[$session]['slots'][] = [
                'id' => $slot->id,
                'time' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
                'start_time' => $startTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'price' => 0 // Giá mặc định, có thể cập nhật sau
            ];
        }

        // Chuyển về mảng indexed
        $timeSlots = array_values($timeSlots);

        $fieldTypes = FieldType::where('status', true)->orderBy('name')->get();

        return view('admin.stadiums.show', compact(
            'stadium',
            'fields',
            'timeSlots',
            'services',
            'fieldTypes'
        ));
    }

    public function storeField(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'field_type_id' => 'required|exists:field_types,id',
            'price_per_hour' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $data['stadium_id'] = $stadium->id;
        $data['status'] = (bool) ($request->input('status', 1));

        Field::create($data);

        return redirect()->route('admin.stadiums.show', $stadium->id)
            ->with('success', 'Thêm sân con thành công.');
    }

    public function updateField(Request $request, Stadium $stadium, Field $field)
    {
        if ($field->stadium_id !== $stadium->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|max:255',
            'field_type_id' => 'required|exists:field_types,id',
            'price_per_hour' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $data['status'] = (bool) ($request->input('status', 1));

        $field->update($data);

        return redirect()->route('admin.stadiums.show', $stadium->id)
            ->with('success', 'Cập nhật sân con thành công.');
    }

    public function destroyField(Stadium $stadium, Field $field)
    {
        if ($field->stadium_id !== $stadium->id) {
            abort(404);
        }

        $field->delete();

        return redirect()->route('admin.stadiums.show', $stadium->id)
            ->with('success', 'Xóa sân con thành công.');
    }

    public function prices($id)
    {
        $stadium = Stadium::findOrFail($id);

        $timeSlots = TimeSlot::where('status', true)->orderBy('start_time')->get();

        $existing = StadiumTimeSlotPrice::where('stadium_id', $stadium->id)
            ->pluck('price', 'time_slot_id')
            ->toArray();

        $customSlots = StadiumSpecialTimeSlot::where('stadium_id', $stadium->id)
            ->orderBy('start_time')
            ->get();

        return view('admin.stadiums.prices', compact('stadium', 'timeSlots', 'existing', 'customSlots'));
    }

    public function storeCustom(Request $request, $id)
    {
        $stadium = Stadium::findOrFail($id);

        $data = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price' => 'required',
        ]);

        StadiumSpecialTimeSlot::create([
            'stadium_id' => $stadium->id,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'price' => (float) preg_replace('/[^0-9.]/', '', (string) $data['price']),
        ]);

        return redirect(url('admin/stadiums/' . $stadium->id . '/prices'))->with('success', 'Thêm khung giờ đặc biệt thành công.');
    }

    public function destroyCustom($stadiumId, $slotId)
    {
        $slot = StadiumSpecialTimeSlot::where('stadium_id', $stadiumId)->where('id', $slotId)->firstOrFail();
        $slot->delete();

        return redirect(url('admin/stadiums/' . $stadiumId . '/prices'))->with('success', 'Xóa khung giờ đặc biệt thành công.');
    }

    public function storePrices(Request $request, $id)
    {
        $stadium = Stadium::findOrFail($id);

        $prices = $request->input('prices', []);

        foreach ($prices as $timeSlotId => $value) {
            $price = (float) preg_replace('/[^0-9.]/', '', (string) $value);

            StadiumTimeSlotPrice::updateOrCreate(
                ['stadium_id' => $stadium->id, 'time_slot_id' => $timeSlotId],
                ['price' => $price]
            );
        }

        return redirect(url('admin/stadiums/' . $stadium->id . '/prices'))
            ->with('success', 'Cập nhật giá theo khung giờ thành công.');
    }

    public function edit(Stadium $stadium)
    {
        return view('admin.stadiums.edit', compact('stadium'));
    }

    public function update(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|max:255',
            'open_time' => 'required',
            'close_time' => 'required',
            'description' => 'nullable|string',
        ]);

        if ($request->has('price')) {
            $data['price'] = (float) preg_replace('/[^0-9.]/', '', (string) $request->input('price'));
        }

        if ($request->hasFile('image')) {
            if ($stadium->image && Storage::disk('public')->exists($stadium->image)) {
                Storage::disk('public')->delete($stadium->image);
            }

            $data['image'] = $request->file('image')->store('stadiums', 'public');
        } else {
            $data['image'] = $stadium->image;
        }

        $stadium->update($data);

        return redirect()
            ->route('admin.stadiums.index')
            ->with('success', 'Cập nhật cơ sở sân thành công.');
    }

    public function destroy(Stadium $stadium)
    {
        if ($stadium->image && Storage::disk('public')->exists($stadium->image)) {
            Storage::disk('public')->delete($stadium->image);
        }

        $stadium->delete();

        return redirect()
            ->route('admin.stadiums.index')
            ->with('success', 'Xóa cơ sở sân thành công.');
    }
}
