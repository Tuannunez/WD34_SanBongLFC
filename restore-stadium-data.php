<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('booking_details')->delete();
DB::table('bookings')->delete();
DB::table('fields')->delete();
DB::table('stadiums')->delete();
DB::table('time_slots')->delete();
DB::table('field_types')->delete();

DB::table('field_types')->insert([
    ['id' => 1, 'name' => 'Sân 7', 'number_of_players' => 7, 'description' => 'Sân 7', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['id' => 2, 'name' => 'Sân 9', 'number_of_players' => 9, 'description' => 'Sân 9', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['id' => 3, 'name' => 'Sân 11', 'number_of_players' => 11, 'description' => 'Sân 11', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
]);

$stadiumId = DB::table('stadiums')->insertGetId([
    'name' => 'Sân bóng LFC Football Center',
    'image' => 'stadiums/demo.jpg',
    'phone' => '0988888888',
    'email' => 'contact@lfc.vn',
    'address' => '123 Nguyễn Trãi, Hà Nội',
    'open_time' => '06:00:00',
    'close_time' => '22:00:00',
    'rating' => 4.9,
    'price' => 300000,
    'description' => 'Sân cỏ nhân tạo chất lượng cao.',
    'created_at' => now(),
    'updated_at' => now(),
]);

DB::table('fields')->insert([
    ['stadium_id' => $stadiumId, 'field_type_id' => 1, 'name' => 'Sân 7 A', 'price_per_hour' => 300000, 'description' => 'Sân 7 người', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['stadium_id' => $stadiumId, 'field_type_id' => 2, 'name' => 'Sân 9 B', 'price_per_hour' => 350000, 'description' => 'Sân 9 người', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
]);

$slots = [
    ['start_time' => '06:00:00', 'end_time' => '07:30:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '07:30:00', 'end_time' => '09:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '09:00:00', 'end_time' => '10:30:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '10:30:00', 'end_time' => '12:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '12:00:00', 'end_time' => '13:30:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '13:30:00', 'end_time' => '15:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '15:00:00', 'end_time' => '16:30:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '16:30:00', 'end_time' => '18:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '18:00:00', 'end_time' => '19:30:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['start_time' => '19:30:00', 'end_time' => '21:00:00', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
];

DB::table('time_slots')->insert($slots);

echo 'restored' . PHP_EOL;
echo 'stadiums=' . DB::table('stadiums')->count() . PHP_EOL;
echo 'fields=' . DB::table('fields')->count() . PHP_EOL;
