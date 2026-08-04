<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\Bookings\BookingLifecycleService;
use App\Services\Bookings\BookingScheduleResolver;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

final class SyncBookingLifecycle extends Command
{
    protected $signature = 'bookings:sync-lifecycle
        {--booking= : Chỉ đồng bộ một booking ID}
        {--at= : Giả lập thời điểm, ví dụ 2026-08-02 20:00:00}
        {--dry-run : Chỉ xem thay đổi dự kiến, không ghi database}
        {--debug : Hiển thị trạng thái và khung giờ đã đọc được}';

    protected $description = 'Tự xử lý no-show và tự check-out đơn đã check-in khi hết giờ sân';

    public function handle(
        BookingLifecycleService $service,
        BookingScheduleResolver $resolver,
    ): int {
        try {
            $timezone = (string) config(
                'booking_lifecycle.timezone',
                config('app.timezone', 'Asia/Ho_Chi_Minh'),
            );

            $at = $this->option('at') !== null
                ? CarbonImmutable::parse((string) $this->option('at'), $timezone)
                : null;

            $bookingId = $this->option('booking') !== null
                ? (int) $this->option('booking')
                : null;

            if ((bool) $this->option('debug') && $bookingId !== null) {
                $booking = Booking::query()->find($bookingId);

                if ($booking === null) {
                    $this->error('Không tìm thấy booking ID '.$bookingId.'.');

                    return self::FAILURE;
                }

                $window = $resolver->resolve($booking);
                $current = $at ?? CarbonImmutable::now($timezone);

                $this->table(
                    ['Booking', 'Status', 'Usage', 'Payment', 'Hiện tại', 'Bắt đầu', 'Kết thúc'],
                    [[
                        $booking->id,
                        $booking->status,
                        $booking->usage_status,
                        $booking->payment_status,
                        $current->format('Y-m-d H:i:s'),
                        $window?->startsAt->format('Y-m-d H:i:s') ?? 'KHÔNG ĐỌC ĐƯỢC',
                        $window?->endsAt->format('Y-m-d H:i:s') ?? 'KHÔNG ĐỌC ĐƯỢC',
                    ]],
                );
            }

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
                $this->warn(
                    'Không đọc được lịch sân. Kiểm tra booking_date, start_time/end_time, '
                    .'slot_start_time/slot_end_time hoặc time_slots.',
                );
            }

            return $summary['errors'] > 0
                ? self::FAILURE
                : self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}