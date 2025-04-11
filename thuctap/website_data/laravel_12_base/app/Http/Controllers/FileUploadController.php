<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\File; // Model để lưu thông tin file vào DB
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Response;

class FileUploadController extends Controller
{
    // Hiển thị trang upload file
    public function index()
    {
        // Lấy danh sách location_id có trong bảng
        $locations = DB::table('locations')
        ->join('users_review', 'locations.id', '=', 'users_review.location_id')
        ->select('locations.id as location_id', 'locations.name')
        ->groupBy('locations.id', 'locations.name')
        ->get();

        return view('upload.index', compact('locations'));
    }

    public function download($location_id)
    {
        // Lấy thông tin địa điểm theo location_id
        $location = DB::table('locations')->where('id', $location_id)->first();

        if (!$location) {
            abort(404, "Không tìm thấy địa điểm.");
        }

        // Lấy dữ liệu users_review liên quan
        $reviews = DB::table('users_review')
            ->select('id', 'location_id', 'user_review', 'data_llm')
            ->where('location_id', $location_id)
            ->get();

        if ($reviews->isEmpty()) {
            abort(404, "Không có dữ liệu đánh giá.");
        }

        // Chuẩn bị nội dung CSV
        $csvLines = [];
        $csvLines[] = "id,location_id,user_review,data_llm"; // Tiêu đề

        foreach ($reviews as $row) {
            $csvLines[] = implode(",", [
                $row->id,
                $row->location_id,
                '"' . str_replace('"', '""', $row->user_review) . '"',
                '"' . str_replace('"', '""', $row->data_llm) . '"'
            ]);
        }

        $csvContent = implode("\n", $csvLines);

        // Đặt tên file theo tên địa điểm
        $slugName = strtolower(str_replace(' ', '_', $location->name));
        $fileName = "reviews_{$slugName}.csv";

        // Trả về file để tải về
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\""
        ]);
    }

}

