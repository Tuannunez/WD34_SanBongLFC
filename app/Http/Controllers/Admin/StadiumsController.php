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
        return view('admin.stadiums.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100', 'unique:stadiums,name', 'regex:/.*\S+.*/'],
            'price' => ['required', 'string', 'regex:/^-?\d+$/', function ($attribute, $value, $fail) {
                $number = intval($value);
                if ($number === 0) {
                    $fail('Giá sân phải lớn hơn 0.');
                } elseif ($number < 0) {
                    $fail('Giá sân không hợp lệ.');
                }
            }],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'phone' => ['required', 'regex:/^0(3|5|7|8|9)[0-9]{8}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'min:5', 'max:255', 'regex:/.*\S+.*/'],
            'open_time' => ['required', 'date_format:H:i'],
            'close_time' => ['required', 'date_format:H:i', 'after:open_time'],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Vui lòng nhập tên sân.',
            'name.min' => 'Tên sân phải có ít nhất 3 ký tự.',
            'name.max' => 'Tên sân tối đa 100 ký tự.',
            'name.unique' => 'Tên sân đã tồn tại.',
            'name.regex' => 'Tên sân không được chỉ chứa khoảng trắng.',
            'price.required' => 'Vui lòng nhập giá sân.',
            'price.regex' => 'Giá sân phải là số.',
            'image.image' => 'Ảnh phải là hình ảnh hợp lệ.',
            'image.mimes' => 'Ảnh không đúng định dạng.',
            'image.max' => 'Ảnh tối đa 5MB.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'email.email' => 'Email không đúng định dạng.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'address.min' => 'Địa chỉ phải có ít nhất 5 ký tự.',
            'address.max' => 'Địa chỉ tối đa 255 ký tự.',
            'address.regex' => 'Địa chỉ không được chỉ chứa khoảng trắng.',
            'open_time.required' => 'Vui lòng chọn giờ mở.',
            'open_time.date_format' => 'Giờ mở phải đúng định dạng HH:mm.',
            'close_time.required' => 'Vui lòng chọn giờ đóng.',
            'close_time.date_format' => 'Giờ đóng phải đúng định dạng HH:mm.',
            'close_time.after' => 'Giờ đóng phải sau giờ mở.',
            'description.max' => 'Mô tả không được vượt quá 1.000 ký tự.',
        ]);

        if ($request->filled('price')) {
            $data['price'] = (int) preg_replace('/[^0-9]/', '', (string) $request->input('price'));
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->getSize() < 2 * 1024 * 1024) {
                return back()->withInput()->withErrors(['image' => 'Ảnh tối thiểu 2MB.']);
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                return back()->withInput()->withErrors(['image' => 'Ảnh tối đa 5MB.']);
            }
            $data['image'] = $file->store('stadiums', 'public');
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
