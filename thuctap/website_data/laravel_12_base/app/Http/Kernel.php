<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    
    // 🧩 Đây là chỗ Teaa cần thêm `auth`
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'is_admin' => \App\Http\Middleware\IsAdmin::class,
        'check.apikey' => \App\Http\Middleware\CheckApiKey::class,
        // Các middleware khác nếu có
    ];
}
