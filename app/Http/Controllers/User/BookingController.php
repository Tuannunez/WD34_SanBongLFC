<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class BookingController extends Controller
{
    public function index()
    {
        $query = DB::table('bookings')
            ->leftJoin('booking_details', 'bookings.id', '=', 'booking_details.booking_id')
            ->leftJoin('fields', 'booking_details.field_id', '=', 'fields.id')
            ->leftJoin('time_slots', 'booking_details.time_slot_id', '=', 'time_slots.id')
            ->where('bookings.user_id', Auth::id())
            ->select(
                'bookings.*',
                'booking_details.booking_date as detail_booking_date',
                'booking_details.price as detail_price',
                'fields.name as field_name',
                'fields.price_per_hour as field_price_per_hour',
                'time_slots.start_time as slot_start_time',
                'time_slots.end_time as slot_end_time'
            )
            ->orderByDesc('bookings.id');

        if (Schema::hasColumn('bookings', 'status')) {
            $query->where('bookings.status', '!=', 'cancelled');
        }

        $bookings = $query->paginate(10);

        return view('user.bookings.index', compact('bookings'));
    }

    public function show($booking)
    {
        $booking = DB::table('bookings')
            ->where('id', $booking)
            ->where('user_id', Auth::id())
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
                'time_slots.start_time as slot_start_time',
                'time_slots.end_time as slot_end_time'
            )
            ->get();

        $bookingServices = DB::table('booking_services')
            ->leftJoin('services', 'booking_services.service_id', '=', 'services.id')
            ->where('booking_services.booking_id', $booking->id)
            ->select(
                'booking_services.*',
                'services.name as service_name'
            )
            ->get();

        return view('user.bookings.show', compact(
            'booking',
            'bookingDetails',
            'bookingServices'
        ));
    }

    public function create($stadium)
    {
        $stadiumData = DB::table('stadiums')->where('id', $stadium)->first();

        if (!$stadiumData) {
            abort(404);
        }

        $fields = DB::table('fields')
            ->where('stadium_id', $stadiumData->id)
            ->get();

        $timeSlots = DB::table('time_slots')->get();

        $services = DB::table('services')->get();

        return view('user.bookings.create', [
            'stadium' => $stadiumData,
            'fields' => $fields,
            'timeSlots' => $timeSlots,
            'services' => $services,
        ]);
    }

    public function storeFromStadium(Request $request, $stadium)
    {
        $request->merge([
            'stadium_id' => $request->input('stadium_id', $stadium),
        ]);

        return $this->store($request);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $stadiumId = $request->input('stadium_id');

        if (!$stadiumId) {
            return back()
                ->withInput()
                ->withErrors([
                    'stadium_id' => 'Không xác định được cơ sở sân.',
                ]);
        }

        $bookingDate = $this->convertBookingDate($request->input('booking_date'));

        if (!$bookingDate) {
            return back()
                ->withInput()
                ->withErrors([
                    'booking_date' => 'Ngày đặt sân không hợp lệ.',
                ]);     
        }

        $timeSlotText = $request->input('time_slot');
        [$startTime, $endTime] = $this->splitTimeSlot($timeSlotText);

        $fieldId = $request->input('field_id');

        if (!$fieldId) {
            $field = DB::table('fields')
                ->where('stadium_id', $stadiumId)
                ->orderBy('id')
                ->first();

            if (!$field) {
                $field = DB::table('fields')
                    ->orderBy('id')
                    ->first();
            }

            $fieldId = $field->id ?? null;
        }

        if (!$fieldId) {
            dd([
                'message' => 'Không tìm thấy sân con trong bảng fields',
                'stadium_id' => $stadiumId,
                'fields_count' => DB::table('fields')->count(),
                'fields' => DB::table('fields')->get(),
            ]);
        }

        $timeSlotId = $request->input('time_slot_id');

        if (!$timeSlotId && $startTime && $endTime && Schema::hasTable('time_slots')) {
            $startShort = substr($startTime, 0, 5);
            $endShort = substr($endTime, 0, 5);

            $timeSlot = DB::table('time_slots')
                ->where(function ($query) use ($startTime, $startShort) {
                    $query->where('start_time', $startTime)
                        ->orWhere('start_time', $startShort);
                })
                ->where(function ($query) use ($endTime, $endShort) {
                    $query->where('end_time', $endTime)
                        ->orWhere('end_time', $endShort);
                })
                ->first();

            if ($timeSlot) {
                $timeSlotId = $timeSlot->id;
            } else {
                $timeSlotData = $this->filterColumns('time_slots', [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (!empty($timeSlotData)) {
                    $timeSlotId = DB::table('time_slots')->insertGetId($timeSlotData);
                }
            }
        }

        $totalPrice = $this->parseMoney($request->input('total_price'));

        if ($totalPrice <= 0) {
            $field = DB::table('fields')->where('id', $fieldId)->first();
            $totalPrice = $field->price_per_hour ?? 300000;
        }

        // =========================================================================
        // LOGIC KHÓA SÂN TẠM THỜI 5 PHÚT - TRÁNH TRÙNG LỊCH ĐẶT SÂN
        // =========================================================================
        $duplicateQuery = DB::table('bookings')
            ->join('booking_details', 'bookings.id', '=', 'booking_details.booking_id')
            ->where('booking_details.field_id', $fieldId)
            ->where('booking_details.time_slot_id', $timeSlotId);

        if (Schema::hasColumn('booking_details', 'booking_date')) {
            $duplicateQuery->whereDate('booking_details.booking_date', $bookingDate);
        } elseif (Schema::hasColumn('booking_details', 'date')) {
            $duplicateQuery->whereDate('booking_details.date', $bookingDate);
        }

        $duplicateQuery->where(function ($query) {
            $query->whereIn('bookings.status', ['confirmed', 'completed'])
                  ->orWhere(function ($q) {
                      $q->where('bookings.status', 'pending')
                        ->where('bookings.created_at', '>=', now()->subMinutes(5));
                  });
        });

        $existingBookingDetail = $duplicateQuery->first();

        if ($existingBookingDetail) {
            return back()
                ->withInput()
                ->withErrors([
                    'booking_time' => 'Khung giờ này đã có người đặt hoặc đang trong quá trình thanh toán (giữ sân 5 phút). Vui lòng chọn khung giờ khác hoặc chờ hết thời gian giữ sân!',
                ]);
        }

        // =========================================================================
        // CODE THÊM: DỌN DẸP CHI TIẾT ĐƠN CŨ ĐÃ HỦY ĐỂ TRÁNH LỖI DUPLICATE ENTRY UNIQUE KEY
        // =========================================================================
        $cancelledBookings = DB::table('bookings')
            ->join('booking_details', 'bookings.id', '=', 'booking_details.booking_id')
            ->where('booking_details.field_id', $fieldId)
            ->where('booking_details.time_slot_id', $timeSlotId)
            ->whereDate('booking_details.booking_date', $bookingDate)
            ->where('bookings.status', 'cancelled')
            ->pluck('bookings.id');

        if ($cancelledBookings->isNotEmpty()) {
            DB::table('booking_details')->whereIn('booking_id', $cancelledBookings)->delete();
        }

        $bookingCode = 'BK' . now()->format('YmdHis') . Str::upper(Str::random(3));
        $user = Auth::user();
        
        $customerName = $user->name ?? 'Khách hàng';
        $customerEmail = $user->email ?? 'customer@example.com';
        $customerPhone = $request->input('customer_phone')
            ?? $request->input('phone')
            ?? $user->phone
            ?? '0000000000';

        $depositAmount = $totalPrice * 0.3;

        try {
            DB::beginTransaction();

            $bookingData = $this->filterColumns('bookings', [
                'user_id' => $user->id,
                'stadium_id' => $stadiumId,
                'field_id' => $fieldId,
                'time_slot_id' => $timeSlotId,

                'booking_code' => $bookingCode,
                'code' => $bookingCode,

                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,

                'name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,

                'booking_date' => $bookingDate,
                'date' => $bookingDate,

                'start_time' => $startTime,
                'end_time' => $endTime,

                'status' => 'pending',
                'payment_status' => 'unpaid',

                'price' => $totalPrice,
                'total_price' => $totalPrice,
                'total_amount' => $totalPrice,
                'total' => $totalPrice,
                'amount' => $totalPrice,

                'deposit_amount' => $depositAmount,
                'is_deposit_paid' => false,

                'note' => $request->input('note'),

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (empty($bookingData)) {
                dd([
                    'message' => 'Không có cột phù hợp để insert vào bookings',
                    'bookings_columns' => Schema::getColumnListing('bookings'),
                    'request' => $request->all(),
                ]);
            }

            $bookingId = DB::table('bookings')->insertGetId($bookingData);

            $bookingDetailData = $this->filterColumns('booking_details', [
                'booking_id' => $bookingId,
                'stadium_id' => $stadiumId,
                'field_id' => $fieldId,
                'time_slot_id' => $timeSlotId,

                'booking_date' => $bookingDate,
                'date' => $bookingDate,

                'start_time' => $startTime,
                'end_time' => $endTime,

                'price' => $totalPrice,
                'field_price' => $totalPrice,
                'total_price' => $totalPrice,
                'total' => $totalPrice,
                'amount' => $totalPrice,

                'status' => 'pending',

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($bookingDetailData)) {
                DB::table('booking_details')->insert($bookingDetailData);
            }

            DB::commit();

            return redirect()
                ->route('user.payment.show', $bookingId)
                ->with('success', 'Đơn đặt sân đã được tạo tạm thời. Vui lòng thanh toán cọc 30% để xác nhận đơn.');
        } catch (\Throwable $e) {
            DB::rollBack();

            dd([
                'message' => 'Lỗi thật khi lưu đơn đặt sân',
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'booking_data' => $bookingData ?? null,
                'booking_detail_data' => $bookingDetailData ?? null,
                'bookings_columns' => Schema::hasTable('bookings') ? Schema::getColumnListing('bookings') : null,
                'booking_details_columns' => Schema::hasTable('booking_details') ? Schema::getColumnListing('booking_details') : null,
            ]);
        }
    }

    public function destroy($booking)
    {
        $bookingData = DB::table('bookings')
            ->where('id', $booking)
            ->where('user_id', Auth::id())
            ->first();

        if (!$bookingData) {
            abort(404);
        }

        $status = $bookingData->status ?? 'pending';

        if (in_array($status, ['confirmed', 'completed'])) {
            return back()->withErrors([
                'delete_booking' => 'Đơn đã xác nhận hoặc đã hoàn thành nên không thể xóa. Vui lòng liên hệ quản trị viên.',
            ]);
        }

        try {
            DB::beginTransaction();

            if (Schema::hasColumn('bookings', 'status')) {
                DB::table('bookings')
                    ->where('id', $bookingData->id)
                    ->where('user_id', Auth::id())
                    ->update([
                        'status' => 'cancelled',
                        'updated_at' => now(),
                    ]);
            }

            if (Schema::hasTable('booking_details') && Schema::hasColumn('booking_details', 'status')) {
                DB::table('booking_details')
                    ->where('booking_id', $bookingData->id)
                    ->update([
                        'status' => 'cancelled',
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('user.bookings.index')
                ->with('success', 'Đã xóa đơn đặt sân khỏi lịch sử của bạn.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'delete_booking' => 'Không thể xóa đơn đặt sân. Vui lòng thử lại.',
            ]);
        }
    }

    private function convertBookingDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        $date = trim($date);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $date, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];

            return "{$year}-{$month}-{$day}";
        }

        return null;
    }

    private function splitTimeSlot(string $timeSlot): array
    {
        $parts = explode('-', $timeSlot);

        $startTime = trim($parts[0] ?? '');
        $endTime = trim($parts[1] ?? '');

        return [
            $this->normalizeTime($startTime),
            $this->normalizeTime($endTime),
        ];
    }

    private function normalizeTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        $time = trim($time);

        if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
            [$hour, $minute] = explode(':', $time);

            return str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . $minute . ':00';
        }

        return $time;
    }

    private function parseMoney($value): float
    {
        return (float) preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function getDefaultFieldId($stadiumId, $totalPrice = 0): ?int
    {
        if (!Schema::hasTable('fields')) {
            return null;
        }

        if (Schema::hasColumn('fields', 'stadium_id')) {
            $field = DB::table('fields')
                ->where('stadium_id', $stadiumId)
                ->orderBy('id')
                ->first();

            if ($field) {
                return $field->id;
            }
        }

        $stadium = Schema::hasTable('stadiums')
            ? DB::table('stadiums')->where('id', $stadiumId)->first()
            : null;

        $fieldTypeId = $this->getDefaultFieldTypeId();

        $price = $this->parseMoney($totalPrice);

        $fieldData = $this->filterColumns('fields', [
            'stadium_id' => $stadiumId,
            'field_type_id' => $fieldTypeId,
            'name' => 'Sân mặc định - ' . ($stadium->name ?? 'Cơ sở #' . $stadiumId),
            'description' => 'Sân mặc định được tạo tự động khi khách đặt sân.',
            'price' => $price,
            'default_price' => $price,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (empty($fieldData)) {
            return null;
        }

        try {
            return DB::table('fields')->insertGetId($fieldData);
        } catch (\Throwable $e) {
        }
    }

    private function findOrCreateTimeSlot(?string $startTime, ?string $endTime): ?int
    {
        if (!Schema::hasTable('time_slots')) {
            return null;
        }

        if (!$startTime || !$endTime) {
            return null;
        }

        $startShort = substr($startTime, 0, 5);
        $endShort = substr($endTime, 0, 5);

        if (Schema::hasColumn('time_slots', 'start_time') && Schema::hasColumn('time_slots', 'end_time')) {
            $timeSlot = DB::table('time_slots')
                ->where(function ($query) use ($startTime, $startShort) {
                    $query->where('start_time', $startTime)
                        ->orWhere('start_time', $startShort);
                })
                ->where(function ($query) use ($endTime, $endShort) {
                    $query->where('end_time', $endTime)
                        ->orWhere('end_time', $endShort);
                })
                ->first();

            if ($timeSlot) {
                return $timeSlot->id;
            }
        }

        $timeSlotData = $this->filterColumns('time_slots', [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (empty($timeSlotData)) {
            return null;
        }

        return DB::table('time_slots')->insertGetId($timeSlotData);
    }

    private function getDefaultPrice($fieldId, $timeSlotId): float
    {
        $field = DB::table('fields')->where('id', $fieldId)->first();

        foreach (['price_per_hour', 'price', 'default_price', 'field_price'] as $column) {
            if (isset($field->{$column}) && is_numeric($field->{$column})) {
                return (float) $field->{$column};
            }
        }

        $timeSlot = DB::table('time_slots')->where('id', $timeSlotId)->first();

        foreach (['price', 'default_price'] as $column) {
            if (isset($timeSlot->{$column}) && is_numeric($timeSlot->{$column})) {
                return (float) $timeSlot->{$column};
            }
        }

        return 300000;
    }

    private function filterColumns(string $table, array $data): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        $result = [];

        foreach ($data as $column => $value) {
            if (Schema::hasColumn($table, $column)) {
                $result[$column] = $value;
            }
        }

        return $result;
    }

    public function checkStatus($id)
    {
        $booking = DB::table('bookings')->where('id', $id)->first();
        if (!$booking) {
            return response()->json(['status' => 'not_found'], 404);
        }
        return response()->json(['status' => $booking->status]);
    }

    public function handleBankWebhook(Request $request)
    {
        $content = $request->input('content');

        preg_match('/MDS(\d+)/', $content, $matches);
        
        if (isset($matches[1])) {
            $bookingId = $matches[1];
            $booking = DB::table('bookings')->where('id', $bookingId)->first();
            
            if ($booking && $booking->status === 'pending') {
                DB::table('bookings')
                    ->where('id', $bookingId)
                    ->update([
                        'status' => 'confirmed',
                        'is_deposit_paid' => true,
                        'updated_at' => now()
                    ]);
                
                return response()->json(['success' => true, 'message' => 'Ngân hàng báo có tiền. Hệ thống tự động duyệt thành công!']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Nội dung chuyển khoản hoặc ID đơn hàng không hợp lệ.']);
    }
}