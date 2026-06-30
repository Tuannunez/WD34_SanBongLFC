<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('id', 'desc')->paginate(10);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Vui lòng nhập tên dịch vụ.',
            'price.required' => 'Vui lòng nhập giá dịch vụ.',
            'price.numeric' => 'Giá dịch vụ phải là số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        Service::create([
            'name' => $request->name,
            'price' => $request->price,
            'unit' => $request->unit,
            'description' => $request->description,
            'status' => $request->status,
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

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:0,1',
        ], [
            'name.required' => 'Vui lòng nhập tên dịch vụ.',
            'price.required' => 'Vui lòng nhập giá dịch vụ.',
            'price.numeric' => 'Giá dịch vụ phải là số.',
            'status.required' => 'Vui lòng chọn trạng thái.',
        ]);

        $service->update([
            'name' => $request->name,
            'price' => $request->price,
            'unit' => $request->unit,
            'description' => $request->description,
            'status' => $request->status,
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