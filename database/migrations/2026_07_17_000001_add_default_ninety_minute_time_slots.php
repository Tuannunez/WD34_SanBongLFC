<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the standard 90-minute booking sessions.
     * Sessions starting at 18:00 are treated as evening sessions by the pricing logic.
     */
    public function up(): void
    {
        if (!Schema::hasTable('time_slots')) {
            return;
        }

        $slots = [
            ['06:00:00', '07:30:00'],
            ['07:30:00', '09:00:00'],
            ['09:00:00', '10:30:00'],
            ['10:30:00', '12:00:00'],
            ['13:30:00', '15:00:00'],
            ['15:00:00', '16:30:00'],
            ['16:30:00', '18:00:00'],
            ['18:00:00', '19:30:00'],
            ['19:30:00', '21:00:00'],
            ['21:00:00', '22:30:00'],
        ];

        foreach ($slots as [$startTime, $endTime]) {
            DB::table('time_slots')->updateOrInsert(
                [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ],
                [
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // Do not remove slots on rollback because they may already have bookings.
    }
};
