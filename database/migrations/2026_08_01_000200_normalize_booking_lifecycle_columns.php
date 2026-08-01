<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            throw new RuntimeException('Không tìm thấy bảng bookings. Hãy kiểm tra lại migration gốc trước khi chạy Bước 2.');
        }

        $this->addMissingColumns();
        $this->normalizeExistingValues();
    }

    /**
     * Chỉ thêm cột chưa tồn tại để tương thích với database hiện tại.
     */
    private function addMissingColumns(): void
    {
        $definitions = [
            'usage_status' => static fn (Blueprint $table) => $table
                ->string('usage_status', 30)
                ->default('not_checked_in'),

            'payment_status' => static fn (Blueprint $table) => $table
                ->string('payment_status', 30)
                ->default('unpaid'),

            'payment_type' => static fn (Blueprint $table) => $table
                ->string('payment_type', 30)
                ->nullable(),

            'paid_amount' => static fn (Blueprint $table) => $table
                ->decimal('paid_amount', 15, 2)
                ->default(0),

            'is_deposit_paid' => static fn (Blueprint $table) => $table
                ->boolean('is_deposit_paid')
                ->default(false),

            'hold_expires_at' => static fn (Blueprint $table) => $table
                ->timestamp('hold_expires_at')
                ->nullable(),

            'checked_in_at' => static fn (Blueprint $table) => $table
                ->timestamp('checked_in_at')
                ->nullable(),

            'checked_out_at' => static fn (Blueprint $table) => $table
                ->timestamp('checked_out_at')
                ->nullable(),

            'completed_at' => static fn (Blueprint $table) => $table
                ->timestamp('completed_at')
                ->nullable(),

            'cancelled_at' => static fn (Blueprint $table) => $table
                ->timestamp('cancelled_at')
                ->nullable(),

            'refund_status' => static fn (Blueprint $table) => $table
                ->string('refund_status', 30)
                ->default('none'),

            'refund_amount' => static fn (Blueprint $table) => $table
                ->decimal('refund_amount', 15, 2)
                ->default(0),
        ];

        foreach ($definitions as $column => $definition) {
            if (Schema::hasColumn('bookings', $column)) {
                continue;
            }

            Schema::table('bookings', function (Blueprint $table) use ($definition): void {
                $definition($table);
            });
        }
    }

    /**
     * Chuẩn hóa các giá trị cũ mà không tự suy đoán số tiền đã thanh toán.
     */
    private function normalizeExistingValues(): void
    {
        DB::table('bookings')
            ->where(function ($query): void {
                $query->whereNull('status')->orWhere('status', '');
            })
            ->update(['status' => 'pending']);

        DB::table('bookings')
            ->where('status', 'canceled')
            ->update(['status' => 'cancelled']);

        // Một số code cũ dùng "paid" như trạng thái đơn. Từ Bước 2,
        // thanh toán nằm ở payment_status; trạng thái đơn tương ứng là confirmed.
        DB::table('bookings')
            ->where('status', 'paid')
            ->update(['status' => 'confirmed']);

        DB::table('bookings')
            ->where(function ($query): void {
                $query->whereNull('usage_status')->orWhere('usage_status', '');
            })
            ->update(['usage_status' => 'not_checked_in']);

        DB::table('bookings')
            ->whereIn('usage_status', ['waiting', 'not_started'])
            ->update(['usage_status' => 'not_checked_in']);

        DB::table('bookings')
            ->where('usage_status', 'in_use')
            ->update(['usage_status' => 'checked_in']);

        // Dấu thời gian thực tế đáng tin cậy hơn chuỗi trạng thái cũ.
        DB::table('bookings')
            ->whereNotNull('checked_out_at')
            ->update(['usage_status' => 'checked_out']);

        DB::table('bookings')
            ->whereNull('checked_out_at')
            ->whereNotNull('checked_in_at')
            ->update(['usage_status' => 'checked_in']);

        // Đơn hoàn tất cũ được xem là đã kết thúc sử dụng sân.
        // Không tự tạo checked_in_at/checked_out_at vì database không có thời điểm thực tế đáng tin cậy.
        DB::table('bookings')
            ->where('status', 'completed')
            ->where('usage_status', 'not_checked_in')
            ->update(['usage_status' => 'checked_out']);

        DB::table('bookings')
            ->where(function ($query): void {
                $query->whereNull('payment_status')->orWhere('payment_status', '');
            })
            ->update(['payment_status' => 'unpaid']);

        DB::table('bookings')
            ->whereIn('payment_status', ['pending', 'not_paid'])
            ->update(['payment_status' => 'unpaid']);

        DB::table('bookings')
            ->whereIn('payment_status', ['completed', 'paid_full'])
            ->update(['payment_status' => 'paid']);

        // Code cũ chỉ đánh dấu is_deposit_paid=true nhưng không luôn ghi paid_amount.
        // Vì không đủ dữ liệu để biết đã trả 30% hay 100%, chỉ chuẩn hóa về deposit_paid.
        DB::table('bookings')
            ->where('is_deposit_paid', true)
            ->where('payment_status', 'unpaid')
            ->update(['payment_status' => 'deposit_paid']);

        DB::table('bookings')
            ->where(function ($query): void {
                $query->whereNull('refund_status')->orWhere('refund_status', '');
            })
            ->update(['refund_status' => 'none']);
    }

    public function down(): void
    {
        // Cố ý không xóa các cột trạng thái khi rollback để tránh mất dữ liệu đơn hàng.
        // Đây là migration bảo toàn dữ liệu. Nếu cần gỡ, phải tạo migration mới sau khi sao lưu database.
    }
};
