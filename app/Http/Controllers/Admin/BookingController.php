<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::query()
            ->with([
                'user',
                'bookingDetails.field',
                'bookingDetails.timeSlot',
            ])
            ->orderByDesc('id');

        $this->applyFilters($query, $request);

        $bookings = $query
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Booking::query()->count(),
            'pending' => Booking::query()
                ->where('status', 'pending')
                ->count(),
            'confirmed' => Booking::query()
                ->where('status', 'confirmed')
                ->count(),
            'checked_in' => Booking::query()
                ->where('usage_status', 'checked_in')
                ->count(),
            'completed' => Booking::query()
                ->where('status', 'completed')
                ->count(),
            'no_show' => Schema::hasColumn('bookings', 'no_show_at')
                ? Booking::query()->whereNotNull('no_show_at')->count()
                : 0,
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
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

        $statusHistories = collect();

        if (Schema::hasTable('booking_status_histories')) {
            $statusHistories = DB::table('booking_status_histories')
                ->where('booking_id', $booking->id)
                ->orderByDesc('occurred_at')
                ->orderByDesc('id')
                ->limit(30)
                ->get();
        }

        return view('admin.bookings.show', compact(
            'booking',
            'bookingDetails',
            'bookingServices',
            'payments',
            'bookingReview',
            'statusHistories',
        ));
    }

    /**
     * Admin chỉ xử lý ngoại lệ hoàn tiền.
     * Check-in do khách thực hiện; check-out/no-show do Scheduler xử lý.
     */
    public function processRefund(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'refund_proof_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'refund_proof_note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ], [
            'refund_proof_image.required' => 'Vui lòng tải ảnh chứng từ hoàn tiền.',
            'refund_proof_image.image' => 'Chứng từ hoàn tiền phải là hình ảnh.',
            'refund_proof_image.max' => 'Ảnh chứng từ không được vượt quá 5 MB.',
        ]);

        $path = $data['refund_proof_image']->store('refunds', 'public');

        try {
            DB::transaction(function () use ($id, $path, $data): void {
                /** @var Booking $booking */
                $booking = Booking::query()
                    ->lockForUpdate()
                    ->findOrFail($id);

                abort_unless(
                    strtolower((string) $booking->status) === 'cancelled'
                    && (float) ($booking->refund_amount ?? 0) > 0,
                    422,
                    'Đơn này không thuộc luồng cần hoàn tiền.',
                );

                $updates = [];

                if (Schema::hasColumn('bookings', 'refund_status')) {
                    $updates['refund_status'] = 'completed';
                }

                if (Schema::hasColumn('bookings', 'refund_proof_image')) {
                    $updates['refund_proof_image'] = 'storage/'.$path;
                }

                if (Schema::hasColumn('bookings', 'refund_proof')) {
                    $updates['refund_proof'] = $path;
                }

                if (Schema::hasColumn('bookings', 'refund_proof_note')) {
                    $updates['refund_proof_note'] = $data['refund_proof_note'] ?? null;
                }

                if (Schema::hasColumn('bookings', 'refund_processed_at')) {
                    $updates['refund_processed_at'] = now();
                }

                if ($updates !== []) {
                    $booking->forceFill($updates)->save();
                }
            }, 3);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }

        return back()->with(
            'success',
            'Đã lưu chứng từ hoàn tiền. Khách hàng có thể kiểm tra và xác nhận.',
        );
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

        if (
            Schema::hasColumn('bookings', 'invoice_issued_at')
            && $booking->invoice_issued_at === null
        ) {
            $booking->forceFill([
                'invoice_issued_at' => now(),
            ])->save();
        }

        return view('admin.bookings.invoice', compact('booking'));
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->string('status')->toString(),
            );
        }

        if ($request->filled('usage_status')) {
            $query->where(
                'usage_status',
                $request->string('usage_status')->toString(),
            );
        }

        if (
            $request->filled('payment_status')
            && Schema::hasColumn('bookings', 'payment_status')
        ) {
            $query->where(
                'payment_status',
                $request->string('payment_status')->toString(),
            );
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->string('keyword')->toString());

            $query->where(function (Builder $bookingQuery) use ($keyword): void {
                if (ctype_digit($keyword)) {
                    $bookingQuery->orWhere('id', (int) $keyword);
                }

                if (Schema::hasColumn('bookings', 'booking_code')) {
                    $bookingQuery->orWhere(
                        'booking_code',
                        'like',
                        "%{$keyword}%",
                    );
                }

                if (Schema::hasColumn('bookings', 'customer_name')) {
                    $bookingQuery->orWhere(
                        'customer_name',
                        'like',
                        "%{$keyword}%",
                    );
                }

                if (Schema::hasColumn('bookings', 'customer_phone')) {
                    $bookingQuery->orWhere(
                        'customer_phone',
                        'like',
                        "%{$keyword}%",
                    );
                }

                $bookingQuery->orWhereHas(
                    'user',
                    function (Builder $userQuery) use ($keyword): void {
                        $userQuery
                            ->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    },
                );
            });
        }
    }
}