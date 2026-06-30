<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'price',
        'unit',
        'description',
        'status',
    ];

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }
}