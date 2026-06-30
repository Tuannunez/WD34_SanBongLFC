<?php

namespace Database\Seeders;

use App\Models\Stadium;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    public function run(): void
    {
        Stadium::create([
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
]);
    }
}   