<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Review;
use Carbon\Carbon;
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

        $bookings = $query->paginate(10);

        return view('user.bookings.index', compact('bookings'));
    }

    public function show(int $booking)
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

        $bookingReview = Review::where('booking_id', $booking->id)->first();

        return view('user.bookings.show', compact(
            'booking',
            'bookingDetails',
            'bookingServices',
            'bookingReview'
        ));
    }

    public function create(int $stadium)
    {
        $stadiumData = DB::table('stadiums')->where('id', $stadium)->first();

        if (!$stadiumData) {
            abort(404);
        }

        $fields = DB::table('fields')
            ->where('stadium_id', $stadiumData->id)
            ->get();

        $timeSlots = DB::table('time_slots')
            ->where('status', true)
            ->orderBy('start_time')
            ->get();

        $services = DB::table('services')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        if ($services->isEmpty()) {
            $fallbackServices = [
                ['name' => 'Nước uống', 'description' => 'Nước suối, nước ngọt', 'price' => 10000, 'unit' => 'chai'],
                ['name' => 'Thuê bóng', 'description' => 'Bóng thi đấu chất lượng', 'price' => 50000, 'unit' => 'trận'],
                ['name' => 'Áo bib', 'description' => 'Áo phân đội', 'price' => 20000, 'unit' => 'bộ'],
                ['name' => 'Thuê găng tay', 'description' => 'Găng tay thủ môn', 'price' => 30000, 'unit' => 'cặp'],
                ['name' => 'Bãi gửi xe', 'description' => 'Gửi xe cho khách', 'price' => 5000, 'unit' => 'xe'],
            ];

            $services = collect($fallbackServices)->map(fn ($item) => (object) $item);
        }

        return view('user.bookings.create', [
            'stadium' => $stadiumData,
            'fields' => $fields,
            'timeSlots' => $timeSlots,
            'services' => $services,
        ]);
    }

    public function createMonthly(Request $request, int $stadium)
    {
        $stadiumData = DB::table('stadiums')->where('id', $stadium)->first();

        if (!$stadiumData) {
            abort(404);
        }

        $fields = DB::table('fields')
            ->where('stadium_id', $stadiumData->id)
            ->where('status', true)
            ->get();

        $timeSlots = DB::table('time_slots')
            ->where('status', true)
            ->orderBy('start_time')
            ->get();

        return view('user.bookings.create_monthly', [
            'stadium' => $stadiumData,
            'fields' => $fields,
            'timeSlots' => $timeSlots,
        ]);
    }

    public function availability(Request $request, int $stadium)
    {
        $stadiumData = DB::table('stadiums')->where('id', $stadium)->first();

        if (!$stadiumData) {
            abort(404);
        }

        $fieldId = $request->input('field_id');
        $bookingDate = $this->convertBookingDate($request->input('booking_date')) ?? now()->format('Y-m-d');

        if (!$fieldId) {
            $field = DB::table('fields')->where('stadium_id', $stadiumData->id)->orderBy('id')->first();
            $fieldId = $field?->id;
        }

        $field = DB::table('fields')
            ->leftJoin('field_types', 'fields.field_type_id', '=', 'field_types.id')
            ->where('fields.id', $fieldId)
            ->where('fields.stadium_id', $stadiumData->id)
            ->select('fields.*', 'field_types.name as field_type_name', 'field_types.number_of_players')
            ->first();

        if (!$field) {
            return response()->json(['message' => 'Sân không hợp lệ.'], 422);
        }

        $timeSlots = DB::table('time_slots')
            ->where('status', true)
            ->orderBy('start_time')
            ->get();

        if ($timeSlots->isEmpty() && Schema::hasTable('time_slots')) {
            $timeSlots = collect([
                ['start_time' => '06:00:00', 'end_time' => '07:00:00', 'status' => true],
                ['start_time' => '07:00:00', 'end_time' => '08:00:00', 'status' => true],
                ['start_time' => '08:00:00', 'end_time' => '09:00:00', 'status' => true],
                ['start_time' => '09:00:00', 'end_time' => '10:00:00', 'status' => true],
                ['start_time' => '10:00:00', 'end_time' => '11:00:00', 'status' => true],
            ]);
        }

        $slots = [];

        foreach ($timeSlots as $slot) {
            $exists = DB::table('booking_details as bd')
                ->join('bookings as b', 'bd.booking_id', '=', 'b.id')
                ->where('bd.field_id', $fieldId)
                ->where('bd.time_slot_id', $slot->id)
                ->whereDate('bd.booking_date', $bookingDate)
                ->where('b.status', '!=', 'cancelled')
                ->exists();

            $slotId = $slot->id ?? null;
            $startTime = $slot->start_time ?? data_get($slot, 'start_time');
            $endTime = $slot->end_time ?? data_get($slot, 'end_time');
            $slotDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $bookingDate . ' ' . substr($startTime, 0, 8));
            $isPast = $slotDateTime->isPast();

            $status = 'available';
            if ($exists) {
                $status = 'booked';
            } elseif ($isPast) {
                $status = 'locked';
            }

            $slots[] = [
                'id' => $slotId,
                'time' => substr($startTime, 0, 5) . ' - ' . substr($endTime, 0, 5),
                'price' => $this->calculateSlotPrice($field, $startTime),
                'available' => $status === 'available',
                'status' => $status,
            ];
        }

        return response()->json([
            'stadium_id' => $stadiumData->id,
            'field_id' => $fieldId,
            'booking_date' => $bookingDate,
            'slots' => $slots,
        ]);
    }

    public function storeFromStadium(Request $request, int $stadium)
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

        $field = DB::table('fields')
            ->leftJoin('field_types', 'fields.field_type_id', '=', 'field_types.id')
            ->where('fields.id', $fieldId)
            ->where('fields.stadium_id', $stadiumId)
            ->select('fields.*', 'field_types.name as field_type_name', 'field_types.number_of_players')
            ->first();

        $timeSlot = $timeSlotId
            ? DB::table('time_slots')->where('id', $timeSlotId)->where('status', true)->first()
            : null;

        if (!$field || !$timeSlot) {
            return back()->withInput()->withErrors([
                'booking_time' => 'Sân hoặc khung giờ không hợp lệ.',
            ]);
        }

        $slotPrice = $this->calculateSlotPrice($field, $timeSlot->start_time);

        $serviceTotal = 0;
        $serviceInputs = collect($request->input('services', []));
        $selectedServices = [];

        foreach ($serviceInputs as $item) {
            $serviceId = isset($item['id']) ? intval($item['id']) : null;
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;

            if (!$serviceId || $quantity <= 0) {
                continue;
            }

            $service = DB::table('services')
                ->where('id', $serviceId)
                ->where('status', true)
                ->first();

            if (!$service) {
                continue;
            }

            $price = (float) $service->price;
            $total = $price * $quantity;

            $serviceTotal += $total;
            $selectedServices[] = [
                'service_id' => $serviceId,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $total,
            ];
        }

        $subTotal = $slotPrice + $serviceTotal;

        $promotion = null;
        $discountAmount = 0;
        $promotionCode = Str::upper(trim((string) $request->input('promotion_code')));

        if ($promotionCode !== '') {
            $promotion = Promotion::query()
                ->whereRaw('UPPER(code) = ?', [$promotionCode])
                ->where('status', true)
                ->where(fn ($query) => $query->whereNull('start_date')->orWhereDate('start_date', '<=', today()))
                ->where(fn ($query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', today()))
                ->first();

            if (!$promotion) {
                return back()->withInput()->withErrors(['promotion_code' => 'Mã giảm giá không tồn tại, chưa có hiệu lực hoặc đã hết hạn.']);
            }

            if ($subTotal < (float) $promotion->min_order_amount) {
                return back()->withInput()->withErrors(['promotion_code' => 'Đơn hàng chưa đạt giá trị tối thiểu để dùng mã này.']);
            }

            if ($promotion->quantity !== null && $promotion->bookings()->where('status', '!=', 'cancelled')->count() >= $promotion->quantity) {
                return back()->withInput()->withErrors(['promotion_code' => 'Mã giảm giá đã hết lượt sử dụng.']);
            }

            $discountAmount = $promotion->discount_type === 'percent'
                ? $subTotal * ((float) $promotion->discount_value / 100)
                : (float) $promotion->discount_value;

            if ($promotion->max_discount_amount !== null) {
                $discountAmount = min($discountAmount, (float) $promotion->max_discount_amount);
            }

            $discountAmount = min($discountAmount, $subTotal);
        }

        $finalAmount = $subTotal - $discountAmount;

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

        $depositAmount = $finalAmount * 0.3; // Đơn lẻ cọc 30%
        $paymentTypeInput = strtolower((string)$request->input('payment_type', 'deposit'));

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
                'payment_type' => $paymentTypeInput,
                'booking_type' => 'single',
                'paid_amount' => 0,

                'price' => $slotPrice,
                'service_total' => $serviceTotal,
                'total_amount' => $finalAmount,
                'total_price' => $finalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'promotion_id' => $promotion?->id,

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

                'price' => $slotPrice,
                'field_price' => $slotPrice,
                'total_price' => $slotPrice,

                'status' => 'pending',

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (!empty($bookingDetailData)) {
                DB::table('booking_details')->insert($bookingDetailData);
            }

            if (!empty($selectedServices)) {
                foreach ($selectedServices as $serviceRow) {
                    $serviceRow = $this->filterColumns('booking_services', [
                        'booking_id' => $bookingId,
                        'service_id' => $serviceRow['service_id'],
                        'quantity' => $serviceRow['quantity'],
                        'price' => $serviceRow['price'],
                        'total' => $serviceRow['total'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (!empty($serviceRow)) {
                        DB::table('booking_services')->insert($serviceRow);
                    }
                }
            }

            DB::commit();

            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'title' => 'Đơn đặt sân mới',
                'content' => 'Khách hàng ' . $customerName . ' đã tạo đơn đặt sân mã ' . $bookingCode . '. Vui lòng kiểm tra và xử lý.',
                'type' => 'booking',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()
                ->route('user.payment.show', $bookingId)
                ->with('success', 'Đơn đặt sân đã được tạo tạm thời. Vui lòng thanh toán để xác nhận đơn.');
        } catch (\Throwable $e) {
            DB::rollBack();

            dd([
                'message' => 'Lỗi thật khi lưu đơn đặt sân',
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
        }
    }

    // 🔥 HÀM XỬ LÝ ĐẶT LỊCH CỐ ĐỊNH THEO THÁNG (CỌC 50% HOẶC 100%)
    public function storeMonthly(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $stadiumId = $request->input('stadium_id');
        $fieldId = $request->input('field_id');
        $timeSlotId = $request->input('time_slot_id');

        $year = (int)$request->input('year', now()->year);
        $month = (int)$request->input('month', now()->month);
        $dayOfWeek = (int)$request->input('day_of_week'); 
        $paymentType = $request->input('payment_type', 'deposit_50');

        if (!$stadiumId || !$fieldId || !$timeSlotId) {
            return back()->withInput()->withErrors(['monthly_error' => 'Vui lòng chọn đầy đủ Sân và Khung giờ đá!']);
        }

        $datesInMonth = [];
        $startDate = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $startDate->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            if ($date->dayOfWeek === $dayOfWeek) {
                $datesInMonth[] = $date->format('Y-m-d');
            }
        }

        if (empty($datesInMonth)) {
            return back()->withInput()->withErrors(['monthly_error' => 'Không tìm thấy ngày phù hợp trong tháng đã chọn.']);
        }

        $conflictedDates = DB::table('booking_details as bd')
            ->join('bookings as b', 'bd.booking_id', '=', 'b.id')
            ->where('bd.field_id', $fieldId)
            ->where('bd.time_slot_id', $timeSlotId)
            ->whereIn('bd.booking_date', $datesInMonth)
            ->where('b.status', '!=', 'cancelled')
            ->pluck('bd.booking_date')
            ->toArray();

        if (!empty($conflictedDates)) {
            $formattedDates = array_map(fn($d) => Carbon::parse($d)->format('d/m/Y'), $conflictedDates);
            return back()->withInput()->withErrors([
                'monthly_error' => 'Khung giờ này đã bị trùng lịch vào các ngày: ' . implode(', ', $formattedDates) . '.'
            ]);
        }

        $field = DB::table('fields')->where('id', $fieldId)->first();
        $timeSlot = DB::table('time_slots')->where('id', $timeSlotId)->first();

        if (!$field || !$timeSlot) {
            return back()->withInput()->withErrors(['monthly_error' => 'Sân hoặc Khung giờ không tồn tại.']);
        }

        $pricePerSlot = $this->calculateSlotPrice($field, $timeSlot->start_time);
        $totalSlots = count($datesInMonth);
        $totalAmount = $pricePerSlot * $totalSlots;
        
        // Đơn tháng: cọc 50% nếu chọn deposit_50, ngược lại trả đủ 100%
        $depositAmount = ($paymentType === 'deposit_50') ? ($totalAmount * 0.50) : $totalAmount;

        $bookingCode = 'BKMONTH' . now()->format('YmdHis') . Str::upper(Str::random(2));
        $user = Auth::user();

        $dayNames = [0 => 'Chủ Nhật', 1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7'];
        $dayLabel = $dayNames[$dayOfWeek] ?? 'Thứ';

        try {
            DB::beginTransaction();

            $bookingData = $this->filterColumns('bookings', [
                'user_id' => $user->id,
                'stadium_id' => $stadiumId,
                'booking_code' => $bookingCode,
                'code' => $bookingCode,
                'customer_name' => $user->name ?? 'Khách hàng',
                'customer_phone' => $request->input('phone', $user->phone ?? '0000000000'),
                'customer_email' => $user->email,
                'name' => $user->name ?? 'Khách hàng',
                'email' => $user->email,
                'phone' => $request->input('phone', $user->phone ?? '0000000000'),
                'total_amount' => $totalAmount,
                'total_price' => $totalAmount,
                'final_amount' => $totalAmount,
                'deposit_amount' => $depositAmount, // Lưu đúng 50% hoặc 100%
                'is_deposit_paid' => false,
                'discount_amount' => 0,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'refund_amount' => 0,
                'refund_status' => 'none',
                'note' => "Lịch cố định $dayLabel Tháng $month/$year ($totalSlots buổi)",
                'payment_type' => $paymentType,
                'paid_amount' => 0,
                'booking_type' => 'monthly', 
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bookingId = DB::table('bookings')->insertGetId($bookingData);

            foreach ($datesInMonth as $bDate) {
                $detailData = $this->filterColumns('booking_details', [
                    'booking_id' => $bookingId,
                    'stadium_id' => $stadiumId,
                    'field_id' => $fieldId,
                    'time_slot_id' => $timeSlotId,
                    'booking_date' => $bDate,
                    'date' => $bDate,
                    'start_time' => $timeSlot->start_time,
                    'end_time' => $timeSlot->end_time,
                    'price' => $pricePerSlot,
                    'total_price' => $pricePerSlot,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (!empty($detailData)) {
                    DB::table('booking_details')->insert($detailData);
                }
            }

            DB::commit();

            return redirect()->route('user.payment.show', $bookingId)
                ->with('success', "Tạo lịch cố định $dayLabel Tháng $month/$year ($totalSlots buổi) thành công! Vui lòng hoàn tất thanh toán.");

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['monthly_error' => 'Lỗi tạo lịch tháng: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, int $booking)
    {
        $bookingData = DB::table('bookings')
            ->leftJoin('booking_details', 'bookings.id', '=', 'booking_details.booking_id')
            ->leftJoin('time_slots', 'booking_details.time_slot_id', '=', 'time_slots.id')
            ->where('bookings.id', $booking)
            ->where('bookings.user_id', Auth::id())
            ->select(
                'bookings.*',
                'booking_details.booking_date as detail_booking_date',
                'time_slots.start_time as slot_start_time'
            )
            ->first();

        if (!$bookingData) {
            abort(404);
        }

        $status = $bookingData->status ?? 'pending';
        $isMonthly = (($bookingData->booking_type ?? 'single') === 'monthly');

        if ($isMonthly && $status !== 'pending') {
            return back()->withErrors([
                'delete_booking' => 'Đơn đặt lịch cố định theo tháng đã được xác nhận hoặc thanh toán KHÔNG áp dụng chính sách hủy sân!'
            ]);
        }

        if ($status === 'completed' || $status === 'cancelled') {
            return back()->withErrors([
                'delete_booking' => 'Đơn hàng này không thể thực hiện hủy.',
            ]);
        }

        try {
            DB::beginTransaction();

            DB::table('bookings')
                ->where('id', $bookingData->id)
                ->where('user_id', Auth::id())
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

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
                ->with('success', 'Hủy đơn đặt sân thành công.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'delete_booking' => 'Không thể xử lý hủy đơn đặt sân. Vui lòng thử lại.',
            ]);
        }
    }

    public function confirmRefund(int $id)
    {
        DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'refund_status' => 'confirmed_by_user',
                'updated_at' => now(),
            ]);

        $booking = DB::table('bookings')->where('id', $id)->first();
        DB::table('notifications')->insert([
            'user_id' => $booking?->user_id,
            'title' => 'Xác nhận đã nhận tiền',
            'content' => 'Khách hàng ' . ($booking?->customer_name ?? 'Khách hàng') . ' đã xác nhận đã nhận tiền hoàn cho đơn ' . ($booking?->booking_code ?? $booking?->code ?? $id) . '.',
            'type' => 'payment',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận! Giao dịch hủy sân và hoàn tiền đã hoàn tất.');
    }

    public function disputeRefund(Request $request, int $id)
    {
        $request->validate([
            'dispute_reason' => 'required|string|max:500',
        ], [
            'dispute_reason.required' => 'Vui lòng nhập nội dung sự cố bạn gặp phải.',
        ]);

        DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'refund_status' => 'disputed',
                'user_dispute_reason' => $request->input('dispute_reason'),
                'updated_at' => now(),
            ]);

        $booking = DB::table('bookings')->where('id', $id)->first();
        DB::table('notifications')->insert([
            'user_id' => $booking?->user_id,
            'title' => 'Khiếu nại hoàn tiền',
            'content' => 'Khách hàng ' . ($booking?->customer_name ?? 'Khách hàng') . ' đã gửi yêu cầu khiếu nại hoàn tiền cho đơn ' . ($booking?->booking_code ?? $booking?->code ?? $id) . '.',
            'type' => 'payment',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Đã gửi báo cáo tới Admin. Ban quản lý sẽ kiểm tra lại bill giao dịch và hỗ trợ bạn sớm nhất!');
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

    private function parseMoney(mixed $value): float
    {
        return (float) preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function calculateSlotPrice(object $field, ?string $startTime): float
    {
        $players = $this->resolveFieldPlayers($field);

        $basePrice = [
            7 => 350000,
            9 => 400000,
            11 => 500000,
        ][$players] ?? (float) ($field->price_per_hour ?? 0);

        $startHour = (int) substr((string) $startTime, 0, 2);

        return $basePrice + ($startHour >= 18 ? 100000 : 0);
    }

    private function resolveFieldPlayers(object $field): ?int
    {
        foreach ([$field->name ?? '', $field->field_type_name ?? ''] as $label) {
            if (preg_match('/(?<!\d)(7|9|11)(?!\d)/u', (string) $label, $matches)) {
                return (int) $matches[1];
            }
        }

        return isset($field->number_of_players) ? (int) $field->number_of_players : null;
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

    public function checkStatus(int $id)
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
                
                $isFull = ($booking->payment_type ?? 'deposit') === 'full';
                $totalPrice = (float)($booking->total_amount ?? $booking->total_price ?? 0);

                DB::table('bookings')
                    ->where('id', $bookingId)
                    ->update([
                        'status' => 'confirmed',
                        'is_deposit_paid' => true,
                        'paid_amount' => $isFull ? $totalPrice : (float)($booking->deposit_amount ?? ($totalPrice * 0.3)),
                        'updated_at' => now(),
                    ]);

                return response()->json(['success' => true, 'message' => 'Ngân hàng báo có tiền. Tự động duyệt thành công!']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Nội dung chuyển khoản hoặc ID đơn hàng không hợp lệ.']);
    }
}