<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingDetailController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('booking_details')
            ->leftJoin('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->leftJoin('users', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('fields', 'booking_details.field_id', '=', 'fields.id')
            ->leftJoin('time_slots', 'booking_details.time_slot_id', '=', 'time_slots.id')
            ->select(
                'booking_details.*',
                'bookings.id as booking_id',
                'bookings.status as booking_status',
                'users.name as user_name',
                'users.email as user_email',
                'fields.name as field_name',
                'fields.price_per_hour as field_price_per_hour',
                'time_slots.start_time as slot_start_time',
                'time_slots.end_time as slot_end_time'
            )
            ->orderByDesc('booking_details.id');

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('booking_details.id', $keyword)
                    ->orWhere('booking_details.booking_id', $keyword)
                    ->orWhere('fields.name', 'like', "%{$keyword}%")
                    ->orWhere('users.name', 'like', "%{$keyword}%")
                    ->orWhere('users.email', 'like', "%{$keyword}%");
            });
        }

        $bookingDetails = $query->paginate(15)->withQueryString();

        return view('admin.booking_details.index', compact('bookingDetails'));
    }

    public function show($id)
    {
        $bookingDetail = DB::table('booking_details')
            ->leftJoin('bookings', 'booking_details.booking_id', '=', 'bookings.id')
            ->leftJoin('users', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('fields', 'booking_details.field_id', '=', 'fields.id')
            ->leftJoin('time_slots', 'booking_details.time_slot_id', '=', 'time_slots.id')
            ->select(
                'booking_details.*',
                'bookings.id as booking_id',
                'bookings.status as booking_status',
                'users.name as user_name',
                'users.email as user_email',
                'fields.name as field_name',
                'fields.price_per_hour as field_price_per_hour',
                'time_slots.start_time as slot_start_time',
                'time_slots.end_time as slot_end_time'
            )
            ->where('booking_details.id', $id)
            ->first();

        if (!$bookingDetail) {
            abort(404);
        }

        return view('admin.booking_details.show', compact('bookingDetail'));
    }
}