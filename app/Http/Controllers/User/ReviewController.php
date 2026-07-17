<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $stadium)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::with(['bookingDetails.field', 'review'])
            ->whereKey($request->booking_id)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->first();

        if (!$booking) {
            return back()->withInput()->withErrors([
                'booking_id' => 'Chỉ có thể đánh giá đơn đặt sân của bạn sau khi đã hoàn thành.',
            ]);
        }

        $bookingDetail = $booking->bookingDetails
            ->first(fn ($detail) => $detail->field?->stadium_id == $stadium);

        if (!$bookingDetail) {
            return back()->withInput()->withErrors([
                'booking_id' => 'Lần đặt sân không thuộc cơ sở này.',
            ]);
        }

        if ($booking->review) {
            return back()->withErrors([
                'booking_id' => 'Lần đặt sân này đã được đánh giá.',
            ]);
        }

        $review = Review::firstOrCreate(['booking_id' => $booking->id], [
            'user_id' => Auth::id(),
            'field_id' => $bookingDetail->field_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => true,
        ]);

        if (!$review->wasRecentlyCreated) {
            return back()->withErrors([
                'booking_id' => 'Lần đặt sân này đã được đánh giá.',
            ]);
        }

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá.');
    }
}
