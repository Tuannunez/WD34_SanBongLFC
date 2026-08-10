<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlotSurcharge extends Model
{
    protected $table = 'time_slot_surcharges';

    protected $fillable = [
        'time_slot_id',
        'name',
        'surcharge_amount',
        'type',
    ];

    protected $casts = [
        'surcharge_amount' => 'decimal:2',
    ];

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
