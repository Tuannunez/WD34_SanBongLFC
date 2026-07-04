<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StadiumSpecialTimeSlot extends Model
{
    protected $table = 'stadium_special_time_slots';

    protected $fillable = [
        'stadium_id',
        'start_time',
        'end_time',
        'price',
    ];

    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }
}
