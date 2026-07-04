<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $guarded = []; // Cho phép mass-assign tất cả các trường đi kèm

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}