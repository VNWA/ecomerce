<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            // Nếu chưa đăng nhập, chuyển hướng tới trang đăng nhập hoặc trả về lỗi 401
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Nếu đã đăng nhập, tiếp tục yêu cầu
        return $next($request);
    }
}
