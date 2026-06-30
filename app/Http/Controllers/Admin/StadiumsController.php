<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StadiumsController extends Controller
{
    public function index()
    {
        $stadiums = Stadium::latest()->get();

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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|max:255',
            'open_time' => 'required',
            'close_time' => 'required',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('stadiums', 'public');
        }

        Stadium::create($data);

        return redirect()
            ->route('admin.stadiums.index')
            ->with('success', 'Thêm cơ sở sân thành công.');
    }

    public function show(Stadium $stadium)
    {
        return view('admin.stadiums.show', compact('stadium'));
    }

    public function edit(Stadium $stadium)
    {
        return view('admin.stadiums.edit', compact('stadium'));
    }

    public function update(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|max:255',
            'open_time' => 'required',
            'close_time' => 'required',
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
