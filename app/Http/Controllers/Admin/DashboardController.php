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

         $monthlyTarget = 15000000;

    $monthlyPercent = $monthlyTarget > 0
        ? round(($monthlyRevenue / $monthlyTarget) * 100, 1)
        : 0;

    $totalCustomers = $this->getTotalCustomers();

        $totalCustomers = $this->getTotalCustomers();

        $totalStadiums = $this->countTable('stadiums');
        $totalFieldTypes = $this->countTable('field_types');
        $totalTimeSlots = $this->countTable('time_slots');
        $totalServices = $this->countTable('services');

        $latestBookings = $this->getLatestBookings(); 
        $bookingSpark = $this->getBookingSpark();
$revenueSpark = $this->getRevenueSpark();
$customerSpark = $this->getCustomerSpark();
$fieldSpark = $this->getFieldSpark();
        $growth = $this->getDashboardGrowth();
        

        $revenue7Days = $this->getRevenueChart(7);

$revenue30Days = $this->getRevenueChart(30);

$bookingStatusChart = $this->getBookingStatusChart();

$topFieldsChart = $this->getTopFieldsChart();

$topCustomers = $this->getTopCustomers();

$weeklyRevenueChart = $this->getWeeklyRevenueChart();

$fieldOccupancy = $this->getFieldOccupancy();

$monthlyBookingChart = $this->getMonthlyBookingChart();

$bookingThisMonth = $this->getBookingThisMonth();

$monthlyRevenueCard = $this->getMonthlyRevenueCard();

$newCustomers = $this->getNewCustomers();

$occupancyRate = $this->getOccupancyRate();

$bookingGrowth = $this->getBookingGrowth();

$revenueGrowth = $this->getRevenueGrowth();

$customerGrowth = $this->getCustomerGrowth();

$occupancyGrowth = $this->getOccupancyGrowth();

$quarter1Revenue = $this->getQuarterRevenue(1);

$quarter2Revenue = $this->getQuarterRevenue(2);

