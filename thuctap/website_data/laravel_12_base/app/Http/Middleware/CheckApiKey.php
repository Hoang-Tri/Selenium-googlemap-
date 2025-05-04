<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Chỉ kiểm tra nếu người dùng là admin (level = 1)
        if (Auth::check() && Auth::user()->level == 1) {
            $apiKey = DB::table('settings')->where('key_name', 'ai_api_key')->value('value');

            if (!$apiKey || trim($apiKey) === '') {
                // Nếu không có API key, chuyển hướng đến trang settings
                return redirect('http://lht_2110421:8080/settings?redirect=' . urlencode($request->path()))
                    ->with('warning', 'Vui lòng nhập API Key để tiếp tục.');
            }
        }

        return $next($request);
    }
}
