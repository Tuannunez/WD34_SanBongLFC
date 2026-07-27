<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('bookings')
            ->leftJoin('users', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('payments', 'bookings.id', '=', 'payments.booking_id')
            ->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->select(
                'bookings.*',
                'users.name as user_name',
                'users.email as user_email',
                'payment_methods.name as method_name'
            )
            ->orderByDesc('bookings.id');

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                if (is_numeric($keyword)) {
                    $q->where('bookings.id', $keyword);
                }

                if (Schema::hasColumn('bookings', 'booking_code')) {
                    $q->orWhere('bookings.booking_code', 'like', "%{$keyword}%");
                }

                if (Schema::hasColumn('bookings', 'code')) {
                    $q->orWhere('bookings.code', 'like', "%{$keyword}%");
                }

                if (Schema::hasColumn('bookings', 'customer_name')) {
                    $q->orWhere('bookings.customer_name', 'like', "%{$keyword}%");
                }

                if (Schema::hasColumn('bookings', 'customer_email')) {
                    $q->orWhere('bookings.customer_email', 'like', "%{$keyword}%");
                }

                if (Schema::hasColumn('bookings', 'customer_phone')) {
                    $q->orWhere('bookings.customer_phone', 'like', "%{$keyword}%");
                }

                $q->orWhere('users.name', 'like', "%{$keyword}%")
                  ->orWhere('users.email', 'like', "%{$keyword}%");

                if (Schema::hasColumn('bookings', 'status')) {
                    $q->orWhere('bookings.status', 'like', "%{$keyword}%");
                }
            });
        }

        if ($request->filled('status') && Schema::hasColumn('bookings', 'status')) {
            $query->where('bookings.status', $request->status);
        }

        if ($request->filled('booking_date')) {
            if (Schema::hasColumn('bookings', 'booking_date')) {
                $query->whereDate('bookings.booking_date', $request->booking_date);
            } elseif (Schema::hasColumn('bookings', 'date')) {
                $query->whereDate('bookings.date', $request->booking_date);
            }
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = DB::table('bookings')
            ->leftJoin('payments', 'bookings.id', '=', 'payments.booking_id')
            ->leftJoin('users', 'bookings.user_id', '=', 'users.id')
            ->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->select(
                'bookings.*',
                'users.name as user_name',
                'users.email as user_email',
                'payment_methods.name as method_name'
            )
            ->where('bookings.id', $id)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $bookingDetails = DB::table('booking_details')
            ->leftJoin('fields', 'booking_details.field_id', '=', 'fields.id')
            ->leftJoin('time_slots', 'booking_details.time_slot_id', '=', 'time_slots.id')
            ->where('booking_details.booking_id', $booking->id)
            ->select(
                'booking_details.*',
                'fields.name as field_name',
                'fields.price_per_hour as field_price_per_hour',
                'time_slots.start_time as slot_start_time',
                'time_slots.end_time as slot_end_time'
            )
            ->get();

        return view('admin.bookings.show', compact('booking', 'bookingDetails'));
    }

    public function processRefund(Request $request, $id)
    {
        $request->validate([
            'refund_proof_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'refund_proof_note' => 'nullable|string|max:255',
        ], [
            'refund_proof_image.required' => 'Vui lòng chọn ảnh bill chuyển khoản.',
            'refund_proof_image.image' => 'File tải lên phải là hình ảnh hợp lệ.',
        ]);

        $booking = DB::table('bookings')->where('id', $id)->first();

        if (!$booking) {
            return back()->withErrors(['error' => 'Không tìm thấy đơn đặt sân.']);
        }

        $imagePath = null;
        if ($request->hasFile('refund_proof_image')) {
            $file = $request->file('refund_proof_image');
            $filename = 'bill_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            if (!file_exists(public_path('uploads/refunds'))) {
                mkdir(public_path('uploads/refunds'), 0777, true);
            }
            
            $file->move(public_path('uploads/refunds'), $filename);
            $imagePath = 'uploads/refunds/' . $filename;
        }

        DB::table('bookings')->where('id', $id)->update([
            'refund_status' => 'completed',
            'refund_proof_image' => $imagePath,
            'refund_proof_note' => $request->input('refund_proof_note'),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.bookings.show', $id)
            ->with('success', 'Đã tải bill chuyển khoản thành công! Khách hàng đã có thể xem được ảnh bill.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'payment_status' => ['nullable', 'string', 'max:50'],
        ]);

        $updateData = [
            'status' => $request->status,
            'updated_at' => now(),
        ];

        if ($request->filled('payment_status') && $request->payment_status === 'paid') {
            DB::table('payments')
                ->where('booking_id', $id)
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(), 
                    'updated_at' => now()
                ]);
        }

        DB::table('bookings')
            ->where('id', $id)
            ->update($updateData);

        return redirect()
            ->route('admin.bookings.show', $id)
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function destroy($id)
    {
        if (Schema::hasColumn('bookings', 'status')) {
            DB::table('bookings')
                ->where('id', $id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);
        }

        if (Schema::hasColumn('booking_details', 'status')) {
            DB::table('booking_details')
                ->where('booking_id', $id)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);
        }

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Đã hủy đơn đặt sân.');
    }
}