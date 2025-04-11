<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();  // Lấy tất cả các location từ cơ sở dữ liệu
        return view('locations.index', compact('locations'));  // Truyền vào view
    }

    public function showChart()
    {
        $locations = DB::table('locations')->get();

        $chartData = $locations->map(function ($loc) {
            $llm = json_decode($loc->data_llm, true);

            return [
                'name' => $loc->name,
                'phan_tram_tot' => floatval($llm['GPT']['phan_tram_tot'] ?? 0),
                'phan_tram_xau' => floatval($llm['GPT']['phan_tram_xau'] ?? 0),
            ];
        });

        return view('nguoidung', ['chartData' => $chartData]);
    }
}
