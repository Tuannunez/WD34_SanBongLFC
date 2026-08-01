<?php

namespace App\Console\Commands;

use App\Services\Bookings\BookingLifecycleService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

class SyncBookingLifecycle extends Command
{
    protected $signature = 'bookings:sync-lifecycle
        {--booking= : Chỉ đồng bộ một booking ID}
        {--at= : Giả lập thời điểm, ví dụ 2026-08-01 18:15:00}
        {--dry-run : Chỉ xem thay đổi dự kiến, không ghi database}';

    protected $description = 'Tự hủy đơn quá hạn/no-show và tự check-out đơn đã check-in';

    public function handle(BookingLifecycleService $service): int
    {
        try {
            $at = $this->option('at') !== null
                ? CarbonImmutable::parse(
                    (string) $this->option('at'),
                    (string) config(
                        'booking_lifecycle.timezone',
                        config('app.timezone', 'Asia/Ho_Chi_Minh'),
                    ),
                )
                : null;

            $bookingId = $this->option('booking') !== null
                ? (int) $this->option('booking')
                : null;

            $summary = $service->synchronize(
                at: $at,
                bookingId: $bookingId,
                dryRun: (bool) $this->option('dry-run'),
            );

            if ($summary['actions'] !== []) {
                $this->table(
                    ['Booking', 'Mã đơn', 'Hành động', 'Từ', 'Sang', 'Lý do', 'Thời điểm'],
                    collect($summary['actions'])
                        ->take(100)
                        ->map(fn (array $action): array => [
                            $action['booking_id'],
                            $action['booking_code'],
                            $action['action'],
                            $action['from_status'] ?? '-',
                            $action['to_status'],
                            $action['reason'],
                            $action['occurred_at']->format('Y-m-d H:i:s'),
                        ])
                        ->all(),
                );
            }

            $this->table(
                [
                    'Đã quét',
                    'Thay đổi',
                    'Hủy chưa TT',
                    'Xác nhận',
                    'No-show',
                    'Check-in',
                    'Check-out',
                    'Hoàn tất',
                    'Thiếu lịch',
                    'Lỗi',
                ],
                [[
                    $summary['scanned'],
                    $summary['changed'],
                    $summary['cancelled'],
                    $summary['confirmed'],
                    $summary['no_show'],
                    $summary['checked_in'],
                    $summary['checked_out'],
                    $summary['completed'],
                    $summary['missing_schedule'],
                    $summary['errors'],
                ]],
            );

            if ((bool) $this->option('dry-run')) {
                $this->warn('DRY RUN: database chưa bị thay đổi.');
            }

            if ($summary['missing_schedule'] > 0) {
                $this->warn('Có đơn confirmed không xác định được ngày/giờ. Hãy kiểm tra booking_details và time_slots.');
            }

            return $summary['errors'] > 0 ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
