<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bookings', 'usage_status')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->string('usage_status', 32)
                    ->default('not_checked_in')
                    ->after('status');
            });
        }

        if (!Schema::hasColumn('bookings', 'checked_in_at')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->timestamp('checked_in_at')
                    ->nullable()
                    ->after('usage_status');
            });
        }

        if (!Schema::hasColumn('bookings', 'checked_out_at')) {
            Schema::table('bookings', function (Blueprint $table): void {
                $table->timestamp('checked_out_at')
                    ->nullable()
                    ->after('checked_in_at');
            });
        }
    }

    public function down(): void
    {
        $columns = [];

        foreach ([
            'usage_status',
            'checked_in_at',
            'checked_out_at',
        ] as $column) {
            if (Schema::hasColumn('bookings', $column)) {
                $columns[] = $column;
            }
        }

        if ($columns !== []) {
            Schema::table('bookings', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};