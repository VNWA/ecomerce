<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Cache;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function showAll()
    {

        return Inertia::render('Admin/Setting/Show');
    }
    public function showFrontendUrls()
    {
        $frontend_urls = Setting::where('type', 'frontend_urls')->first();
        $data = implode("\n", $frontend_urls->json_value);
        return Inertia::render('Admin/Setting/FrontendUrls', ['data' => $data]);
    }
    public function updateFrontendUrls(Request $request)
    {
        // Validate đầu vào
        $validated = $request->validate([
            'content' => 'required|string', // Dữ liệu phải là chuỗi và không được để trống
        ]);

        // Lấy hoặc tạo mới bản ghi 'frontend_urls' trong bảng setting
        $setting = Setting::firstOrCreate(
            ['type' => 'frontend_urls'],
            ['json_value' => '[]']
        );

        // Chuyển chuỗi thành mảng và loại bỏ các dòng trống hoặc khoảng trắng
        $urls = array_filter(array_map('trim', explode("\n", $validated['content'])));

        // Cập nhật giá trị json_value
        $setting->json_value = $urls;
        $setting->save();

        // Làm mới cache sau khi cập nhật
        Cache::forget('frontend_urls');  // Xóa cache cũ
        Cache::put('frontend_urls', $urls, 86400);  // Lưu cache mới trong 1 ngày

        // Trả về dữ liệu cho view
        return Inertia::render('Admin/Setting/FrontendUrls', ['data' => $validated['content']]);
    }
    public function showConfigStripe()
    {
        $stripe = Setting::where('type', 'stripe')->first();
        return Inertia::render('Admin/Setting/Stripe', ['data' => $stripe->json_value]);
    }
    public function updateConfigStripe(Request $request)
    {
        // Validate đầu vào
        $request->validate([
            'secret_key' => 'required|string', // Dữ liệu phải là chuỗi và không được để trống
            'publish_key' => 'required|string', // Dữ liệu phải là chuỗi và không được để trống
            'webhook_key' => 'required|string', // Dữ liệu phải là chuỗi và không được để trống
        ]);



        // Lấy hoặc tạo mới bản ghi 'frontend_urls' trong bảng setting
        $setting = Setting::firstOrCreate(
            ['type' => 'stripe'],
            ['json_value' => '[]']
        );
        $value = [
            'secret_key' => $request->secret_key,
            'publish_key' => $request->publish_key,
            'webhook_key' => $request->webhook_key,
        ];
        $setting->json_value = $value;
        $setting->save();

        // Làm mới cache sau khi cập nhật
        Cache::forget('stripe');  // Xóa cache cũ
        Cache::put('stripe', $value, 86400);  // Lưu cache mới trong 1 ngày

        // Trả về dữ liệu cho view
        return redirect()->back()->with('success', 'Stripe config updated successfully!');

    }
    public function showConfigPaypal()
    {
        $paypal = Setting::where('type', 'paypal')->first();
        return Inertia::render('Admin/Setting/Paypal', ['data' => $paypal->json_value]);
    }
    public function updateConfigPaypal(Request $request)
    {
        // Validate đầu vào
        $request->validate([
            'mode' => 'required|string',
            'app_id' => 'required|string',
            'currency' => 'required|string',
            'locale' => 'required|string',
            'webhook_id' => 'required|string',
            'secret_key' => 'required|string',
            'client_id' => 'required|string',
        ]);


        // Lấy hoặc tạo mới bản ghi 'frontend_urls' trong bảng setting
        $setting = Setting::firstOrCreate(
            ['type' => 'paypal'],
            ['json_value' => '[]']
        );
        $value = [
            'mode' => $request->mode,
            'app_id' => $request->app_id,
            'currency' => $request->currency,
            'locale' => $request->locale,
            'webhook_id' => $request->webhook_id,
            'secret_key' => $request->secret_key,
            'client_id' => $request->client_id,
        ];
        $setting->json_value = $value;
        $setting->save();

        // Làm mới cache sau khi cập nhật
        Cache::forget('paypal');  // Xóa cache cũ
        Cache::put('paypal', $value, 86400);  // Lưu cache mới trong 1 ngày

        // Trả về dữ liệu cho view
        return redirect()->back()->with('success', 'Paypal config updated successfully!');

    }




}
