<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::table('stadiums')->truncate();
DB::table('fields')->truncate();
DB::table('field_types')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1');

$types = [
    ['name' => 'Sân 5', 'number_of_players' => 5, 'description' => 'Sân 5 người', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sân 7', 'number_of_players' => 7, 'description' => 'Sân 7 người', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Sân 11', 'number_of_players' => 11, 'description' => 'Sân 11 người', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
];

DB::table('field_types')->insert($types);

$fieldTypes = DB::table('field_types')->orderBy('id')->get();
$type5 = $fieldTypes->first();
$type7 = $fieldTypes->skip(1)->first();
$type11 = $fieldTypes->skip(2)->first();

$stadiumId = DB::table('stadiums')->insertGetId([
    'name' => 'Sân bóng LFC Football Center',
    'image' => 'https://picsum.photos/900/500',
    'phone' => '0988888888',
    'email' => 'contact@lfc.vn',
    'address' => '123 Nguyễn Trãi, Thanh Xuân, Hà Nội',
    'open_time' => '05:00:00',
    'close_time' => '23:00:00',
    'rating' => 4.9,
    'price' => 300000,
    'description' => 'Sân cỏ nhân tạo chất lượng cao.',
    'field_type_id' => $type5->id,
    'created_at' => now(),
    'updated_at' => now(),
]);

DB::table('fields')->insert([
    ['stadium_id' => $stadiumId, 'field_type_id' => $type5->id, 'name' => 'Sân 5 - A', 'price_per_hour' => 100000, 'description' => 'Sân 5 người chất lượng tốt', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['stadium_id' => $stadiumId, 'field_type_id' => $type5->id, 'name' => 'Sân 5 - B', 'price_per_hour' => 100000, 'description' => 'Sân 5 người chất lượng tốt', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['stadium_id' => $stadiumId, 'field_type_id' => $type7->id, 'name' => 'Sân 7 - C', 'price_per_hour' => 150000, 'description' => 'Sân 7 người cỏ nhân tạo', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['stadium_id' => $stadiumId, 'field_type_id' => $type11->id, 'name' => 'Sân 11 - D', 'price_per_hour' => 250000, 'description' => 'Sân 11 người cỏ tự nhiên', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
]);

echo "Sample data created\n";
