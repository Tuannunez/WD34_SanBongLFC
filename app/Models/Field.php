<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'stadium_id',
        'field_type_id',
        'name',
        'price_per_hour',
        'description',
        'status',
    ];

    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }

    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function images()
    {
        return $this->hasMany(FieldImage::class);
    }
}
