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
                $this->firstValue([
                    $booking->getAttribute('booking_date'),
                    $booking->getAttribute('date'),
                    $booking->getAttribute('play_date'),
                ]),
                $this->firstValue([
                    $booking->getAttribute('start_time'),
                    $booking->getAttribute('slot_start_time'),
                    $booking->getAttribute('booking_start_time'),
                    $booking->getAttribute('starts_at'),
                ]),
                $this->firstValue([
                    $booking->getAttribute('end_time'),
                    $booking->getAttribute('slot_end_time'),
                    $booking->getAttribute('booking_end_time'),
                    $booking->getAttribute('ends_at'),
                ]),
            );

            return $fallback;
        }

        usort(
            $windows,
            fn (BookingScheduleWindow $a, BookingScheduleWindow $b): int =>
                $a->startsAt <=> $b->startsAt,
        );

        $startsAt = $windows[0]->startsAt;
        $endsAt = $windows[0]->endsAt;

        foreach ($windows as $window) {
            if ($window->endsAt->greaterThan($endsAt)) {
                $endsAt = $window->endsAt;
            }
        }

        return new BookingScheduleWindow($startsAt, $endsAt);
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

        foreach ([
            'booking_date',
            'date',
            'play_date',
            'start_time',
            'end_time',
            'slot_start_time',
            'slot_end_time',
            'booking_start_time',
            'booking_end_time',
            'starts_at',
            'ends_at',
            'time_slot_id',
        ] as $column) {
            if ($this->schema->columnExists('booking_details', $column)) {
                $columns[] = 'bd.'.$column;
            }
        }

        $canJoinTimeSlots = $this->schema->tableExists('time_slots')
            && $this->schema->columnExists('booking_details', 'time_slot_id');

        if ($canJoinTimeSlots) {
            $query->leftJoin('time_slots as ts', 'bd.time_slot_id', '=', 'ts.id');

            foreach ([
                'start_time' => 'time_slot_start_time',
                'end_time' => 'time_slot_end_time',
                'slot_start_time' => 'time_slot_slot_start_time',
                'slot_end_time' => 'time_slot_slot_end_time',
                'starts_at' => 'time_slot_starts_at',
                'ends_at' => 'time_slot_ends_at',
            ] as $column => $alias) {
                if ($this->schema->columnExists('time_slots', $column)) {
                    $columns[] = 'ts.'.$column.' as '.$alias;
                }
            }
        }

        $rows = $query->select($columns)->get();
        $windows = [];

        foreach ($rows as $row) {
            $window = $this->makeWindow(
                $this->firstValue([
                    $row->booking_date ?? null,
                    $row->date ?? null,
                    $row->play_date ?? null,
                    $booking->getAttribute('booking_date'),
                    $booking->getAttribute('date'),
                    $booking->getAttribute('play_date'),
                ]),
                $this->firstValue([
                    $row->start_time ?? null,
                    $row->slot_start_time ?? null,
                    $row->booking_start_time ?? null,
                    $row->starts_at ?? null,
                    $row->time_slot_start_time ?? null,
                    $row->time_slot_slot_start_time ?? null,
                    $row->time_slot_starts_at ?? null,
                    $booking->getAttribute('start_time'),
                    $booking->getAttribute('slot_start_time'),
                    $booking->getAttribute('booking_start_time'),
                    $booking->getAttribute('starts_at'),
                ]),
                $this->firstValue([
                    $row->end_time ?? null,
                    $row->slot_end_time ?? null,
                    $row->booking_end_time ?? null,
                    $row->ends_at ?? null,
                    $row->time_slot_end_time ?? null,
                    $row->time_slot_slot_end_time ?? null,
                    $row->time_slot_ends_at ?? null,
                    $booking->getAttribute('end_time'),
                    $booking->getAttribute('slot_end_time'),
                    $booking->getAttribute('booking_end_time'),
                    $booking->getAttribute('ends_at'),
                ]),
            );

            if ($window !== null) {
                $windows[] = $window;
            }
        }

        return $windows;
    }

    private function makeWindow(
        mixed $date,
        mixed $startTime,
        mixed $endTime,
    ): ?BookingScheduleWindow {
        try {
            $timezone = (string) config(
                'booking_lifecycle.timezone',
                config('app.timezone', 'Asia/Ho_Chi_Minh'),
            );

            $start = $this->makeDateTime($date, $startTime, $timezone);
            $end = $this->makeDateTime($date, $endTime, $timezone);

            if ($start === null || $end === null) {
                return null;
            }

            // Hỗ trợ khung giờ qua nửa đêm, ví dụ 23:00 - 00:30.
            if ($end->lessThanOrEqualTo($start)) {
                $end = $end->addDay();
            }

            return new BookingScheduleWindow($start, $end);
        } catch (Throwable) {
            return null;
        }
    }

    private function makeDateTime(
        mixed $date,
        mixed $time,
        string $timezone,
    ): ?CarbonImmutable {
        if ($time === null || trim((string) $time) === '') {
            return null;
        }

        if ($time instanceof DateTimeInterface) {
            $time = $time->format('H:i:s');
        }

        $timeString = trim((string) $time);

        // Nếu cột đã chứa đầy đủ ngày giờ thì dùng trực tiếp.
        if (
            preg_match('/^\d{4}-\d{2}-\d{2}[ T]/', $timeString) === 1
            || preg_match('/^\d{2}\/\d{2}\/\d{4}\s+/', $timeString) === 1
        ) {
            return CarbonImmutable::parse($timeString, $timezone);
        }

        $dateString = $this->dateString($date);

        if ($dateString === null) {
            return null;
        }

        if (preg_match('/^\d{1,2}:\d{2}$/', $timeString) === 1) {
            $timeString .= ':00';
        }

        return CarbonImmutable::parse(
            $dateString.' '.$timeString,
            $timezone,
        );
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

    /**
     * @param  array<int, mixed>  $values
     */
    private function firstValue(array $values): mixed
    {
        foreach ($values as $value) {
            if ($value instanceof DateTimeInterface) {
                return $value;
            }

            if ($value !== null && trim((string) $value) !== '') {
                return $value;
            }
        }

        return null;
    }
}
