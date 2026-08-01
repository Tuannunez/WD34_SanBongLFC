<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            throw new RuntimeException(
                'Không tìm thấy bảng bookings. Hãy chạy migration tạo bảng bookings trước.'
            );
        }

        $addNoShowAt = ! Schema::hasColumn('bookings', 'no_show_at');
        $addDepositForfeitedAmount = ! Schema::hasColumn(
            'bookings',
            'deposit_forfeited_amount'
        );
        $addCheckInSource = ! Schema::hasColumn(
            'bookings',
            'check_in_source'
        );

        if (
            ! $addNoShowAt
            && ! $addDepositForfeitedAmount
            && ! $addCheckInSource
        ) {
            return;
        }

        Schema::table('bookings', function (Blueprint $table) use (
            $addNoShowAt,
            $addDepositForfeitedAmount,
            $addCheckInSource
        ): void {
            if ($addNoShowAt) {
                $table->timestamp('no_show_at')
                    ->nullable()
                    ->after('cancelled_at');
            }

            if ($addDepositForfeitedAmount) {
                $table->decimal(
                    'deposit_forfeited_amount',
                    15,
                    2
                )
                    ->default(0)
                    ->after('no_show_at');
            }

            if ($addCheckInSource) {
                $table->string('check_in_source', 32)
                    ->nullable()
                    ->after('checked_in_at');
            }
        });
    }

    public function down(): void
    {
        // Không tự xóa các cột đối soát để tránh mất dữ liệu no-show.
    }
};