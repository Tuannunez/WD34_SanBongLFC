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

    public function addExtraTime(Request $request, int $bookingId)
    {
        $userId = Auth::id();

        $booking = DB::table('bookings')
            ->where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $bookingDetail = DB::table('booking_details')
            ->where('booking_id', $bookingId)
            ->orderByDesc('id')
            ->first();

        if (!$bookingDetail) {
            return back()->with('error', 'Không tìm thấy thông tin chi tiết sân của đơn hàng.');
        }

        $fieldId = $bookingDetail->field_id;
        $bookingDate = $bookingDetail->booking_date ?? now()->format('Y-m-d');
        
        $currentTimeSlot = DB::table('time_slots')
            ->where('id', $bookingDetail->time_slot_id)
            ->first();

        if (!$currentTimeSlot || !isset($currentTimeSlot->end_time)) {
            return back()->with('error', 'Không xác định được khung giờ hiện tại của sân.');
        }

        $currentEndTime = $currentTimeSlot->end_time;
        $durationMinutes = (int) $request->input('duration_minutes', 60);

        // Tìm khung giờ tiếp theo dựa theo giờ kết thúc
        $nextSlot = DB::table('time_slots')
            ->where('start_time', $currentEndTime)
            ->where('status', true)
            ->first();

        if (!$nextSlot) {
            return back()->with('error', 'Rất tiếc, đã hết khung giờ trong ngày hoặc không tìm thấy khung giờ tiếp theo để thêm giờ.');
        }

        // KIỂM TRA TOÀN DIỆN: Khung giờ này đã tồn tại trong DB (do ràng buộc unique_field_time_date)
        $isSlotTaken = DB::table('booking_details as bd')
            ->join('bookings as b', 'bd.booking_id', '=', 'b.id')
            ->where('bd.field_id', $fieldId)
            ->where('bd.time_slot_id', $nextSlot->id)
            ->whereDate('bd.booking_date', $bookingDate)
            ->where('b.status', '!=', 'cancelled')
            ->exists();

        if ($isSlotTaken) {
            return back()->with('error', 'Khung giờ tiếp theo (' . substr($nextSlot->start_time, 0, 5) . ' - ' . substr($nextSlot->end_time, 0, 5) . ') đã có đơn đặt hoặc đã được sử dụng, không thể gia hạn thêm!');
        }

        $field = DB::table('fields')->where('id', $fieldId)->first();
        $pricePerHour = (float)($field->price_per_hour ?? 350000);
        $extraPrice = $pricePerHour * ($durationMinutes / 60);

        try {
            DB::beginTransaction();

            DB::table('booking_details')->insert([
                'booking_id' => $bookingId,
                'field_id' => $fieldId,
                'time_slot_id' => $nextSlot->id,
                'booking_date' => $bookingDate,
                'price' => $extraPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $currentTotal = (float)($booking->total_amount ?? $booking->final_amount ?? 0);
            $newTotalAmount = $currentTotal + $extraPrice;

            $updateData = ['updated_at' => now()];
            if (Schema::hasColumn('bookings', 'total_amount')) {
                $updateData['total_amount'] = $newTotalAmount;
            }
            if (Schema::hasColumn('bookings', 'final_amount')) {
                $updateData['final_amount'] = $newTotalAmount;
            }

            DB::table('bookings')->where('id', $bookingId)->update($updateData);

            DB::commit();
            return back()->with('success', 'Gia hạn thêm khung giờ (' . substr($nextSlot->start_time, 0, 5) . ' - ' . substr($nextSlot->end_time, 0, 5) . ') thành công!');

        } catch (\Throwable $e) {
            DB::rollBack();
            // Bắt trọn lỗi SQL trùng lặp unique để hiển thị thông báo thân thiện thay vì crash trang
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->with('error', 'Khung giờ này đã tồn tại trong hệ thống lịch đặt của sân, không thể thêm trùng.');
            }
            return back()->with('error', 'Lỗi khi thêm giờ: ' . $e->getMessage());
        }
    }

    public function addServiceToBooking(Request $request, int $bookingId)
    {
        $userId = Auth::id();

        $booking = DB::table('bookings')
            ->where('id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        if (!$booking) {
            abort(404);
        }

        $serviceId = (int) $request->input('service_id');
        $quantity = (int) $request->input('quantity', 1);

        if ($quantity <= 0) {
            return back()->with('error', 'Số lượng dịch vụ không hợp lệ.');
        }

        $service = DB::table('services')
            ->where('id', $serviceId)
            ->where('status', true)
            ->first();

        if (!$service) {
            return back()->with('error', 'Dịch vụ không tồn tại hoặc đã ngừng cung cấp.');
        }

        $price = (float) $service->price;
        $totalLinePrice = $price * $quantity;

        try {
            DB::beginTransaction();

            DB::table('booking_services')->insert([
                'booking_id' => $bookingId,
                'service_id' => $serviceId,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $totalLinePrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $currentTotal = (float)($booking->total_amount ?? $booking->final_amount ?? 0);
            $newTotalAmount = $currentTotal + $totalLinePrice;

            $updateData = ['updated_at' => now()];
            if (Schema::hasColumn('bookings', 'total_amount')) {
                $updateData['total_amount'] = $newTotalAmount;
            }
            if (Schema::hasColumn('bookings', 'final_amount')) {
                $updateData['final_amount'] = $newTotalAmount;
            }

            DB::table('bookings')->where('id', $bookingId)->update($updateData);

            DB::table('notifications')->insert([
                'user_id' => $booking->user_id,
                'title' => 'Khách gọi thêm dịch vụ',
                'content' => 'Đơn #' . $bookingId . ' vừa gọi thêm ' . $quantity . ' ' . ($service->unit ?? 'phần') . ' ' . $service->name . ' (' . number_format($totalLinePrice, 0, ',', '.') . 'đ).',
                'type' => 'booking',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Thêm thành công ' . $quantity . ' ' . $service->name . ' vào đơn hàng!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi thêm dịch vụ: ' . $e->getMessage());
        }
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

        $fieldSlotPrices = DB::table('field_time_slot_prices')
            ->whereIn('field_id', $fields->pluck('id'))
            ->get()
            ->groupBy('field_id')
            ->map(fn ($prices) => $prices->pluck('price', 'time_slot_id')->all())
            ->all();

        return view('user.bookings.create_monthly', [
            'stadium' => $stadiumData,
            'fields' => $fields,
            'timeSlots' => $timeSlots,
            'fieldSlotPrices' => $fieldSlotPrices,
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
                ['start_time' => '06:00:00', 'end_time' => '07:30:00', 'status' => true],
                ['start_time' => '07:30:00', 'end_time' => '09:00:00', 'status' => true],
                ['start_time' => '09:00:00', 'end_time' => '10:30:00', 'status' => true],
                ['start_time' => '10:30:00', 'end_time' => '12:00:00', 'status' => true],
                ['start_time' => '12:00:00', 'end_time' => '13:30:00', 'status' => true],
                ['start_time' => '13:30:00', 'end_time' => '15:00:00', 'status' => true],
                ['start_time' => '15:00:00', 'end_time' => '16:30:00', 'status' => true],
                ['start_time' => '16:30:00', 'end_time' => '18:00:00', 'status' => true],
                ['start_time' => '18:00:00', 'end_time' => '19:30:00', 'status' => true],
                ['start_time' => '19:30:00', 'end_time' => '21:00:00', 'status' => true],
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
                'price' => $this->calculateSlotPrice($field, $startTime, $slotId),
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

        $user = Auth::user();
        $bookingDate = $this->convertBookingDate($request->input('booking_date'));

        if (!$bookingDate) {
            return back()
                ->withInput()
                ->withErrors([
                    'booking_date' => 'Ngày đặt sân không hợp lệ.',
                ]);
        }

        $customerPhone = trim((string) $request->input('customer_phone', $user->phone ?? ''));
        if ($customerPhone === '') {
            return back()
                ->withInput()
                ->withErrors([
                    'customer_phone' => 'Vui lòng nhập số điện thoại.',
                ]);
        }
        if (!preg_match('/^[0-9]{1,10}$/', $customerPhone)) {
            return back()
                ->withInput()
                ->withErrors([
                    'customer_phone' => 'Số điện thoại chỉ gồm số và tối đa 10 chữ số.',
                ]);
        }

        $timeSlotText = $request->input('time_slot');
        if ($timeSlotText) {
            [$startTime, $endTime] = $this->splitTimeSlot($timeSlotText);
        } else {
            $startTime = null;
            $endTime = null;
        }

        $fieldId = $request->input('field_id');

        if (!$fieldId) {
            $field = DB::table('fields')
                ->where('stadium_id', $stadiumId)
                ->orderBy('id')
                ->first();

            if (!$field) {
                $field = DB::table('fields')->orderBy('id')->first();
            }

            $fieldId = $field->id ?? null;
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
            }
        }

        $field = DB::table('fields')
            ->leftJoin('field_types', 'fields.field_type_id', '=', 'field_types.id')
            ->where('fields.id', $fieldId)
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

        $slotPrice = $this->calculateSlotPrice($field, $timeSlot->start_time, $timeSlot->id);

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
                ->first();

            if ($promotion) {
                $discountAmount = $promotion->discount_type === 'percent'
                    ? $subTotal * ((float) $promotion->discount_value / 100)
                    : (float) $promotion->discount_value;
                $discountAmount = min($discountAmount, $subTotal);
            }
        }

        $finalAmount = $subTotal - $discountAmount;
        $bookingCode = 'BK' . now()->format('YmdHis') . Str::upper(Str::random(3));
        $depositAmount = $finalAmount * 0.3;

        try {
            DB::beginTransaction();

            $bookingData = $this->filterColumns('bookings', [
                'user_id' => $user->id,
                'stadium_id' => $stadiumId,
                'booking_code' => $bookingCode,
                'code' => $bookingCode,
                'customer_name' => $user->name ?? 'Khách hàng',
                'customer_email' => $user->email,
                'customer_phone' => $customerPhone,
                'total_amount' => $finalAmount,
                'final_amount' => $finalAmount,
                'deposit_amount' => $depositAmount,
                'is_deposit_paid' => false,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_type' => 'deposit',
                'booking_type' => 'single',
                'paid_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bookingId = DB::table('bookings')->insertGetId($bookingData);

            DB::table('booking_details')->insert([
                'booking_id' => $bookingId,
                'field_id' => $fieldId,
                'time_slot_id' => $timeSlotId,
                'booking_date' => $bookingDate,
                'price' => $slotPrice,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($selectedServices as $serviceRow) {
                DB::table('booking_services')->insert([
                    'booking_id' => $bookingId,
                    'service_id' => $serviceRow['service_id'],
                    'quantity' => $serviceRow['quantity'],
                    'price' => $serviceRow['price'],
                    'total' => $serviceRow['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('user.payment.show', $bookingId)
                ->with('success', 'Đơn đặt sân đã được tạo tạm thời. Vui lòng thanh toán để xác nhận đơn.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Lỗi khi tạo đơn: ' . $e->getMessage()]);
        }
    }

    public function storeMonthly(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'stadium_id' => 'required|exists:stadiums,id',
            'field_id' => 'required|exists:fields,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2026',
            'payment_type' => 'required|in:deposit_50,full',
        ]);

        $userId = Auth::id();
        $stadiumId = $request->input('stadium_id');
        $fieldId = $request->input('field_id');
        $timeSlotId = $request->input('time_slot_id');
        $dayOfWeek = (int) $request->input('day_of_week');
        $month = (int) $request->input('month');
        $year = (int) $request->input('year');
        $paymentType = $request->input('payment_type');

        $field = DB::table('fields')->where('id', $fieldId)->first();
        $timeSlot = DB::table('time_slots')->where('id', $timeSlotId)->first();

        if (!$field || !$timeSlot) {
            return back()->withInput()->withErrors(['error' => 'Sân hoặc khung giờ không hợp lệ.']);
        }

        $slotCountCalc = 0;
        $todayCalc = Carbon::today();
        $dateCalc = Carbon::createFromDate($year, $month, 1)->startOfDay();
        while ($dateCalc->month === $month) {
            if ($dateCalc->dayOfWeek === $dayOfWeek && $dateCalc >= $todayCalc) {
                $slotCountCalc++;
            }
            $dateCalc->addDay();
        }

        $pricePerSlot = $this->calculateSlotPrice(
            $field,
            $timeSlot->start_time,
            (int) $timeSlot->id
        );

        $slotCount = 0;
        $today = Carbon::today();
        $date = Carbon::createFromDate($year, $month, 1)->startOfDay();

        while ($date->month === $month) {
            if ($date->dayOfWeek === $dayOfWeek && $date >= $today) {
                $slotCount++;
            }
            $date->addDay();
        }

        if ($slotCount <= 0) {
            return back()->withInput()->withErrors(['error' => 'Không tìm thấy buổi đá nào hợp lệ từ hôm nay trở đi trong tháng đã chọn.']);
        }

        $totalAmount = $slotCount * $pricePerSlot;
        $payableNow = ($paymentType === 'full') ? $totalAmount : ($totalAmount * 0.50);

        $bookingCode = 'BM' . now()->format('YmdHis') . Str::upper(Str::random(3));

        try {
            DB::beginTransaction();

            $bookingData = $this->filterColumns('bookings', [
                'user_id' => $userId,
                'stadium_id' => $stadiumId,
                'booking_code' => $bookingCode,
                'code' => $bookingCode,
                'customer_name' => Auth::user()->name ?? 'Khách hàng',
                'customer_email' => Auth::user()->email,
                'customer_phone' => Auth::user()->phone ?? '0123456789',
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'deposit_amount' => $payableNow,
                'is_deposit_paid' => false,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_type' => ($paymentType === 'full') ? 'full' : 'deposit',
                'booking_type' => 'monthly',
                'paid_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $bookingId = DB::table('bookings')->insertGetId($bookingData);

            $date = Carbon::createFromDate($year, $month, 1)->startOfDay();
            while ($date->month === $month) {
                if ($date->dayOfWeek === $dayOfWeek && $date >= Carbon::today()) {
                    $bookingDateStr = $date->format('Y-m-d');

                    $isBooked = DB::table('booking_details as bd')
                        ->join('bookings as b', 'bd.booking_id', '=', 'b.id')
                        ->where('bd.field_id', $fieldId)
                        ->where('bd.time_slot_id', $timeSlotId)
                        ->whereDate('bd.booking_date', $bookingDateStr)
                        ->where('b.status', '!=', 'cancelled')
                        ->exists();

                    if (!$isBooked) {
                        $existsInCurrent = DB::table('booking_details')
                            ->where('booking_id', $bookingId)
                            ->where('field_id', $fieldId)
                            ->where('time_slot_id', $timeSlotId)
                            ->whereDate('booking_date', $bookingDateStr)
                            ->exists();

                        if (!$existsInCurrent) {
                            DB::table('booking_details')->insert([
                                'booking_id' => $bookingId,
                                'field_id' => $fieldId,
                                'time_slot_id' => $timeSlotId,
                                'booking_date' => $bookingDateStr,
                                'price' => $pricePerSlot,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
                $date->addDay();
            }

            DB::commit();

            return redirect()
                ->route('user.payment.show', $bookingId)
                ->with('success', 'Đơn đặt lịch tháng đã được tạo thành công. Vui lòng thanh toán để giữ lịch!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Lỗi khi tạo lịch tháng: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, int $booking)
    {
        $bookingData = DB::table('bookings')
            ->where('id', $booking)
            ->where('user_id', Auth::id())
            ->first();

        if (!$bookingData) {
            abort(404);
        }

        $status = $bookingData->status ?? 'pending';

        if ($status === 'completed' || $status === 'cancelled') {
            return back()->withErrors([
                'delete_booking' => 'Đơn hàng này không thể thực hiện hủy.',
            ]);
        }

        $totalMoneyRow = (float)($bookingData->total_amount ?? $bookingData->total_price ?? $bookingData->final_amount ?? 0);
        $paidAmt = (float)($bookingData->paid_amount ?? 0);
        $depositAmt = (float)($bookingData->deposit_amount ?? 0);

        $pType = strtolower((string)($bookingData->payment_type ?? ''));
        $pStatus = strtolower((string)($bookingData->payment_status ?? ''));
        $isPaidFull = ($pType === 'full' || in_array($pStatus, ['paid', 'completed']) || ($paidAmt >= $totalMoneyRow && $totalMoneyRow > 0));

        $estRefund = 0;
        
        $firstDetail = DB::table('booking_details')->where('booking_id', $bookingData->id)->orderBy('booking_date')->first();
        $bookingDate = $firstDetail->booking_date ?? ($bookingData->booking_date ?? now()->format('Y-m-d'));
        
        $firstSlot = $firstDetail ? DB::table('time_slots')->where('id', $firstDetail->time_slot_id)->first() : null;
        $startTime = $firstSlot->start_time ?? ($bookingData->start_time ?? '00:00:00');

        try {
            $mDate = Carbon::parse($bookingDate . ' ' . $startTime);
            $hrs = Carbon::now()->diffInHours($mDate, false);

            if ($status === 'pending') {
                $estRefund = 0;
            } elseif ($hrs >= 24) {
                // Lấy CHÍNH XÁC số tiền thực tế khách đã đóng (nếu khách đóng cọc thì lấy tiền cọc, đóng đủ lấy tổng tiền)
                $actualPaidMoney = $paidAmt > 0 ? $paidAmt : ($depositAmt > 0 ? $depositAmt : $totalMoneyRow);
                
                // Hoàn 50% hoặc 70% tùy thuộc vào việc khách đã đóng full hay đóng cọc
                $estRefund = $isPaidFull ? ($totalMoneyRow * 0.70) : ($actualPaidMoney * 0.50);
            } else {
                $estRefund = $isPaidFull ? ($totalMoneyRow * 0.30) : 0;
            }
        } catch (\Throwable) {
            $estRefund = 0;
        }

        $bankName = trim($request->input('bank_name', ''));
        $bankAccount = trim($request->input('bank_account_number', ''));
        $bankHolder = trim($request->input('bank_account_holder', ''));
        $cancelReason = trim($request->input('cancel_reason', 'Không có lý do'));

        $bankInfoSummary = "";
        if ($estRefund > 0) {
            if (!$bankName || !$bankAccount || !$bankHolder) {
                return back()->withErrors(['delete_booking' => 'Vui lòng điền đầy đủ thông tin tài khoản ngân hàng để nhận tiền hoàn.']);
            }
            $bankInfoSummary = "\nNgân hàng: {$bankName}\nSố STK: {$bankAccount}\nChủ STK: {$bankHolder}\nLý do hủy: {$cancelReason}";
        } else {
            $bankInfoSummary = "\nLý do hủy: {$cancelReason} (Đơn không hoàn tiền)";
        }

        $existingNote = $bookingData->note ?? '';
        $finalNote = trim($existingNote . "\n--- THÔNG TIN HỦY SÂN & HOÀN TIỀN ---" . $bankInfoSummary);

        try {
            DB::beginTransaction();

            DB::table('bookings')
                ->where('id', $bookingData->id)
                ->update([
                    'status' => 'cancelled',
                    'refund_amount' => $estRefund,
                    'refund_status' => ($estRefund > 0) ? 'pending' : 'none',
                    'note' => $finalNote,
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

            DB::table('notifications')->insert([
                'user_id' => Auth::id(),
                'title' => 'Yêu cầu hủy đơn & hoàn tiền',
                'content' => 'Khách hàng vừa hủy đơn #' . $bookingData->id . ($estRefund > 0 ? " và yêu cầu hoàn số tiền " . number_format($estRefund, 0, ',', '.') . "đ." : "."),
                'type' => 'booking',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('user.bookings.index')
                ->with('success', $estRefund > 0 ? 'Đã gửi yêu cầu hủy đơn và hoàn tiền tới Admin thành công. Vui lòng chờ xét duyệt.' : 'Hủy đơn đặt sân thành công.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'delete_booking' => 'Không thể xử lý hủy đơn: ' . $e->getMessage(),
            ]);
        }
    }

    public function disputeRefundWithImage(Request $request, int $id)
    {
        $request->validate([
            'dispute_reason' => 'required|string|max:500',
            'dispute_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'dispute_reason.required' => 'Vui lòng nhập nội dung sự cố bạn gặp phải.',
        ]);

        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        }

        $imagePath = null;
        if ($request->hasFile('dispute_image')) {
            $file = $request->file('dispute_image');
            $filename = 'dispute_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/disputes'), $filename);
            $imagePath = 'uploads/disputes/' . $filename;
        }

        try {
            DB::beginTransaction();

            $existingNote = $booking->note ?? '';
            $disputeText = "\n[BÁO CÁO SỰ CỐ]: " . $request->input('dispute_reason');
            if ($imagePath) {
                $disputeText .= " (Ảnh minh chứng: " . $imagePath . ")";
            }

            $updateData = [
                'refund_status' => 'disputed',
                'user_dispute_reason' => $request->input('dispute_reason'),
                'note' => $existingNote . $disputeText,
                'updated_at' => now(),
            ];

            if ($imagePath && Schema::hasColumn('bookings', 'dispute_image')) {
                $updateData['dispute_image'] = $imagePath;
            }

            DB::table('bookings')->where('id', $id)->update($updateData);

            DB::table('notifications')->insert([
                'user_id' => $booking->user_id,
                'title' => 'Khách hàng báo cáo sự cố hoàn tiền',
                'content' => 'Đơn #' . $id . ' có phản hồi sự cố từ khách: "' . $request->input('dispute_reason') . '"',
                'type' => 'payment',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Đã gửi báo cáo sự cố tới Admin thành công. Bộ phận hỗ trợ sẽ kiểm tra lại!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi gửi báo cáo: ' . $e->getMessage());
        }
    }

    public function confirmRefund(int $id)
    {
        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            DB::table('bookings')->where('id', $id)->update([
                'refund_status' => 'confirmed_by_user',
                'updated_at' => now(),
            ]);

            DB::table('notifications')->insert([
                'user_id' => $booking->user_id,
                'title' => 'Khách hàng đã xác nhận nhận tiền hoàn',
                'content' => 'Khách hàng đã xác nhận nhận đủ tiền hoàn cho đơn #' . $id . '. Giao dịch hoàn tất.',
                'type' => 'payment',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Xác nhận đã nhận tiền thành công. Cảm ơn bạn!');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function disputeRefund(Request $request, int $id)
    {
        return back();
    }

    public function cancelTimeout(int $id)
    {
        $booking = DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($booking) {
            DB::table('bookings')->where('id', $id)->update([
                'status' => 'cancelled',
                'note' => trim(($booking->note ?? '') . "\n--- ĐƠN HÀNG TỰ ĐỘNG HỦY DO QUÁ HẠN THANH TOÁN (5 PHÚT) ---"),
                'updated_at' => now(),
            ]);

            if (Schema::hasTable('booking_details') && Schema::hasColumn('booking_details', 'status')) {
                DB::table('booking_details')
                    ->where('booking_id', $id)
                    ->update([
                        'status' => 'cancelled',
                        'updated_at' => now(),
                    ]);
            }
        }

        return response()->json(['success' => true]);
    }

    private function convertBookingDate(?string $date): ?string
    {
        if (!$date) return null;
        $date = trim($date);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return $date;
        if (preg_match('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', $date, $matches)) {
            return "{$matches[3]}-" . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . "-" . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        }
        return null;
    }

    private function splitTimeSlot(?string $timeSlot): array
    {
        if (!$timeSlot) {
            return [null, null];
        }

        $parts = explode('-', $timeSlot);
        return [trim($parts[0] ?? ''), trim($parts[1] ?? '')];
    }

    private function calculateSlotPrice(object $field, ?string $startTime, ?int $timeSlotId = null): float
    {
        if ($timeSlotId !== null) {
            $fieldPrice = DB::table('field_time_slot_prices')
                ->where('field_id', $field->id)
                ->where('time_slot_id', $timeSlotId)
                ->value('price');

            if ($fieldPrice !== null) {
                return (float) $fieldPrice;
            }

            $stadiumPrice = DB::table('stadium_time_slot_prices')
                ->where('stadium_id', $field->stadium_id)
                ->where('time_slot_id', $timeSlotId)
                ->value('price');

            if ($stadiumPrice !== null) {
                return (float) $stadiumPrice;
            }
        }

        return (float) ($field->price_per_hour ?? 350000);
    }

    private function filterColumns(string $table, array $data): array
    {
        if (!Schema::hasTable($table)) return [];
        $result = [];
        foreach ($data as $column => $value) {
            if (Schema::hasColumn($table, $column)) {
                $result[$column] = $value;
            }
        }
        return $result;
    }
}