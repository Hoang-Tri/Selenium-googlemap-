<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class LocationStatusController extends Controller
{
    // 1. Kiểm tra hoặc bắt đầu crawl
    public function checkOrStart(Request $request)
    {
        $place = trim($request->input('place'));
        
        // Kiểm tra đầu vào
        if (empty($place)) {
            return response()->json(['message' => 'Tên địa điểm không hợp lệ'], 400);
        }
    
        $existing = DB::table('locations')->where('name', $place)->first();
    
        if ($existing) {
            return response()->json(['status' => $existing->status]);
        }
    
        // Thêm mới và bắt đầu crawl
        DB::table('locations')->insert([
            'name' => $place,
            'status' => 1,
            'scraped_date' => null,
        ]);
    
        // Gọi API backend để crawl
        try {
            Http::withHeaders([
                'API-Key' => env('API_KEY'),  // Sử dụng env cho API key
                'accept' => 'application/json',
            ])->asForm()->post('http://localhost:60074/base/start/', [
                'keywords' => $place
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Không thể kết nối đến hệ thống crawler'], 500);
        }
    
        return response()->json(['status' => 1]); // Đang crawl
    }

    // 2. Cập nhật khi crawler xong
    public function updateStatus(Request $request)
    {
        $place = $request->input('place');
        $status = $request->input('status', 2); // mặc định 2 (xong)

        DB::table('locations')
            ->where('name', $place)
            ->update([
                'status' => $status,
                'scraped_date' => now()
            ]);

        return response()->json(['message' => 'Cập nhật thành công']);
    }

    // 3. Lấy trạng thái hiện tại
    public function getStatus(Request $request)
    {
        $place = $request->input('place');
        $row = DB::table('locations')->where('name', $place)->first();

        if (!$row) {
            return response()->json(['message' => 'Không tìm thấy địa điểm'], 404);
        }

        return response()->json(['status' => $row->status]);
    }
}
