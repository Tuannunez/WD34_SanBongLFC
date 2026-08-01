<?php

namespace App\Services\Bookings;

use App\Models\Booking;
use App\Support\DatabaseSchemaInspector;
use App\ValueObjects\Bookings\BookingScheduleWindow;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

final class BookingScheduleResolver
{
    public function __construct(
        private readonly DatabaseSchemaInspector $schema,
    ) {
    }

    public function resolve(Booking $booking): ?BookingScheduleWindow
    {
        $windows = $this->detailWindows($booking);

        if ($windows === []) {
            $fallback = $this->makeWindow(
                $booking->getAttribute('booking_date') ?? $booking->getAttribute('date'),
                $booking->getAttribute('start_time'),
                $booking->getAttribute('end_time'),
            );

            return $fallback;
        }

        $starts = array_map(
            fn (BookingScheduleWindow $window): CarbonImmutable => $window->startsAt,
            $windows,
        );
        $ends = array_map(
            fn (BookingScheduleWindow $window): CarbonImmutable => $window->endsAt,
            $windows,
        );

        usort($starts, fn (CarbonImmutable $a, CarbonImmutable $b): int => $a <=> $b);
        usort($ends, fn (CarbonImmutable $a, CarbonImmutable $b): int => $a <=> $b);

        return new BookingScheduleWindow(
            $starts[0],
            $ends[array_key_last($ends)],
        );
    }

    /**
     * @return list<BookingScheduleWindow>
     */
    private function detailWindows(Booking $booking): array
    {
        if (! $this->schema->tableExists('booking_details')) {
            return [];
        }

        $query = DB::table('booking_details as bd')
            ->where('bd.booking_id', $booking->getKey());

        $columns = ['bd.id'];

        foreach (['booking_date', 'date', 'start_time', 'end_time'] as $column) {
            if ($this->schema->columnExists('booking_details', $column)) {
                $columns[] = 'bd.'.$column;
            }
        }

        $canJoinTimeSlots = $this->schema->tableExists('time_slots')
            && $this->schema->columnExists('booking_details', 'time_slot_id');

        if ($canJoinTimeSlots) {
            $query->leftJoin('time_slots as ts', 'bd.time_slot_id', '=', 'ts.id');

            if ($this->schema->columnExists('time_slots', 'start_time')) {
                $columns[] = 'ts.start_time as slot_start_time';
            }

            if ($this->schema->columnExists('time_slots', 'end_time')) {
                $columns[] = 'ts.end_time as slot_end_time';
            }
        }

        $rows = $query->select($columns)->get();
        $windows = [];

        foreach ($rows as $row) {
            $window = $this->makeWindow(
                $row->booking_date
                    ?? $row->date
                    ?? $booking->getAttribute('booking_date')
                    ?? $booking->getAttribute('date'),
                $row->start_time
                    ?? $row->slot_start_time
                    ?? $booking->getAttribute('start_time'),
                $row->end_time
                    ?? $row->slot_end_time
                    ?? $booking->getAttribute('end_time'),
            );

            if ($window !== null) {
                $windows[] = $window;
            }
        }

        return $windows;
    }

    private function makeWindow(mixed $date, mixed $startTime, mixed $endTime): ?BookingScheduleWindow
    {
        $dateString = $this->dateString($date);

        if ($dateString === null || $startTime === null || $endTime === null) {
            return null;
        }

        try {
            $timezone = (string) config(
                'booking_lifecycle.timezone',
                config('app.timezone', 'Asia/Ho_Chi_Minh'),
            );

            $start = CarbonImmutable::parse(
                $dateString.' '.$this->normalizeTime($startTime),
                $timezone,
            );
            $end = CarbonImmutable::parse(
                $dateString.' '.$this->normalizeTime($endTime),
                $timezone,
            );

            // Hỗ trợ khung giờ qua nửa đêm, ví dụ 23:00 - 00:30.
            if ($end->lessThanOrEqualTo($start)) {
                $end = $end->addDay();
            }

            return new BookingScheduleWindow($start, $end);
        } catch (Throwable) {
            return null;
        }
    }

    private function dateString(mixed $date): ?string
    {
        if ($date instanceof DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        if ($date === null || trim((string) $date) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse((string) $date)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    private function normalizeTime(mixed $time): string
    {
        if ($time instanceof DateTimeInterface) {
            return $time->format('H:i:s');
        }

        $value = trim((string) $time);

        if (preg_match('/^\d{1,2}:\d{2}$/', $value) === 1) {
            return $value.':00';
        }

        return $value;
    }
}
