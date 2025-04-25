<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\History;

class HistoryController extends Controller
{
    public function addReview(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (auth()->check()) {
            $userId = auth()->user()->id;
            $placeId = $request->place_id; // ID địa điểm mà người dùng đánh giá
            $reviewText = $request->review_text; // Nội dung đánh giá

            // Lưu lịch sử vào bảng history
            History::create([
                'user_id' => $userId,
                'place_id' => $placeId,
                'review_text' => $reviewText
            ]);

            return redirect()->route('nguoidung.dashboard')->with('success', 'Đánh giá đã được lưu!');
        }

        return redirect()->route('login'); // Chuyển hướng nếu người dùng chưa đăng nhập
    }

}
