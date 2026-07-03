<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $totalFields = $this->countTable('fields');

        $todayBookings = Schema::hasTable('bookings')
            ? DB::table('bookings')->whereDate('created_at', today())->count()
            : 0;

        $monthlyRevenue = $this->getMonthlyRevenue();

        $totalCustomers = $this->getTotalCustomers();

        $totalStadiums = $this->countTable('stadiums');
        $totalFieldTypes = $this->countTable('field_types');
        $totalTimeSlots = $this->countTable('time_slots');
        $totalServices = $this->countTable('services');

        $latestBookings = $this->getLatestBookings();

        return view('admin.dashboard', compact(
            'totalFields',
            'todayBookings',
            'monthlyRevenue',
            'totalCustomers',
            'totalStadiums',
            'totalFieldTypes',
            'totalTimeSlots',
            'totalServices',
            'latestBookings'
        ));
    }

    private function countTable(string $table): int
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : 0;
    }

    private function getTotalCustomers(): int
    {
        if (!Schema::hasTable('users')) {
            return 0;
        }

        $query = DB::table('users');

        if (Schema::hasColumn('users', 'role')) {
            $query->whereNotIn('role', ['admin', 'super_admin']);
        }

        return $query->count();
    }

    private function getMonthlyRevenue(): float
    {
        $now = now();

        if (Schema::hasTable('payments')) {
            $amountColumn = $this->firstExistingColumn('payments', [
                'amount',
                'total_amount',
                'paid_amount',
            ]);

            if ($amountColumn) {
                $query = DB::table('payments');

                if (Schema::hasColumn('payments', 'created_at')) {
                    $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                }

                return (float) $query->sum($amountColumn);
            }
        }

        if (Schema::hasTable('bookings')) {
            $amountColumn = $this->firstExistingColumn('bookings', [
                'total_amount',
                'total_price',
                'amount',
            ]);

            if ($amountColumn) {
                $query = DB::table('bookings');

                if (Schema::hasColumn('bookings', 'created_at')) {
                    $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                }

                return (float) $query->sum($amountColumn);
            }
        }

        if (Schema::hasTable('booking_details')) {
            $amountColumn = $this->firstExistingColumn('booking_details', [
                'price',
                'total_price',
                'amount',
                'subtotal',
            ]);

            if ($amountColumn) {
                $query = DB::table('booking_details');

                if (Schema::hasColumn('booking_details', 'created_at')) {
                    $query->whereMonth('created_at', $now->month)
                        ->whereYear('created_at', $now->year);
                }

                return (float) $query->sum($amountColumn);
            }
        }

        return 0;
    }

    private function getLatestBookings()
    {
        if (!Schema::hasTable('bookings')) {
            return collect();
        }

        $query = DB::table('bookings');

        $select = ['bookings.id'];

        if (Schema::hasColumn('bookings', 'status')) {
            $select[] = 'bookings.status';
        }

        if (Schema::hasColumn('bookings', 'created_at')) {
            $select[] = 'bookings.created_at';
        }

        if (Schema::hasColumn('bookings', 'total_amount')) {
            $select[] = 'bookings.total_amount';
        }

        if (
            Schema::hasColumn('bookings', 'user_id') &&
            Schema::hasTable('users')
        ) {
            $query->leftJoin('users', 'bookings.user_id', '=', 'users.id');

            if (Schema::hasColumn('users', 'name')) {
                $select[] = 'users.name as user_name';
            }

            if (Schema::hasColumn('users', 'email')) {
                $select[] = 'users.email as user_email';
            }
        }

        $bookings = $query
            ->select($select)
            ->orderByDesc(Schema::hasColumn('bookings', 'created_at') ? 'bookings.created_at' : 'bookings.id')
            ->limit(8)
            ->get();

        return $bookings->map(function ($booking) {
            $booking->field_name = 'Chưa có sân';
            $booking->booking_date = $booking->created_at ?? null;
            $booking->display_total = $booking->total_amount ?? 0;

            if (!Schema::hasTable('booking_details')) {
                return $booking;
            }

            $dateColumn = $this->firstExistingColumn('booking_details', [
                'booking_date',
                'date',
                'play_date',
                'created_at',
            ]);

            $priceColumn = $this->firstExistingColumn('booking_details', [
                'price',
                'total_price',
                'amount',
                'subtotal',
            ]);

            $detailQuery = DB::table('booking_details')
                ->where('booking_id', $booking->id);

            if (
                Schema::hasColumn('booking_details', 'field_id') &&
                Schema::hasTable('fields') &&
                Schema::hasColumn('fields', 'name')
            ) {
                $fieldNames = DB::table('booking_details')
                    ->leftJoin('fields', 'booking_details.field_id', '=', 'fields.id')
                    ->where('booking_details.booking_id', $booking->id)
                    ->whereNotNull('fields.name')
                    ->pluck('fields.name')
                    ->unique()
                    ->implode(', ');

                if ($fieldNames) {
                    $booking->field_name = $fieldNames;
                }
            }

            if ($dateColumn) {
                $dateValue = DB::table('booking_details')
                    ->where('booking_id', $booking->id)
                    ->orderBy($dateColumn)
                    ->value($dateColumn);

                if ($dateValue) {
                    $booking->booking_date = $dateValue;
                }
            }

            if (!$booking->display_total && $priceColumn) {
                $booking->display_total = (float) $detailQuery->sum($priceColumn);
            }

            return $booking;
        });
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }
}