<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    public function __construct()
    {
        @session_start();
    }

    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key_name');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $path = $file->store('settings', 'public');
                $value = 'storage/' . $path;
            }

            Setting::setValue($key, $value);
        }

        try {
            Http::post('http://localhost:60074/base/refresh-settings');
            return redirect()->back()->with('success', 'Cập nhật cấu hình và đồng bộ thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('success', 'Cập nhật cấu hình thành công nhưng chưa đồng bộ được!');
        }
    }
}
