<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'field.stadium'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Đã xóa đánh giá thành công.');
    }
}
