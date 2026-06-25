<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Quản trị viên hệ thống',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nhân viên',
                'slug' => 'staff',
                'description' => 'Nhân viên quản lý sân',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Khách hàng',
                'slug' => 'customer',
                'description' => 'Khách hàng đặt sân',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('field_types')->insertOrIgnore([
            [
                'name' => 'Sân 5 người',
                'number_of_players' => 5,
                'description' => 'Loại sân dành cho 5 người mỗi đội',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sân 7 người',
                'number_of_players' => 7,
                'description' => 'Loại sân dành cho 7 người mỗi đội',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sân 11 người',
                'number_of_players' => 11,
                'description' => 'Loại sân tiêu chuẩn 11 người',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('time_slots')->insertOrIgnore([
            [
                'start_time' => '06:00:00',
                'end_time' => '07:30:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'start_time' => '07:30:00',
                'end_time' => '09:00:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'start_time' => '17:00:00',
                'end_time' => '18:30:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'start_time' => '18:30:00',
                'end_time' => '20:00:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'start_time' => '20:00:00',
                'end_time' => '21:30:00',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('payment_methods')->insertOrIgnore([
            [
                'name' => 'Tiền mặt',
                'code' => 'cash',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'VNPay',
                'code' => 'vnpay',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'MoMo',
                'code' => 'momo',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ZaloPay',
                'code' => 'zalopay',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}