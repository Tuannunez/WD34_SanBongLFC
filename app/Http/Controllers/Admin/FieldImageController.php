<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;

class FieldImageController extends Controller
{
    public function index()
    {
        $fields = Field::query()
            ->with([
                'stadium:id,name',
                'images' => fn ($query) => $query->orderByDesc('is_main')->orderByDesc('id'),
            ])
            ->orderBy('stadium_id')
            ->orderBy('name')
            ->get();

        return view('admin.field-images.index', compact('fields'));
    }
}
