<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stadiums', function (Blueprint $table) {
            $table->foreignId('field_type_id')
                ->nullable()
                ->after('image')
                ->constrained('field_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stadiums', function (Blueprint $table) {
            $table->dropForeign(['field_type_id']);
            $table->dropColumn('field_type_id');
        });
    }
};
