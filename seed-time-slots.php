<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::table('booking_details')->delete();
DB::table('time_slots')->delete();
DB::statement('SET FOREIGN_KEY_CHECKS=1');

$slots = [
    ['start_time' => '06:00:00', 'end_time' => '07:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '07:00:00', 'end_time' => '08:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '08:00:00', 'end_time' => '09:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '09:00:00', 'end_time' => '10:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '10:00:00', 'end_time' => '11:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '11:00:00', 'end_time' => '12:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '12:00:00', 'end_time' => '13:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '13:00:00', 'end_time' => '14:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '14:00:00', 'end_time' => '15:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '15:00:00', 'end_time' => '16:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
];

DB::table('time_slots')->insert($slots);
echo 'time_slots=' . DB::table('time_slots')->count() . PHP_EOL;
