<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $stadium)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ], [
            'field_id.required' => 'Vui lòng chọn sân để đánh giá.',
            'field_id.exists' => 'Sân đánh giá không hợp lệ.',
            'rating.required' => 'Vui lòng chọn điểm đánh giá.',
            'rating.integer' => 'Điểm đánh giá phải là số nguyên.',
            'rating.min' => 'Điểm đánh giá tối thiểu là 1.',
            'rating.max' => 'Điểm đánh giá tối đa là 5.',
        ]);

        $field = Field::findOrFail($request->field_id);

        if ($field->stadium_id != $stadium) {
            return back()->withInput()->withErrors([
                'field_id' => 'Sân không thuộc cơ sở này.',
            ]);
        }

        Review::create([
            'user_id' => Auth::id(),
            'field_id' => $field->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => true,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá.');
    }
}
