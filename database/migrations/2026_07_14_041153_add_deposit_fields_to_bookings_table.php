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
        Schema::table('bookings', function (Blueprint $table) {
            // Thêm cột số tiền cọc (kiểu decimal để lưu tiền tệ chính xác) sau cột total_amount
            $table->decimal('deposit_amount', 15, 2)->nullable()->after('total_amount')->comment('Số tiền cần cọc (30%)');
            
            // Thêm cột đánh dấu đã thanh toán cọc hay chưa
            $table->boolean('is_deposit_paid')->default(false)->after('deposit_amount')->comment('Đã thanh toán cọc chưa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Xóa 2 cột khi rollback migration
            $table->dropColumn(['deposit_amount', 'is_deposit_paid']);
        });
    }
};