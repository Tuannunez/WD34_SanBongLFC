<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldImage;
use App\Models\FieldType;
use App\Models\Stadium;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::with(['stadium', 'fieldType', 'images'])
            ->orderBy('stadium_id')
            ->orderBy('name')
            ->get();

        $stadiums = Stadium::query()->orderBy('name')->get();
        $fieldTypes = FieldType::query()->where('status', true)->orderBy('name')->get();

        return view('admin.fields.index', compact('fields', 'stadiums', 'fieldTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'stadium_id' => 'required|exists:stadiums,id',
            'name' => 'required|max:255',
            'field_type_id' => 'required|exists:field_types,id',
            'price_per_hour' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $data['status'] = (bool) ($request->input('status', 1));
        unset($data['image']);

        $field = Field::create($data);
        $this->storeMainImage($request, $field);

        return redirect()->route('admin.fields.index')
            ->with('success', 'Thêm sân con thành công.');
    }

    public function update(Request $request, Field $field)
    {
        $data = $request->validate([
            'stadium_id' => 'required|exists:stadiums,id',
            'name' => 'required|max:255',
            'field_type_id' => 'required|exists:field_types,id',
            'price_per_hour' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $data['status'] = (bool) $request->input('status', 1);
        unset($data['image']);

        $field->update($data);
        $this->storeMainImage($request, $field);

        return redirect()->route('admin.fields.index')
            ->with('success', 'Cập nhật sân con thành công.');
    }

    public function destroy(Field $field)
    {
        $field->delete();

        return redirect()->route('admin.fields.index')
            ->with('success', 'Xóa sân con thành công.');
    }

    private function storeMainImage(Request $request, Field $field): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        FieldImage::where('field_id', $field->id)->update(['is_main' => false]);

        FieldImage::create([
            'field_id' => $field->id,
            'image_path' => $request->file('image')->store('fields', 'public'),
            'is_main' => true,
        ]);
    }
}
