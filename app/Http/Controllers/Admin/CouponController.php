<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function loadDataTable()
    {
        $data = Coupon::latest()->get();

        return response()->json(['data' => $data]);
    }


    function index()
    {
        return Inertia::render('Admin/Ecommerce/Coupon/Show');
    }
    public function delete(Request $request)
    {
        try {
            Coupon::whereIn('id', $request->dataId)->delete();
            return response()->json(['message' => 'Delete Coupon Success.'], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function store(Request $request)
    {
        // Xác thực dữ liệu từ form
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code',
            'type' => 'required|in:percentage,amount',
            'qnt' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'is_duration' => 'required|integer|in:1,0',

            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ], [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'Coupon code must be unique.',
            'type.required' => 'Discount type is required.',
            'qnt.required' => 'Quantity is required.',
            'qnt.numeric' => 'Quantity must be a number.',
            'type.in' => 'Discount type must be either percentage or amount.',
            'value.required' => 'Discount value is required.',
            'value.numeric' => 'Discount value must be a number.',
            'value.min' => 'Discount value cannot be negative.',
            'start_time.after_or_equal' => 'Start time cannot be in the past.',
            'end_time.after' => 'End time must be after start time.',
        ]);

        try {
            // Tạo mới Coupon và lưu vào cơ sở dữ liệu
            $coupon = new Coupon();
            $coupon->code = $validatedData['code'];
            $coupon->type = $validatedData['type'];
            $coupon->qnt = $validatedData['qnt'];
            $coupon->value = $validatedData['value'];
            $coupon->is_duration = $validatedData['is_duration'];

            // Kiểm tra và gán giá trị thời gian bắt đầu và kết thúc
            if ($validatedData['is_duration'] == 1) {
                $coupon->start_time = Carbon::parse($validatedData['start_time']);
                $coupon->end_time = Carbon::parse($validatedData['end_time']);
            } else {
                $coupon->start_time = null;
                $coupon->end_time = null;
            }

            $coupon->save();

            // Trả về response thành công
            return response()->json(['message' => 'Coupon created successfully.'], 201);

        } catch (\Exception $e) {
            // Trả về lỗi nếu có ngoại lệ
            return response()->json(['message' => 'Failed to create coupon.', 'error' => $e->getMessage()], 500);
        }
    }
    public function edit($id)
    {
        try {
            $Coupon = Coupon::find($id);
            return Inertia::render('Admin/Ecommerce/Coupon/Edit', ['data' => $Coupon]);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    public function update(Request $request, $id)
    {
        // Xác thực dữ liệu từ form
        $validatedData = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $id,
            'type' => 'required|in:percentage,amount',
            'qnt' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'is_duration' => 'required|integer|in:1,0',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
        ], [
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'Coupon code must be unique.',
            'type.required' => 'Discount type is required.',
            'type.in' => 'Discount type must be either percentage or amount.',
            'qnt.required' => 'Quantity is required.',
            'qnt.numeric' => 'Quantity must be a number.',
            'value.required' => 'Discount value is required.',
            'value.numeric' => 'Discount value must be a number.',
            'value.min' => 'Discount value cannot be negative.',
            'start_time.date' => 'Start time must be a valid date.',
            'end_time.after' => 'End time must be after start time.',
        ]);

        try {
            // Tìm coupon cần cập nhật
            $coupon = Coupon::find($id);
            // Cập nhật dữ liệu coupon
            $coupon->code = $validatedData['code'];
            $coupon->type = $validatedData['type'];
            $coupon->qnt = $validatedData['qnt'];
            $coupon->value = $validatedData['value'];
            $coupon->is_duration = $validatedData['is_duration'];

            // Kiểm tra và gán giá trị thời gian bắt đầu và kết thúc
            if ($validatedData['is_duration'] == 1) {
                $coupon->start_time = Carbon::parse($validatedData['start_time']);
                $coupon->end_time = Carbon::parse($validatedData['end_time']);
            } else {
                $coupon->start_time = null;
                $coupon->end_time = null;
            }

            $coupon->save();

            // Trả về response thành công
            return response()->json(['message' => 'Coupon updated successfully.'], 200);

        } catch (\Exception $e) {
            // Trả về lỗi nếu có ngoại lệ
            return response()->json(['message' => 'Failed to update coupon.', 'error' => $e->getMessage()], 500);
        }
    }

}
