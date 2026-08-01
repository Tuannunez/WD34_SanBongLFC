<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InspectBookingSchema extends Command
{
    protected $signature = 'bookings:inspect-schema';

    protected $description = 'Kiểm tra cấu trúc và thống kê trạng thái của bảng bookings';

    public function handle(): int
    {
        if (! Schema::hasTable('bookings')) {
            $this->error('Không tìm thấy bảng bookings.');

            return self::FAILURE;
        }

        $requiredColumns = [
            'status',
            'usage_status',
            'payment_status',
            'payment_type',
            'paid_amount',
            'is_deposit_paid',
            'hold_expires_at',
            'checked_in_at',
            'checked_out_at',
            'completed_at',
            'cancelled_at',
            'refund_status',
            'refund_amount',
        ];

        $rows = collect($requiredColumns)
            ->map(fn (string $column): array => [
                'Cột' => $column,
                'Kết quả' => Schema::hasColumn('bookings', $column) ? 'OK' : 'THIẾU',
            ])
            ->all();

        $this->table(['Cột', 'Kết quả'], $rows);

        $missing = collect($requiredColumns)
            ->reject(fn (string $column): bool => Schema::hasColumn('bookings', $column));

        if ($missing->isNotEmpty()) {
            $this->error('Database còn thiếu cột: '.$missing->implode(', '));

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Trạng thái đơn hàng');
        $this->table(
            ['status', 'số lượng'],
            DB::table('bookings')
                ->select('status', DB::raw('COUNT(*) AS total'))
                ->groupBy('status')
                ->orderBy('status')
                ->get()
                ->map(fn ($row): array => [(string) $row->status, (int) $row->total])
                ->all()
        );

        $this->info('Trạng thái sử dụng sân');
        $this->table(
            ['usage_status', 'số lượng'],
            DB::table('bookings')
                ->select('usage_status', DB::raw('COUNT(*) AS total'))
                ->groupBy('usage_status')
                ->orderBy('usage_status')
                ->get()
                ->map(fn ($row): array => [(string) $row->usage_status, (int) $row->total])
                ->all()
        );

        $this->info('Trạng thái thanh toán');
        $this->table(
            ['payment_status', 'số lượng'],
            DB::table('bookings')
                ->select('payment_status', DB::raw('COUNT(*) AS total'))
                ->groupBy('payment_status')
                ->orderBy('payment_status')
                ->get()
                ->map(fn ($row): array => [(string) $row->payment_status, (int) $row->total])
                ->all()
        );

        if (! Schema::hasTable('booking_status_histories')) {
            $this->error('Thiếu bảng booking_status_histories.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Bước 2 đạt yêu cầu: schema trạng thái đã sẵn sàng.');

        return self::SUCCESS;
    }
}
