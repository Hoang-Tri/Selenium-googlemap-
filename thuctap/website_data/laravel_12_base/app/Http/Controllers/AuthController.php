<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                return redirect()->route('admin.dashboard');
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

