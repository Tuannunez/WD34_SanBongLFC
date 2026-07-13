<?php

namespace Tests\Feature;

use App\Models\Field;
use App\Models\FieldType;
use App\Models\Stadium;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_slot_availability_for_selected_field_and_date(): void
    {
        $stadium = Stadium::create([
            'name' => 'Sân Test',
            'image' => 'stadiums/test.jpg',
            'address' => 'Địa chỉ test',
            'open_time' => '06:00:00',
            'close_time' => '22:00:00',
            'rating' => 5.0,
            'price' => 300000,
            'description' => 'Sân test',
        ]);

        $fieldType = FieldType::create([
            'name' => 'Sân 7',
            'number_of_players' => 7,
            'description' => 'Sân 7',
            'status' => true,
        ]);

        $field = Field::create([
            'stadium_id' => $stadium->id,
            'field_type_id' => $fieldType->id,
            'name' => 'Sân A1',
            'price_per_hour' => 300000,
            'description' => 'Sân test',
            'status' => true,
        ]);

        $firstSlotId = DB::table('time_slots')->insertGetId([
            'start_time' => '06:00:00',
            'end_time' => '07:00:00',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $secondSlotId = DB::table('time_slots')->insertGetId([
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $bookingId = DB::table('bookings')->insertGetId([
            'booking_code' => 'BKTEST001',
            'user_id' => null,
            'customer_name' => 'Khách test',
            'customer_phone' => '0123456789',
            'customer_email' => 'test@example.com',
            'total_amount' => 300000,
            'discount_amount' => 0,
            'final_amount' => 300000,
            'status' => 'pending',
            'note' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('booking_details')->insert([
            'booking_id' => $bookingId,
            'field_id' => $field->id,
            'time_slot_id' => $firstSlotId,
            'booking_date' => '2026-07-14',
            'price' => 300000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->getJson(route('user.bookings.availability', ['stadium' => $stadium->id]), [
            'field_id' => $field->id,
            'booking_date' => '2026-07-14',
        ]);

        $response->assertOk();
        $response->assertJsonPath('slots.0.available', false);
        $response->assertJsonPath('slots.1.available', true);
    }
}
