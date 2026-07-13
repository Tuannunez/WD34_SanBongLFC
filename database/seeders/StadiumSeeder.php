<?php

namespace Database\Seeders;

use App\Models\Stadium;
use App\Models\Field;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    public function run(): void
    {
        Stadium::query()->delete();
        Field::query()->delete();

        $stadium = Stadium::create([
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
            'field_type_id' => 1,
        ]);

        // Tạo các sân bóng
        Field::create([
            'stadium_id' => $stadium->id,
            'field_type_id' => 1,
            'name' => 'Sân 9 - A',
            'price_per_hour' => 100000,
            'description' => 'Sân 9 người chất lượng tốt',
            'status' => true,
        ]);

        Field::create([
            'stadium_id' => $stadium->id,
            'field_type_id' => 1,
            'name' => 'Sân 7 - A',
            'price_per_hour' => 100000,
            'description' => 'Sân 7 người chất lượng tốt',
            'status' => true,
        ]);

        Field::create([
            'stadium_id' => $stadium->id,
            'field_type_id' => 2,
            'name' => 'Sân 7 - B',
            'price_per_hour' => 150000,
            'description' => 'Sân 7 người cỏ nhân tạo',
            'status' => true,
        ]);
        Field::create([
            'stadium_id' => $stadium->id,
            'field_type_id' => 2,
            'name' => 'Sân 7 - C',
            'price_per_hour' => 150000,
            'description' => 'Sân 7 người cỏ nhân tạo',
            'status' => true,
        ]);

        Field::create([
            'stadium_id' => $stadium->id,
            'field_type_id' => 3,
            'name' => 'Sân 11 - D',
            'price_per_hour' => 250000,
            'description' => 'Sân 11 người cỏ tự nhiên',
            'status' => true,
        ]);
    }
}
