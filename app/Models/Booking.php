<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'user_id',
        'promotion_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'total_amount',
        'discount_amount',
        'final_amount',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}