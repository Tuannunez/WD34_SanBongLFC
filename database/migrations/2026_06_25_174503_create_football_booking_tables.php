<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Vai trò
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Admin, Nhân viên, Khách hàng
            $table->string('slug')->unique(); // admin, staff, customer
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 2. Cơ sở sân bóng
        Schema::create('stadiums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. Loại sân
        Schema::create('field_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Sân 5, sân 7, sân 11
            $table->integer('number_of_players')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 4. Sân bóng
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stadium_id')->constrained('stadiums')->cascadeOnDelete();
            $table->foreignId('field_type_id')->constrained('field_types')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price_per_hour', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 5. Hình ảnh sân
        Schema::create('field_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->string('image_path');
            $table->boolean('is_main')->default(false);
            $table->timestamps();
        });

        // 6. Khung giờ
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 7. Khuyến mãi
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('discount_type', ['percent', 'fixed'])->default('fixed');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('min_order_amount', 12, 2)->default(0);
            $table->integer('quantity')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 8. Đơn đặt sân
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained('promotions')->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();

            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);

            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->text('note')->nullable();
            $table->timestamps();
        });

        // 9. Chi tiết đặt sân
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->foreignId('time_slot_id')->constrained('time_slots')->cascadeOnDelete();

            $table->date('booking_date');
            $table->decimal('price', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(['field_id', 'time_slot_id', 'booking_date'], 'unique_field_time_date');
        });

        // 10. Dịch vụ
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nước, áo, bóng...
            $table->decimal('price', 12, 2)->default(0);
            $table->string('unit')->nullable(); // chai, bộ, quả...
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 11. Chi tiết dịch vụ đặt sân
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();

            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();
        });

        // 12. Phương thức thanh toán
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tiền mặt, VNPay, MoMo
            $table->string('code')->unique(); // cash, vnpay, momo
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 13. Thanh toán
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->cascadeOnDelete();

            $table->decimal('amount', 12, 2)->default(0);
            $table->string('transaction_code')->nullable();

            $table->enum('status', [
                'unpaid',
                'paid',
                'failed',
                'refunded'
            ])->default('unpaid');

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // 14. Đánh giá
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();

            $table->tinyInteger('rating')->default(5);
            $table->text('comment')->nullable();
            $table->boolean('status')->default(true);

            $table->timestamps();
        });

        // 15. Thông báo
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->text('content');
            $table->string('type')->nullable(); // booking, payment, system
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('booking_services');
        Schema::dropIfExists('services');
        Schema::dropIfExists('booking_details');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('field_images');
        Schema::dropIfExists('fields');
        Schema::dropIfExists('field_types');
        Schema::dropIfExists('stadiums');
        Schema::dropIfExists('roles');
    }
};