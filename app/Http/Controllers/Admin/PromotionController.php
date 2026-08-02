<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::orderBy('id', 'desc')->paginate(10);

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Z0-9_-]+$/', 'unique:promotions,code'],
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/.*\S.*/'],
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'gt:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Giá trị giảm giá phải nhỏ hơn hoặc bằng 100 khi chọn phần trăm.');
                    }
                },
            ],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0', function ($attribute, $value, $fail) use ($request) {
                if ($request->discount_type === 'percent' && !is_null($value) && $value <= 0) {
                    $fail('Giá trị giảm tối đa phải lớn hơn 0 khi chọn giảm giá theo phần trăm.');
                }
            }],
            'min_order_amount' => 'nullable|integer|min:0',
            'quantity' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:0,1',
        ], [
            'code.required' => 'Vui lòng nhập mã khuyến mãi.',
            'code.min' => 'Mã khuyến mãi phải có ít nhất 3 ký tự.',
            'code.max' => 'Mã khuyến mãi tối đa 50 ký tự.',
            'code.regex' => 'Mã khuyến mãi chỉ gồm chữ hoa A-Z, số 0-9, gạch ngang và gạch dưới.',
            'code.unique' => 'Mã khuyến mãi này đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên khuyến mãi.',
            'name.min' => 'Tên khuyến mãi phải có ít nhất 3 ký tự.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm giá.',
            'discount_value.numeric' => 'Giá trị giảm giá phải là số.',
            'discount_value.gt' => 'Giá trị giảm giá phải lớn hơn 0.',
            'max_discount_amount.numeric' => 'Giá trị giảm tối đa phải là số.',
            'max_discount_amount.min' => 'Giá trị giảm tối đa phải lớn hơn hoặc bằng 0.',
            'min_order_amount.integer' => 'Số tiền tối thiểu phải là số nguyên.',
            'min_order_amount.min' => 'Số tiền tối thiểu phải lớn hơn hoặc bằng 0.',
            'quantity.integer' => 'Số lần sử dụng phải là số nguyên.',
            'quantity.min' => 'Số lần sử dụng phải lớn hơn hoặc bằng 1.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        Promotion::create([
            'code' => strtoupper($request->code),
            'name' => trim($request->name),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount_amount' => $request->max_discount_amount,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'quantity' => $request->quantity,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Thêm khuyến mãi thành công.');
    }

    public function show($id)
    {
        $promotion = Promotion::findOrFail($id);

        return view('admin.promotions.show', compact('promotion'));
    }

    public function edit($id)
    {
        $promotion = Promotion::findOrFail($id);

        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $request->validate([
            'code' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Z0-9_-]+$/', 'unique:promotions,code,' . $id],
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/.*\S.*/'],
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'gt:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percent' && $value > 100) {
                        $fail('Giá trị giảm giá phải nhỏ hơn hoặc bằng 100 khi chọn phần trăm.');
                    }
                },
            ],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0', function ($attribute, $value, $fail) use ($request) {
                if ($request->discount_type === 'percent' && !is_null($value) && $value <= 0) {
                    $fail('Giá trị giảm tối đa phải lớn hơn 0 khi chọn giảm giá theo phần trăm.');
                }
            }],
            'min_order_amount' => 'nullable|integer|min:0',
            'quantity' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:0,1',
        ], [
            'code.required' => 'Vui lòng nhập mã khuyến mãi.',
            'code.min' => 'Mã khuyến mãi phải có ít nhất 3 ký tự.',
            'code.max' => 'Mã khuyến mãi tối đa 50 ký tự.',
            'code.regex' => 'Mã khuyến mãi chỉ gồm chữ hoa A-Z, số 0-9, gạch ngang và gạch dưới.',
            'code.unique' => 'Mã khuyến mãi này đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên khuyến mãi.',
            'name.min' => 'Tên khuyến mãi phải có ít nhất 3 ký tự.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm giá.',
            'discount_value.numeric' => 'Giá trị giảm giá phải là số.',
            'discount_value.gt' => 'Giá trị giảm giá phải lớn hơn 0.',
            'max_discount_amount.numeric' => 'Giá trị giảm tối đa phải là số.',
            'max_discount_amount.min' => 'Giá trị giảm tối đa phải lớn hơn hoặc bằng 0.',
            'min_order_amount.integer' => 'Số tiền tối thiểu phải là số nguyên.',
            'min_order_amount.min' => 'Số tiền tối thiểu phải lớn hơn hoặc bằng 0.',
            'quantity.integer' => 'Số lần sử dụng phải là số nguyên.',
            'quantity.min' => 'Số lần sử dụng phải lớn hơn hoặc bằng 1.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
            'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
            'end_date.date' => 'Ngày kết thúc không hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        $promotion->update([
            'code' => strtoupper($request->code),
            'name' => trim($request->name),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount_amount' => $request->max_discount_amount,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'quantity' => $request->quantity,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Cập nhật khuyến mãi thành công.');
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();

        return redirect()
            ->route('admin.promotions.index')
            ->with('success', 'Xóa khuyến mãi thành công.');
    }
}
