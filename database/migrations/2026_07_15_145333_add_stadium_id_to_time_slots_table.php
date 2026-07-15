<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            $table->foreignId('stadium_id')->nullable()->after('id')->constrained('stadiums')->nullOnDelete();
        });

        // Duplicate existing global slots for each stadium and then remove globals
        $stadiums = \Illuminate\Support\Facades\DB::table('stadiums')->pluck('id')->toArray();

        if (!empty($stadiums)) {
            $globalSlots = \Illuminate\Support\Facades\DB::table('time_slots')->whereNull('stadium_id')->get();

            foreach ($stadiums as $stadiumId) {
                foreach ($globalSlots as $slot) {
                    \Illuminate\Support\Facades\DB::table('time_slots')->insert([
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                        'status' => $slot->status,
                        'stadium_id' => $stadiumId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Remove original global slots
            \Illuminate\Support\Facades\DB::table('time_slots')->whereNull('stadium_id')->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_slots', function (Blueprint $table) {
            if (Schema::hasColumn('time_slots', 'stadium_id')) {
                $table->dropForeign(['stadium_id']);
                $table->dropColumn('stadium_id');
            }
        });
    }
};
