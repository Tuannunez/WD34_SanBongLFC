<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldTimeSlotPrice extends Model
{
    protected $table = 'field_time_slot_prices';

    protected $fillable = [
        'field_id',
        'time_slot_id',
        'price',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
