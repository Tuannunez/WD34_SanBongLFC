<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'status',
        'duration_minutes',
        'name',
        'is_peak_hour',
        'is_evening',
        'peak_hour_surcharge',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_peak_hour' => 'boolean',
        'is_evening' => 'boolean',
        'peak_hour_surcharge' => 'decimal:2',
    ];

    public function fieldTimeSlotPrices()
    {
        return $this->hasMany(FieldTimeSlotPrice::class);
    }

    public function stadiumTimeSlotPrices()
    {
        return $this->hasMany(StadiumTimeSlotPrice::class);
    }

    public function surcharges()
    {
        return $this->hasMany(TimeSlotSurcharge::class);
    }

    /**
     * Lấy tên khung giờ (Ví dụ: "8:00 - 9:30")
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: $this->formatTime($this->start_time) . ' - ' . $this->formatTime($this->end_time);
    }

    /**
     * Format time from HH:MM:SS to HH:MM
     */
    private function formatTime($time)
    {
        return substr($time, 0, 5);
    }
}
