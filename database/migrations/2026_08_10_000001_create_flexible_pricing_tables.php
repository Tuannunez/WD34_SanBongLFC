<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm bảng để quản lý giá cơ bản theo loại sân và các khoản phụ phí
     */
    public function up(): void
    {
        // Bảng giá cơ bản theo loại sân
        Schema::create('field_type_base_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_type_id')->constrained('field_types')->cascadeOnDelete();
            $table->decimal('base_price', 12, 2);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique('field_type_id');
        });

        // Bảng quản lý các khoản phụ phí theo khung giờ
        Schema::create('time_slot_surcharges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();
            $table->string('name'); // Ví dụ: "Phụ phí giờ tối", "Phụ phí cuối tuần"
            $table->decimal('surcharge_amount', 12, 2);
            $table->string('type')->default('fixed'); // fixed = số tiền cố định, percentage = phần trăm
            $table->timestamps();
        });

        // Mở rộng bảng time_slots để lưu thêm thông tin
        if (!Schema::hasColumn('time_slots', 'duration_minutes')) {
            Schema::table('time_slots', function (Blueprint $table) {
                $table->integer('duration_minutes')->default(90)->after('end_time');
                $table->string('name')->nullable()->after('duration_minutes');
                $table->boolean('is_peak_hour')->default(false)->after('name');
                $table->boolean('is_evening')->default(false)->after('is_peak_hour');
                $table->decimal('peak_hour_surcharge', 12, 2)->nullable()->after('is_evening');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_slot_surcharges');
        Schema::dropIfExists('field_type_base_prices');

        if (Schema::hasColumn('time_slots', 'duration_minutes')) {
            Schema::table('time_slots', function (Blueprint $table) {
                $table->dropColumn(['duration_minutes', 'name', 'is_peak_hour', 'is_evening', 'peak_hour_surcharge']);
            });
        }
    }
};
