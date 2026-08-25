<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingCustomerPhoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_uses_user_phone_when_customer_phone_is_missing(): void
    {
        $user = User::factory()->create([
            'name' => 'User Test',
            'email' => 'user@test.com',
            'phone' => '0987654321',
            'role' => 'user',
            'status' => true,
        ]);

        $this->actingAs($user);

        $fieldTypeId = DB::table('field_types')->insertGetId([
            'name' => 'Sân 5',
            'number_of_players' => 5,
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $stadiumId = DB::table('stadiums')->insertGetId([
            'name' => 'Sân A',
            'image' => 'default.jpg',
            'address' => 'Địa chỉ A',
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
            'rating' => 5.0,
            'price' => 350000,
            'description' => 'Sân thử nghiệm',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $fieldId = DB::table('fields')->insertGetId([
            'stadium_id' => $stadiumId,
            'field_type_id' => $fieldTypeId,
            'name' => 'Sân 1',
            'price_per_hour' => 350000,
            'description' => 'Sân 1',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $timeSlotId = DB::table('time_slots')->insertGetId([
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post('/dat-san', [
            'stadium_id' => $stadiumId,
            'field_id' => $fieldId,
            'time_slot_id' => $timeSlotId,
            'booking_date' => now()->addDay()->format('d/m/Y'),
            'time_slot' => '08:00 - 09:00',
            'services' => [],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'customer_phone' => '0987654321',
        ]);
    }
}
