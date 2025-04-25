<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Kiểm tra nếu người dùng chưa đăng nhập
        if (Auth::guard($guard)->guest()) {
            // Chuyển hướng người dùng đến trang login nếu chưa đăng nhập
            return redirect()->route('login');
        }

        return $next($request);
    }

    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            return route('login'); // Đảm bảo route 'login' đã tồn tại
        }
        return null;
    }
}

