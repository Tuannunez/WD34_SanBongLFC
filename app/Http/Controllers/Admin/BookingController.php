<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::query()
            ->with(['user', 'bookingDetails.field', 'bookingDetails.timeSlot', 'latestPayment'])
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('usage_status')) {
            $query->where('usage_status', $request->string('usage_status')->toString());
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->string('keyword')->toString());

            $query->where(function ($bookingQuery) use ($keyword): void {
                $bookingQuery->where('id', ctype_digit($keyword) ? (int) $keyword : 0);

                if (Schema::hasColumn('bookings', 'booking_code')) {
                    $bookingQuery->orWhere('booking_code', 'like', "%{$keyword}%");
                }

                if (Schema::hasColumn('bookings', 'customer_name')) {
                    $bookingQuery->orWhere('customer_name', 'like', "%{$keyword}%");
                }

                if (Schema::hasColumn('bookings', 'customer_phone')) {
                    $bookingQuery->orWhere('customer_phone', 'like', "%{$keyword}%");
                }

                $bookingQuery->orWhereHas('user', function ($userQuery) use ($keyword): void {
                    $userQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            });
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'user',
            'bookingDetails.field',
            'bookingDetails.timeSlot',
            'bookingServices.service',
            'payments.paymentMethod',
            'review',
        ]);

        $bookingDetails = $booking->bookingDetails ?? collect();
        $bookingServices = $booking->bookingServices ?? collect();
        $payments = $booking->payments ?? collect();
        $bookingReview = $booking->review;

        return view('admin.bookings.show', compact(
            'booking',
            'bookingDetails',
            'bookingServices',
            'payments',
            'bookingReview'
        ));
    }

    /**
     * Admin chỉ xử lý ngoại lệ hoàn tiền; không can thiệp check-in/check-out.
     */
    public function processRefund(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'refund_bill' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'refund_bill.required' => 'Vui lòng tải lên chứng từ hoàn tiền.',
            'refund_bill.image' => 'Chứng từ hoàn tiền phải là hình ảnh.',
            'refund_bill.max' => 'Ảnh chứng từ không được vượt quá 5 MB.',
        ]);

        $path = $data['refund_bill']->store('refunds', 'public');

        try {
            DB::transaction(function () use ($id, $path): void {
                /** @var Booking $booking */
                $booking = Booking::query()->lockForUpdate()->findOrFail($id);

                abort_unless(
                    $booking->status === 'cancelled'
                    && (float) ($booking->refund_amount ?? 0) > 0
                    && in_array((string) ($booking->refund_status ?? 'none'), ['pending', 'disputed'], true),
                    422,
                    'Đơn này không thuộc luồng cần hoàn tiền.'
                );

                $booking->forceFill([
                    'refund_status' => 'pending',
                    'refund_proof' => $path,
                    'refund_processed_at' => now(),
                ])->save();

                $this->notifyRefundProcessed($booking);
            }, 3);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }

        return back()->with('success', 'Đã ghi nhận chứng từ hoàn tiền. Khách hàng có thể xác nhận hoặc khiếu nại.');
    }

    public function invoice(int $id): View
    {
        /** @var Booking $booking */
        $booking = Booking::query()
            ->with([
                'user',
                'bookingDetails.field',
                'bookingDetails.timeSlot',
                'bookingServices.service',
                'payments.paymentMethod',
            ])
            ->findOrFail($id);

        if (Schema::hasColumn('bookings', 'invoice_issued_at') && $booking->invoice_issued_at === null) {
            $booking->forceFill(['invoice_issued_at' => now()])->save();
        }

        return view('admin.bookings.invoice', compact('booking'));
    }

    private function notifyRefundProcessed(Booking $booking): void
    {
        if (!Schema::hasTable('notifications') || $booking->user_id === null) {
            return;
        }

        $data = collect([
            'user_id' => $booking->user_id,
            'title' => 'Đã xử lý hoàn tiền',
            'content' => 'Hoàn tiền cho đơn ' . ($booking->booking_code ?? $booking->code ?? ('#' . $booking->id)) . ' đã được xử lý. Vui lòng kiểm tra và xác nhận.',
            'type' => 'payment',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ])->filter(fn ($value, $column): bool => Schema::hasColumn('notifications', (string) $column))->all();

        if ($data !== []) {
            DB::table('notifications')->insert($data);
        }
    }
}
