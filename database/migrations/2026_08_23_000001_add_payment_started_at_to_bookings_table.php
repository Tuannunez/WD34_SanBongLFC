<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookings') || Schema::hasColumn('bookings', 'payment_started_at')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table): void {
            $table->timestamp('payment_started_at')
                ->nullable()
                ->after('created_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('bookings') || ! Schema::hasColumn('bookings', 'payment_started_at')) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table): void {
            $table->dropColumn('payment_started_at');
        });
    }
};
