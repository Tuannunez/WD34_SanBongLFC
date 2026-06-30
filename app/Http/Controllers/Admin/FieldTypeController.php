<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FieldType;
use Illuminate\Http\Request;

class FieldTypeController extends Controller
{
    public function index()
    {
        $fieldTypes = FieldType::latest()->get();

        return view('admin.field-types.index', compact('fieldTypes'));
    }

    public function create()
    {
        return view('admin.field-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'number_of_players' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $data['status'] = $request->boolean('status');

        FieldType::create($data);

        return redirect()
            ->route('admin.field-types.index')
            ->with('success', 'Loại sân đã được tạo.');
    }

    public function show(FieldType $fieldType)
    {
        return view('admin.field-types.show', compact('fieldType'));
    }

    public function edit(FieldType $fieldType)
    {
        return view('admin.field-types.edit', compact('fieldType'));
    }

    public function update(Request $request, FieldType $fieldType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'number_of_players' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $data['status'] = $request->boolean('status');

        $fieldType->update($data);

        return redirect()
            ->route('admin.field-types.index')
            ->with('success', 'Loại sân đã được cập nhật.');
    }

    public function destroy(FieldType $fieldType)
    {
        $fieldType->delete();

        return redirect()
            ->route('admin.field-types.index')
            ->with('success', 'Loại sân đã được xóa.');
    }
}
