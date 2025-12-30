<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem user đã đăng nhập và có phải là admin không
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request); // Cho phép đi tiếp
        }

        // Nếu không phải admin, trả về lỗi 403 theo format nhất quán [cite: 362-367]
        return response()->json([
            'success' => false,
            'message' => 'Access denied. Admin role required.'
        ], 403);
    }
}