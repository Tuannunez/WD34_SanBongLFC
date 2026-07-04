<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stadium_time_slot_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stadium_id')->constrained('stadiums')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();
            $table->decimal('price', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['stadium_id', 'time_slot_id'], 'stadium_time_slot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stadium_time_slot_prices');
    }
};
