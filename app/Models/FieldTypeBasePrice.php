<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldTypeBasePrice extends Model
{
    protected $table = 'field_type_base_prices';

    protected $fillable = [
        'field_type_id',
        'base_price',
        'description',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }
}
