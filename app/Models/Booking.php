<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const USAGE_NOT_CHECKED_IN = 'not_checked_in';
    public const USAGE_CHECKED_IN = 'checked_in';
    public const USAGE_CHECKED_OUT = 'checked_out';

    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_DEPOSIT_PAID = 'deposit_paid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_PARTIALLY_REFUNDED = 'partially_refunded';
    public const PAYMENT_REFUNDED = 'refunded';

    public const REFUND_NONE = 'none';
    public const REFUND_PENDING = 'pending';
    public const REFUND_PROCESSED = 'processed';
    public const REFUND_CONFIRMED_BY_USER = 'confirmed_by_user';
    public const REFUND_DISPUTED = 'disputed';

    protected $table = 'bookings';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'deposit_forfeited_amount' => 'decimal:2',

            'is_deposit_paid' => 'boolean',

            'hold_expires_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'no_show_at' => 'datetime',
            'invoice_issued_at' => 'datetime',
        ];
    }

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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'booking_id')->latestOfMany();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class, 'booking_id');
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