$quarter3Revenue = $this->getQuarterRevenue(3);




        return view('admin.dashboard', compact(

'totalFields',

'todayBookings',

'quarter1Revenue',

'quarter2Revenue',

'quarter3Revenue',

'monthlyRevenue',

'bookingGrowth',

'revenueGrowth',

'customerGrowth',

'occupancyGrowth',

'totalCustomers',

'totalStadiums',

'totalFieldTypes',

'totalTimeSlots',

'totalServices',

'revenue7Days',

'revenue30Days',

'bookingStatusChart',

'topFieldsChart',

'topCustomers',

'weeklyRevenueChart',

'fieldOccupancy',

'monthlyBookingChart',

'growth',

'bookingSpark',

'revenueSpark',

'customerSpark',

'fieldSpark',

'bookingThisMonth',

'monthlyRevenueCard',

'newCustomers',

'occupancyRate',

'latestBookings',

'monthlyTarget',

'monthlyPercent'

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

                $revenue = (float) $query->sum($amountColumn);

if ($revenue > 0) {
    return $revenue;
}
            }
        }

        if (Schema::hasTable('bookings')) {
            $amountColumn = $this->firstExistingColumn('bookings', [
                'total_amount',
                'total_price',
                'amount',
            ]);

            if ($amountColumn) {
                $query = DB::table('bookings')
    ->whereIn('bookings.status', ['confirmed', 'completed']);

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

    private function getRevenueChart($days = 30)
{
    $amountColumn = $this->firstExistingColumn('bookings',[
        'total_amount',
        'total_price',
        'amount'
    ]);

    if(!$amountColumn){
        return [
            'labels'=>[],
            'series'=>[]
        ];
    }

    $start = now()->subDays($days - 1);

   $bookings = DB::table('bookings')
    ->whereIn('bookings.status', ['confirmed', 'completed'])

        ->selectRaw("
            DATE(created_at) as date,
            SUM($amountColumn) as total
        ")

        ->whereDate('created_at','>=',$start)

        ->groupBy(DB::raw("DATE(created_at)"))

        ->orderBy('date')

        ->get();

    $labels=[];

    $series=[];

    for($i=0;$i<$days;$i++){

        $day=$start->copy()->addDays($i);

        $labels[]=$day->format('d/m');

        $value=$bookings
            ->firstWhere(
                'date',
                $day->format('Y-m-d')
            );

        $series[]=$value
            ? (float)$value->total
            :0;

    }

    return [

        'labels'=>$labels,

        'series'=>$series

    ];
}

    private function getMonthlyRevenueChart()
{
    $result = [];

    for ($month = 1; $month <= 12; $month++) {

        $result[] = DB::table('payments')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    return $result;
}

private function getBookingStatusChart()
{
    return [

        DB::table('bookings')
            ->where('status', 'confirmed')
            ->whereDate('created_at', today())
            ->count(),

        DB::table('bookings')
            ->where('status', 'pending')
            ->whereDate('created_at', today())
            ->count(),

        DB::table('bookings')
            ->where('status', 'cancelled')
            ->whereDate('created_at', today())
            ->count(),

    ];
}
private function getTopCustomers()
{
    return DB::table('bookings')
    ->whereIn('bookings.status', ['confirmed', 'completed'])

        ->join('users', 'bookings.user_id', '=', 'users.id')

        ->select(

            'users.name',

            DB::raw('COUNT(bookings.id) as total')

        )

        ->groupBy('users.id', 'users.name')

        ->orderByDesc('total')

        ->limit(5)

        ->get();
}

private function getTopFieldsChart()
{
    return DB::table('booking_details')

        ->join('fields', 'booking_details.field_id', '=', 'fields.id')

        ->select(

            'fields.name',

            DB::raw('COUNT(*) as total')

        )

        ->groupBy('fields.id', 'fields.name')

        ->orderByDesc('total')

        ->limit(5)

        ->get();
}

private function getWeeklyRevenueChart()
{
    $amountColumn = null;

    if (Schema::hasTable('payments')) {
        $amountColumn = $this->firstExistingColumn('payments', [
            'amount',
            'total_amount',
            'paid_amount'
        ]);
    }

    $labels = [];
    $data = [];

    for ($i = 6; $i >= 0; $i--) {

        $date = now()->subDays($i);

        $labels[] = $date->format('d/m');

        if ($amountColumn) {

            $data[] = DB::table('payments')
                ->whereDate('created_at', $date)
                ->sum($amountColumn);

        } else {

            $data[] = 0;

        }

    }

    return [

        'labels' => $labels,

        'data' => $data

    ];
}

private function getFieldOccupancy()
{
    if (
        !Schema::hasTable('booking_details') ||
        !Schema::hasTable('fields')
    ) {
        return collect();
    }

    return DB::table('booking_details')
        ->join('fields', 'booking_details.field_id', '=', 'fields.id')
        ->select(
            'fields.name',
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('fields.id', 'fields.name')
        ->orderByDesc('total')
        ->limit(6)
        ->get();
}

private function getMonthlyBookingChart()
{
    $result = [];

    for ($month = 1; $month <= 12; $month++) {

        $result[] = DB::table('bookings')

            ->whereYear('created_at', now()->year)

            ->whereMonth('created_at', $month)

            ->count();

    }

    return $result;
}

private function getDashboardGrowth()
{
    $today = now();

    $yesterday = now()->subDay();

    $thisMonth = now();

    $lastMonth = now()->subMonth();

    $todayBooking = DB::table('bookings')
        ->whereDate('created_at', $today)
        ->count();

    $yesterdayBooking = DB::table('bookings')
        ->whereDate('created_at', $yesterday)
        ->count();

    if ($yesterdayBooking == 0) {

    $bookingGrowth = $todayBooking > 0 ? 100 : 0;

} else {

    $bookingGrowth = round(
        (($todayBooking - $yesterdayBooking) / $yesterdayBooking) * 100
    );

}

    $amountColumn = $this->firstExistingColumn('bookings',[
        'total_amount',
        'total_price',
        'amount'
    ]);

    $thisRevenue = 0;

    $lastRevenue = 0;

    if($amountColumn){

    $thisRevenue = DB::table('bookings')
    ->whereIn('bookings.status', ['confirmed', 'completed'])
    ->whereMonth('created_at', $thisMonth->month)
    ->whereYear('created_at', $thisMonth->year)
    ->sum($amountColumn);

    $lastRevenue = DB::table('bookings')
    ->whereIn('bookings.status', ['confirmed', 'completed'])
    ->whereMonth('created_at', $lastMonth->month)
    ->whereYear('created_at', $lastMonth->year)
    ->sum($amountColumn);
    }

    if ($lastRevenue == 0) {

    $revenueGrowth = $thisRevenue > 0 ? 100 : 0;

} else {

    $revenueGrowth = round(
        (($thisRevenue - $lastRevenue) / $lastRevenue) * 100
    );

}

    return [

        'booking'=>$bookingGrowth,

        'revenue'=>$revenueGrowth

    ];
}

private function getBookingSpark()
{
    return DB::table('bookings')
        ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
        ->whereDate('created_at','>=',now()->subDays(11))
        ->groupBy('day')
        ->orderBy('day')
        ->pluck('total')
        ->values()
        ->toArray();
}

private function getRevenueSpark()
{
    return DB::table('bookings')
        ->whereIn('bookings.status', ['confirmed','completed'])
        ->selectRaw('DATE(created_at) as day, SUM(total_amount) as total')
        ->whereDate('created_at','>=',now()->subDays(11))
        ->groupBy('day')
        ->orderBy('day')
        ->pluck('total')
        ->map(fn($v)=>(float)$v)
        ->values()
        ->toArray();
}
private function getCustomerSpark()
{
    return DB::table('users')
        ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
        ->whereDate('created_at','>=',now()->subDays(11))
        ->groupBy('day')
        ->orderBy('day')
        ->pluck('total')
        ->values()
        ->toArray();
}

private function getFieldSpark()
{
    return DB::table('fields')
        ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
        ->whereDate('created_at','>=',now()->subDays(11))
        ->groupBy('day')
        ->orderBy('day')
        ->pluck('total')
        ->values()
        ->toArray();
}
private function getBookingThisMonth()
{
    return DB::table('bookings')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
}

private function getMonthlyRevenueCard()
{
    $amountColumn = $this->firstExistingColumn('bookings', [
        'total_amount',
        'total_price',
        'amount',
    ]);

    if (!$amountColumn) {
        return 0;
    }

    return DB::table('bookings')
        ->where('status', 'confirmed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum($amountColumn);
}

private function getNewCustomers()
{
    return DB::table('users')
        ->whereMonth('created_at', now()->month)
        ->count();
}

private function getOccupancyRate()
{
    $total = DB::table('bookings')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    if ($total == 0) {
        return 0;
    }

    $success = DB::table('bookings')
        ->where('status', 'confirmed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    return round(($success / $total) * 100);
}
private function getRevenueGrowth()
{
    $amountColumn = $this->firstExistingColumn('bookings', [
        'total_amount',
        'total_price',
        'amount'
    ]);

    if (!$amountColumn) {
        return 0;
    }

    $thisMonth = DB::table('bookings')
        ->where('status', 'confirmed')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum($amountColumn);

    $lastMonth = DB::table('bookings')
        ->where('status', 'confirmed')
        ->whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->sum($amountColumn);

    if ($lastMonth == 0) {
        return $thisMonth > 0 ? 100 : 0;
    }

    return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
}

private function getBookingGrowth()
{
    $thisMonth = DB::table('bookings')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $lastMonth = DB::table('bookings')
        ->whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->count();

    if ($lastMonth == 0) {
        return $thisMonth > 0 ? 100 : 0;
    }

    return round((($thisMonth - $lastMonth) / $lastMonth) * 100);
}

private function getCustomerGrowth()
{
    $thisMonth = DB::table('users')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $lastMonth = DB::table('users')
        ->whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->count();

    if ($lastMonth == 0) {
        return $thisMonth > 0 ? 100 : 0;
    }

    return round((($thisMonth - $lastMonth) / $lastMonth) * 100);
}

private function getOccupancyGrowth()
{
    $thisMonth = DB::table('bookings')
        ->where('status','confirmed')
        ->whereMonth('created_at',now()->month)
        ->count();

    $lastMonth = DB::table('bookings')
        ->where('status','confirmed')
        ->whereMonth('created_at',now()->subMonth()->month)
        ->count();

    if($lastMonth==0){
        return $thisMonth>0?100:0;
    }

    return round((($thisMonth-$lastMonth)/$lastMonth)*100);
}

private function getQuarterRevenue($quarter)
{
    $amountColumn = $this->firstExistingColumn('bookings', [
        'total_amount',
        'total_price',
        'amount'
    ]);

    if (!$amountColumn) {
        return [0,0,0,0];
    }

    switch ($quarter) {

        case 1:
            $months = [1,2,3,4];
            break;

        case 2:
            $months = [5,6,7,8];
            break;

        case 3:
            $months = [9,10,11,12];
            break;

        default:
            return [0,0,0,0];
    }

    $data = [];

    foreach ($months as $month) {

        $data[] = DB::table('bookings')
            ->where('status','confirmed')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', $month)
            ->sum($amountColumn);

    }

    return $data;
}
}