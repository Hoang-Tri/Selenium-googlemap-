<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserGoogleMapsController extends Controller
{
    public function index()
    {
        return view('usergooglemaps.index');  // Đảm bảo view được trả về chính xác
    }
}
