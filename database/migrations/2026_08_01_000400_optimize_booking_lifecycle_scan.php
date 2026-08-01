<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX = 'bookings_lifecycle_scan_idx';

    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        foreach (['status', 'usage_status', 'payment_status'] as $column) {
            if (! Schema::hasColumn('bookings', $column)) {
                return;
            }
        }

        $database = DB::connection()->getDatabaseName();

        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'bookings')
            ->where('index_name', self::INDEX)
            ->exists();

        if ($exists) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table): void {
            $table->index(
                ['status', 'usage_status', 'payment_status'],
                self::INDEX,
            );
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        $database = DB::connection()->getDatabaseName();

        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', 'bookings')
            ->where('index_name', self::INDEX)
            ->exists();

        if (! $exists) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropIndex(self::INDEX);
        });
    }
};
