<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:sync-lifecycle')
    ->everyMinute()
    ->timezone((string) config(
        'booking_lifecycle.timezone',
        config('app.timezone', 'Asia/Ho_Chi_Minh'),
    ))
    ->withoutOverlapping(5);
