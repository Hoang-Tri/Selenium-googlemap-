<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Model\NguoiDung;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->status != 1) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['status' => 'Tài khoản của bạn đã bị khóa.']);
            }

            if ($user->level == 1) {
                // Kiểm tra có ai_api_key chưa
                $apiKey = DB::table('settings')->where('key_name', 'ai_api_key')->value('value');

                if (!$apiKey || trim($apiKey) === '') {
                    // Nếu chưa có API Key, chuyển đến trang cấu hình kèm redirect
                    return redirect('http://lht_2110421:8080/settings?redirect=dashboard')
                        ->with('warning', 'Vui lòng nhập API Key để tiếp tục.');
                }

                return redirect('http://lht_2110421:8080/dashboard');
            } else {
                return redirect()->route('nguoidung.dashboard');
            }
        }

        return redirect()->route('login')->with('error', 'Sai email hoặc mật khẩu.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    public function count()
    {
        $userCount = User::count();
        return view('users.count', compact('userCount'));
    }
}

