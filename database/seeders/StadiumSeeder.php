<?php

namespace Database\Seeders;

use App\Models\Stadium;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    public function run(): void
    {
        Stadium::insert([
            [
                'name' => 'Sân LFC Mỹ Đình',
                'image' => 'https://picsum.photos/600/400?1',
                'address' => 'Mỹ Đình, Hà Nội',
                'open_time' => '05:00:00',
                'close_time' => '23:00:00',
                'rating' => 4.9,
                'price' => 300000,
                'description' => 'Sân cỏ nhân tạo chất lượng cao',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sân LFC Cầu Giấy',
                'image' => 'https://picsum.photos/600/400?2',
                'address' => 'Cầu Giấy, Hà Nội',
                'open_time' => '06:00:00',
                'close_time' => '22:30:00',
                'rating' => 4.8,
                'price' => 350000,
                'description' => 'Sân 7 người đạt chuẩn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sân LFC Thanh Xuân',
                'image' => 'https://picsum.photos/600/400?3',
                'address' => 'Thanh Xuân, Hà Nội',
                'open_time' => '05:30:00',
                'close_time' => '23:30:00',
                'rating' => 4.7,
                'price' => 280000,
                'description' => 'Sân bóng có mái che',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sân LFC Hoàng Mai',
                'image' => 'https://picsum.photos/600/400?4',
                'address' => 'Hoàng Mai, Hà Nội',
                'open_time' => '05:00:00',
                'close_time' => '22:00:00',
                'rating' => 4.6,
                'price' => 250000,
                'description' => 'Sân bóng giá tốt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sân LFC Long Biên',
                'image' => 'https://picsum.photos/600/400?5',
                'address' => 'Long Biên, Hà Nội',
                'open_time' => '06:00:00',
                'close_time' => '23:00:00',
                'rating' => 5.0,
                'price' => 400000,
                'description' => 'Sân VIP chất lượng cao',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}