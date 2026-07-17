<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldImage extends Model
{
    protected $fillable = [
        'field_id',
        'image_path',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
