<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StadiumTimeSlotPrice extends Model
{
    protected $table = 'stadium_time_slot_prices';

    protected $fillable = [
        'stadium_id',
        'time_slot_id',
        'price',
    ];

    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
