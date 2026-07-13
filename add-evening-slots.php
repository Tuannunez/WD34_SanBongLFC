<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$slots = [
    ['start_time' => '16:00:00', 'end_time' => '17:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '17:00:00', 'end_time' => '18:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '18:00:00', 'end_time' => '19:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '19:00:00', 'end_time' => '20:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '20:00:00', 'end_time' => '21:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '21:00:00', 'end_time' => '22:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
];

DB::table('time_slots')->insert($slots);
echo 'time_slots=' . DB::table('time_slots')->count() . PHP_EOL;
