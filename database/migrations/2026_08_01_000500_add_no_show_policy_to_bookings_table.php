<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table): void {
            if (! Schema::hasColumn('bookings', 'no_show_payment_type')) {
                $table->string('no_show_payment_type', 20)
                    ->nullable()
                    ->after('no_show_at');
            }

            if (! Schema::hasColumn('bookings', 'no_show_grace_minutes')) {
                $table->unsignedSmallInteger('no_show_grace_minutes')
                    ->nullable()
                    ->after('no_show_payment_type');
            }

            if (! Schema::hasColumn('bookings', 'no_show_forfeited_amount')) {
                $table->decimal('no_show_forfeited_amount', 15, 2)
                    ->default(0)
                    ->after('no_show_grace_minutes');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table): void {
            foreach ([
                'no_show_forfeited_amount',
                'no_show_grace_minutes',
                'no_show_payment_type',
            ] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};