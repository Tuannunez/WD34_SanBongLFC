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

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Field::class, 'stadium_id', 'field_id', 'id', 'id');
    }
}
