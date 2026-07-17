<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetail::class, 'booking_id');
    }

    public function bookingServices(): HasMany
    {
        return $this->hasMany(BookingService::class, 'booking_id');
    }

    public function details(): HasMany
    {
        return $this->bookingDetails();
    }

    public function services(): HasMany
    {
        return $this->bookingServices();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
