<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\NewsLetterMail;
use App\Models\CustomerEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Xác thực email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', // Kiểm tra email đã tồn tại trong bảng customer_emails
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid email or already subscribed.'], 400);
        }

        // Kiểm tra xem có bị throttle không
        if ($this->isRateLimited($request)) {
            return response()->json(['message' => 'You have exceeded the subscription limit. Please try again later.'], 429);
        }

        if (!CustomerEmail::where('email', $request->email)->exists()) {
            // Nếu chưa có, lưu email vào bảng customer_emails
            CustomerEmail::create(['email' => $request->email]);
        }
        $origin = $request->headers->get('Origin');

        Mail::to($request->email)->queue(new NewsLetterMail($origin));

        return response()->json(['message' => 'Thank you for subscribing!']);
    }

    protected function isRateLimited(Request $request)
    {
        $cacheKey = 'email_subscribe_' . md5($request->email);
        $requests = Cache::get($cacheKey, 0);

        // Nếu số lần đã gửi là >= 5, trả về true
        if ($requests >= 5) {
            return true;
        }

        // Tăng số lần đã gửi và lưu vào cache
        Cache::put($cacheKey, $requests + 1, 600); // Thời gian hết hạn 10 phút

        return false;
    }
}
