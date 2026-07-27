<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 12, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('bookings', 'cancel_note')) {
                $table->text('cancel_note')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('bookings', 'refund_status')) {
                $table->string('refund_status', 50)->default('none')->after('cancel_note');
            }
            if (!Schema::hasColumn('bookings', 'refund_proof_image')) {
                $table->string('refund_proof_image', 255)->nullable()->after('refund_status');
            }
            if (!Schema::hasColumn('bookings', 'refund_proof_note')) {
                $table->string('refund_proof_note', 255)->nullable()->after('refund_proof_image');
            }
            if (!Schema::hasColumn('bookings', 'user_dispute_reason')) {
                $table->text('user_dispute_reason')->nullable()->after('refund_proof_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'refund_amount',
                'cancel_note',
                'refund_status',
                'refund_proof_image',
                'refund_proof_note',
                'user_dispute_reason'
            ]);
        });
    }
};