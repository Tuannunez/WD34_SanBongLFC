<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        if (Service::count() === 0) {
            Service::insertOrIgnore([
                [
                    'name' => 'Nước uống',
                    'price' => 10000,
                    'unit' => 'chai',
                    'description' => 'Nước suối, nước ngọt',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Thuê bóng',
                    'price' => 50000,
                    'unit' => 'trận',
                    'description' => 'Bóng thi đấu chất lượng',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Áo bib',
                    'price' => 20000,
                    'unit' => 'bộ',
                    'description' => 'Áo phân đội',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Thuê găng tay',
                    'price' => 30000,
                    'unit' => 'cặp',
                    'description' => 'Găng tay thủ môn',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Bãi gửi xe',
                    'price' => 5000,
                    'unit' => 'xe',
                    'description' => 'Gửi xe cho khách',
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        $services = Service::orderBy('id', 'desc')->paginate(10);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Vui lòng nhập tên dịch vụ.',
            'price.required' => 'Vui lòng nhập giá dịch vụ.',
            'price.numeric' => 'Giá dịch vụ phải là số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('services', 'public');
            $data['image'] = Storage::url($path);
        }

        Service::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'unit' => $data['unit'],
            'description' => $data['description'],
            'image' => $data['image'] ?? null,
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Thêm dịch vụ thành công.');
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);

        return view('admin.services.show', compact('service'));
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);

        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Vui lòng nhập tên dịch vụ.',
            'price.required' => 'Vui lòng nhập giá dịch vụ.',
            'price.numeric' => 'Giá dịch vụ phải là số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        if ($request->hasFile('image_file')) {
            if (! empty($service->image) && str_starts_with($service->image, '/storage/')) {
                $oldPath = ltrim(str_replace('/storage/', '', $service->image), '/');
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image_file')->store('services', 'public');
            $data['image'] = Storage::url($path);
        }

        $service->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'unit' => $data['unit'],
            'description' => $data['description'],
            'image' => $data['image'] ?? $service->image,
            'status' => $data['status'],
        ]);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Cập nhật dịch vụ thành công.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Xóa dịch vụ thành công.');
    }
}