<?php

namespace Tests\Feature;

use App\Models\Stadium;
use App\Models\StadiumTimeSlotPrice;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTimeSlotManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_time_slot_and_its_price(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

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

        $timeSlot = TimeSlot::create([
            'start_time' => '06:00:00',
            'end_time' => '07:30:00',
            'status' => true,
        ]);

        StadiumTimeSlotPrice::create([
            'stadium_id' => $stadium->id,
            'time_slot_id' => $timeSlot->id,
            'price' => 300000,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.time-slots.update', [$stadium->id, $timeSlot->id]), [
                'start_time' => '08:00',
                'end_time' => '09:30',
                'price' => '150000',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('time_slots', [
            'id' => $timeSlot->id,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);
        $this->assertDatabaseHas('stadium_time_slot_prices', [
            'stadium_id' => $stadium->id,
            'time_slot_id' => $timeSlot->id,
            'price' => 150000,
        ]);
    }
}
