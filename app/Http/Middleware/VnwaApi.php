<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Cache;
use Closure;
use Config;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VnwaApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lấy thông tin ngôn ngữ từ segment URL
        $lang = $request->route('lang', 'en');

        $supportedLangs = [
            'en' => 'en_db',
            'es' => 'es_db',
        ];

        if (!isset($supportedLangs[$lang])) {
            logger()->warning("Unsupported language: $lang");
            $lang = 'en'; // Đặt mặc định thay vì trả lỗi
        }

        app()->setLocale($lang);
        Config::set('database.default', $supportedLangs[$lang]);

        // Cache danh sách frontend URLs
        // $allowedUrls = Cache::remember('frontend_urls', 86400, function () {
        //     $setting = Setting::where('type', 'frontend_urls')->first();
        //     return $setting->json_value ?? [];
        // });

        // // Kiểm tra CORS
        // $origin = $request->headers->get('Origin') ?? $request->headers->get('Referer');
        // if (!$origin || !in_array($origin, $allowedUrls)) {
        //     logger()->warning("Unauthorized origin: $origin");
        //     return response()->json(['error' => 'Unauthorized request', 'status_code' => 403], 403);
        // }

        app()->singleton('currentLang', fn() => $lang);
        $request->route()->forgetParameter('lang');
        return $next($request);
    }

}
