<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;;

class PlaceController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');  // Lấy từ khóa tìm kiếm

        // Kiểm tra nếu từ khóa không rỗng
        if (empty($query)) {
            return response()->json([]);  // Trả về mảng rỗng nếu không có từ khóa
        }

        // Tìm các địa điểm có chứa từ khóa
        $places = Location::where('name', 'LIKE', '%' . $query . '%')
                  ->limit(10)
                  ->get();

        return response()->json($places);  // Trả về kết quả tìm kiếm dưới dạng JSON
    }
}

