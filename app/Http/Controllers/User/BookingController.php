<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
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

    public function availability(Request $request, $stadium)
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

        $depositAmount = $finalAmount * 0.3;
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

    public function destroy(Request $request, $booking)
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

        if ($status === 'completed' || $status === 'cancelled') {
            return back()->withErrors([
                'delete_booking' => 'Đơn hàng này không thể thực hiện hủy.',
            ]);
        }

        $refundAmount = 0;
        $refundNote = '';

        if ($status === 'pending') {
            $refundAmount = 0;
            $refundNote = 'Đơn hủy khi chưa hoàn tất thanh toán.';
        } else {
            $bookingDate = $bookingData->detail_booking_date ?? $bookingData->booking_date ?? null;
            $startTime = $bookingData->slot_start_time ?? $bookingData->start_time ?? '00:00:00';

            if (!$bookingDate) {
                return back()->withErrors(['delete_booking' => 'Không tìm thấy thông tin ngày đặt sân để tính phí hủy.']);
            }

            $matchDateTime = Carbon::parse($bookingDate . ' ' . $startTime);
            $now = Carbon::now();
            $hoursUntilMatch = $now->diffInHours($matchDateTime, false);

            if ($hoursUntilMatch < 0) {
                return back()->withErrors(['delete_booking' => 'Trận đấu đã hoặc đang diễn ra, không thể hủy sân!']);
            }

            $totalPrice = (float) ($bookingData->total_amount ?? $bookingData->total_price ?? $bookingData->final_amount ?? 0);
            $depositAmount = (float) ($bookingData->deposit_amount ?? ($totalPrice * 0.3));

            // KIỂM TRA CHÍNH XÁC THANH TOÁN 100%
            $pType = strtolower((string)($bookingData->payment_type ?? ''));
            $pStatus = strtolower((string)($bookingData->payment_status ?? ''));
            $paidAmt = (float)($bookingData->paid_amount ?? 0);

            $isPaidFull = (
                $pType === 'full' || 
                $pType === 'full_payment' || 
                in_array($pStatus, ['paid', 'completed', 'paid_full']) ||
                ($paidAmt >= $totalPrice && $totalPrice > 0) ||
                ($status === 'confirmed' && $pType !== 'deposit')
            );

            if ($hoursUntilMatch >= 24) {
                if ($isPaidFull) {
                    $refundAmount = $totalPrice * 0.70;
                    $refundNote = 'Hủy trước 24h bóng lăn (Đã trả 100%): Hoàn 70% tổng tiền sân.';
                } else {
                    $refundAmount = $depositAmount * 0.50;
                    $refundNote = 'Hủy trước 24h bóng lăn (Đã cọc 30%): Hoàn 50% tiền cọc.';
                }
            } else {
                if ($isPaidFull) {
                    $refundAmount = $totalPrice * 0.30;
                    $refundNote = 'Hủy sát giờ < 24h bóng lăn (Đã trả 100%): Nhận lại 30% tổng tiền sân.';
                } else {
                    $refundAmount = 0;
                    $refundNote = 'Hủy sát giờ < 24h bóng lăn (Đã cọc 30%): Mất 100% tiền cọc.';
                }
            }
        }

        if ($refundAmount > 0) {
            $request->validate([
                'bank_name' => 'required|string|max:100',
                'bank_account_number' => 'required|string|max:50',
                'bank_account_holder' => 'required|string|max:100',
                'cancel_reason' => 'nullable|string|max:500',
            ], [
                'bank_name.required' => 'Vui lòng chọn hoặc nhập tên ngân hàng nhận tiền hoàn.',
                'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
                'bank_account_holder.required' => 'Vui lòng nhập tên chủ tài khoản.',
            ]);
        }

        try {
            DB::beginTransaction();

            $cancelReasonText = $request->input('cancel_reason');
            $fullCancelNote = $refundNote;

            if ($refundAmount > 0) {
                $bankInfo = "\n--- THÔNG TIN HOÀN TIỀN CỦA KHÁCH ---"
                    . "\n- Ngân hàng: " . $request->input('bank_name')
                    . "\n- Số STK: " . $request->input('bank_account_number')
                    . "\n- Chủ STK: " . mb_strtoupper($request->input('bank_account_holder'))
                    . "\n- Hình thức ban đầu: " . ($isPaidFull ? 'Thanh toán 100% tiền sân' : 'Đặt cọc 30% tiền sân')
                    . "\n- Lý do hủy: " . ($cancelReasonText ?? 'Không có lý do');
                
                $fullCancelNote .= $bankInfo;
            }

            $updateData = [
                'status' => 'cancelled',
                'refund_amount' => $refundAmount,
                'cancel_note' => $fullCancelNote,
                'refund_status' => $refundAmount > 0 ? 'pending' : 'none',
                'updated_at' => now(),
            ];

            DB::table('bookings')
                ->where('id', $bookingData->id)
                ->where('user_id', Auth::id())
                ->update($updateData);

            if (Schema::hasTable('booking_details') && Schema::hasColumn('booking_details', 'status')) {
                DB::table('booking_details')
                    ->where('booking_id', $bookingData->id)
                    ->update([
                        'status' => 'cancelled',
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            $msg = 'Hủy đơn đặt sân thành công.';
            if ($refundAmount > 0) {
                $msg .= ' Yêu cầu hoàn lại ' . number_format($refundAmount, 0, ',', '.') . 'đ đã được gửi đến Admin kèm thông tin tài khoản ngân hàng của bạn!';
            } else if ($status !== 'pending') {
                $msg .= ' ' . $refundNote;
            }

            return redirect()
                ->route('user.bookings.index')
                ->with('success', $msg);

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'delete_booking' => 'Không thể xử lý hủy đơn đặt sân. Vui lòng thử lại.',
            ]);
        }
    }

    public function confirmRefund($id)
    {
        DB::table('bookings')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'refund_status' => 'confirmed_by_user',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận! Giao dịch hủy sân và hoàn tiền đã hoàn tất.');
    }

    public function disputeRefund(Request $request, $id)
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

    private function parseMoney($value): float
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