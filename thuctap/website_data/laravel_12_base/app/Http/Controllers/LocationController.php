<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();  // Lấy tất cả các location từ cơ sở dữ liệu
        return view('locations.index', compact('locations'));  // Truyền vào view
    }
}
