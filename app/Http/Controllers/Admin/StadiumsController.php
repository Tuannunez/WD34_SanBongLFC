<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use App\Models\Field;
use App\Models\Service;
use App\Models\TimeSlot;
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
        $fieldTypes = FieldType::where('status', true)->orderBy('name')->get();

        return view('admin.stadiums.create', compact('fieldTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'field_type_id' => 'required|exists:field_types,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|max:255',
            'open_time' => 'required',
            'close_time' => 'required',
            'description' => 'nullable|string',
        ]);

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

        $fields = Field::where('stadium_id', $stadium->id)->get();

        $services = Service::query()->get();

        // Lấy và xử lý timeSlots
        $slots = TimeSlot::query()->where('status', true)->orderBy('start_time')->get();

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

        return view('admin.stadiums.show', compact(
            'stadium',
            'fields',
            'timeSlots',
            'services'
        ));
    }

    public function edit(Stadium $stadium)
    {
        $fieldTypes = FieldType::where('status', true)->orderBy('name')->get();

        return view('admin.stadiums.edit', compact('stadium', 'fieldTypes'));
    }

    public function update(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'field_type_id' => 'required|exists:field_types,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|max:255',
            'open_time' => 'required',
            'close_time' => 'required',
            'description' => 'nullable|string',
        ]);

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