<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    protected $fillable = [
        'name',
        'image',
        'phone',
        'email',
        'address',
        'open_time',
        'close_time',
        'rating',
        'price',
        'description',
        'field_type_id',
    ];

    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }
}
