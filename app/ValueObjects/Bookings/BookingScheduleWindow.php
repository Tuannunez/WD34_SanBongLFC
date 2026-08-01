<?php

namespace App\ValueObjects\Bookings;

use Carbon\CarbonImmutable;

final readonly class BookingScheduleWindow
{
    public function __construct(
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
    ) {
    }
}
