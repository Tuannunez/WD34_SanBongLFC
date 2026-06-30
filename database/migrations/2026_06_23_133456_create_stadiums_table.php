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
       Schema::create('stadiums', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('image');
    $table->string('phone')->nullable();   // Số điện thoại
    $table->string('email')->nullable();   // Email
    $table->string('address');
    $table->time('open_time');
    $table->time('close_time');
    $table->decimal('rating', 2, 1)->default(5.0);
    $table->integer('price');
    $table->text('description')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stadiums');
    }
};
