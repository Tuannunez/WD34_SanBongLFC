<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stadiums', 'price')) {
            Schema::table('stadiums', function (Blueprint $table) {
                $table->decimal('price', 12, 2)->nullable()->after('field_type_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stadiums', 'price')) {
            Schema::table('stadiums', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        }
    }
};
