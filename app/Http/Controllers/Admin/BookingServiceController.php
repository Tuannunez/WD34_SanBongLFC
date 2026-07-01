<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingServiceController extends Controller
{
    public function index()
    {
        $bookingServices = DB::table('booking_services')
            ->leftJoin('bookings', 'booking_services.booking_id', '=', 'bookings.id')
            ->leftJoin('services', 'booking_services.service_id', '=', 'services.id')
            ->select(
                'booking_services.*',
                'bookings.booking_code',
                'bookings.customer_name',
                'services.name as service_name',
                'services.unit as service_unit'
            )
            ->orderBy('booking_services.id', 'desc')
            ->paginate(10);

        return view('admin.booking_services.index', compact('bookingServices'));
    }

    public function create()
    {
        $bookings = DB::table('bookings')
            ->orderBy('id', 'desc')
            ->get();

        $services = DB::table('services')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.booking_services.create', compact('bookings', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'booking_id.required' => 'Vui lòng chọn đơn đặt sân.',
            'booking_id.exists' => 'Đơn đặt sân không tồn tại.',
            'service_id.required' => 'Vui lòng chọn dịch vụ.',
            'service_id.exists' => 'Dịch vụ không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        $service = DB::table('services')
            ->where('id', $request->service_id)
            ->first();

        if (!$service) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Dịch vụ không tồn tại.');
        }

        $price = $service->price;
        $total = $price * $request->quantity;

        DB::table('booking_services')->insert([
            'booking_id' => $request->booking_id,
            'service_id' => $request->service_id,
            'quantity' => $request->quantity,
            'price' => $price,
            'total' => $total,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.booking-services.index')
            ->with('success', 'Thêm dịch vụ đặt sân thành công.');
    }

    public function show($id)
    {
        $bookingService = DB::table('booking_services')
            ->leftJoin('bookings', 'booking_services.booking_id', '=', 'bookings.id')
            ->leftJoin('services', 'booking_services.service_id', '=', 'services.id')
            ->select(
                'booking_services.*',
                'bookings.booking_code',
                'bookings.customer_name',
                'bookings.customer_phone',
                'bookings.customer_email',
                'services.name as service_name',
                'services.unit as service_unit'
            )
            ->where('booking_services.id', $id)
            ->first();

        if (!$bookingService) {
            abort(404);
        }

        return view('admin.booking_services.show', compact('bookingService'));
    }

    public function edit($id)
    {
        $bookingService = DB::table('booking_services')
            ->where('id', $id)
            ->first();

        if (!$bookingService) {
            abort(404);
        }

        $bookings = DB::table('bookings')
            ->orderBy('id', 'desc')
            ->get();

        $services = DB::table('services')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.booking_services.edit', compact('bookingService', 'bookings', 'services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'booking_id.required' => 'Vui lòng chọn đơn đặt sân.',
            'booking_id.exists' => 'Đơn đặt sân không tồn tại.',
            'service_id.required' => 'Vui lòng chọn dịch vụ.',
            'service_id.exists' => 'Dịch vụ không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        $bookingService = DB::table('booking_services')
            ->where('id', $id)
            ->first();

        if (!$bookingService) {
            abort(404);
        }

        $service = DB::table('services')
            ->where('id', $request->service_id)
            ->first();

        if (!$service) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Dịch vụ không tồn tại.');
        }

        $price = $service->price;
        $total = $price * $request->quantity;

        DB::table('booking_services')
            ->where('id', $id)
            ->update([
                'booking_id' => $request->booking_id,
                'service_id' => $request->service_id,
                'quantity' => $request->quantity,
                'price' => $price,
                'total' => $total,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('admin.booking-services.index')
            ->with('success', 'Cập nhật dịch vụ đặt sân thành công.');
    }

    public function destroy($id)
    {
        $bookingService = DB::table('booking_services')
            ->where('id', $id)
            ->first();

        if (!$bookingService) {
            abort(404);
        }

        DB::table('booking_services')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('admin.booking-services.index')
            ->with('success', 'Xóa dịch vụ đặt sân thành công.');
    }
}