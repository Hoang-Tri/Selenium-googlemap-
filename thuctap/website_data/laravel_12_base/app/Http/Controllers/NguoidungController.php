<?php
namespace App\Http\Controllers;

use App\Models\UserReview;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class NguoidungController extends Controller
{   
    public function showSoSanh()
    {   
        $user = Auth::user();
        $history = $user->history ? json_decode($user->history, true) : [];
        
        return view('sosanh_place', [
            'history'=>$history,
        ]);
    }

    private function getChartData($id)
    {
        // Truy vấn dữ liệu theo ID địa điểm
        $location = Location::findOrFail($id);

        //Điểm tin cậy
        $reviews = DB::table('users_review')
            ->where('location_id', $id)
            ->select(
                DB::raw('SUM(CASE WHEN star >= 5 THEN 1 ELSE 0 END) as good_count'),
                DB::raw('SUM(CASE WHEN star <= 1 THEN 1 ELSE 0 END) as bad_count')
            )
            ->first();
        
        $good = $reviews->good_count ?? 0;
        $bad = $reviews->bad_count ?? 1;
        $trustScore = ($good - $bad) > 0 ? 100 - (min($bad, 5) * 10) : 0;
        
        //Phần trăm tốt, xấu
        $llm = json_decode($location->data_llm, true);
        $phan_tram_tot = floatval($llm['GPT']['phan_tram_tot'] ?? 0);
        $phan_tram_xau = floatval($llm['GPT']['phan_tram_xau'] ?? 0);
        
        //trung bình sao
        $averageRating = DB::table('users_review')
            ->where('location_id', $id)
            ->avg('star');

        $averageRating = round($averageRating, 2);
        
        //lịch sử đánh giá
        $userReviewChartData = DB::table('users_review')
            ->where('location_id', $id)
            ->select('user_review as username', DB::raw('IFNULL(star, 0) as star'), DB::raw('DATE(creat_date) as date'))
            ->orderBy('id')
            ->orderBy('creat_date')
            ->get();
        
        //tính nps mức dộ hài lòng
        $npsStats = DB::table('users_review')
            ->where('location_id', $id)
            ->select(
                DB::raw('SUM(CASE WHEN star = 5 THEN 1 ELSE 0 END) as promoters'),
                DB::raw('SUM(CASE WHEN star IN (3,4) THEN 1 ELSE 0 END) as passives'),
                DB::raw('SUM(CASE WHEN star IN (1,2) THEN 1 ELSE 0 END) as detractors'),
                DB::raw('COUNT(*) as total_reviews')
            )
            ->first();

        $total = $npsStats->total_reviews > 0 ? $npsStats->total_reviews : 1;

        $npsScore = (($npsStats->promoters / $total) * 100) - 
                    (($npsStats->detractors / $total) * 100);
        // Phân loại NPS
        if ($npsScore >= 50) {
            $npsLabel = 'Tốt';
        } elseif ($npsScore >= 0) {
            $npsLabel = 'Trung bình';
        } else {
            $npsLabel = 'Kém';
        }

        // comment
        $comments = DB::table('users_review')
            ->where('location_id', $id)
            ->get();

        // Khởi tạo biến đếm bình luận tốt và xấu từ data_llm
        $goodCountFromLLM = 0;
        $badCountFromLLM = 0;

        // Duyệt qua từng bình luận và tính toán từ data_llm
        foreach ($comments as $comment) {
            // Xử lý phần trăm từ data_llm
            $llm = json_decode($comment->data_llm, true);
            $phan_cmt_tram_tot = floatval($llm['GPT']['phan_tram_tot'] ?? 0);
            $phan_cmt_tram_xau = floatval($llm['GPT']['phan_tram_xau'] ?? 0);

            if ($phan_cmt_tram_tot > 50) {
                $goodCountFromLLM++;
            } else {
                $badCountFromLLM++;
            }
        }

        // Tổng hợp bình luận tốt xấu từ data_llm
        $totalGoodCount = $goodCountFromLLM;
        $totalBadCount = $badCountFromLLM;
        
         // tỷ lệ tốt xấu của từng user
        $feedbacks = DB::table('users_review')
            ->where('location_id', $id)
            ->select('user_review', 'data_llm')
            ->get();

        $userLabels = [];
        $goodPercents = [];
        $badPercents = [];

        foreach ($feedbacks as $feedback) {
            $llm = json_decode($feedback->data_llm, true);
            $phan_tram_fb_tot = floatval($llm['GPT']['phan_tram_tot'] ?? 0);
            $phan_tram_fb_xau = floatval($llm['GPT']['phan_tram_xau'] ?? 0);

            $userLabels[] = $feedback->user_review ?? 'Ẩn danh';
            $goodPercents[] = $phan_tram_fb_tot;
            $badPercents[] = $phan_tram_fb_xau;
        }
        //nhất quán / không nhất quan
        $rawReviews = DB::table('users_review')
            ->where('location_id', $id)
            ->select('star', 'user_review', 'data_llm')
            ->get();

        $rawreviews = [];

        foreach ($rawReviews as $fb) {
            $data_llm = json_decode($fb->data_llm, true);
            $percent_good = floatval($data_llm["GPT"]["phan_tram_tot"]);
            $percent_bad = floatval($data_llm["GPT"]["phan_tram_xau"]);
            $adjusted_star = round(1 + 4 * ($percent_good / 100), 1);

            // Kiểm tra mâu thuẫn
            $mau_thuan = false;
            if (($fb->star >= 4 && $percent_bad > 50) || ($fb->star <= 2 && $percent_good > 50)) {
                $mau_thuan = true;
            }

            $rawreviews[] = [
                'original_star' => $fb->star,
                'review' => $fb->user_review,
                'percent_good' => $percent_good,
                'percent_bad' => $percent_bad,
                'adjusted_star' => $adjusted_star,
                'mau_thuan' => $mau_thuan
            ];
        }
        return [
            'trustScore' => $trustScore,
            'phan_tram_tot' => $phan_tram_tot,
            'phan_tram_xau' => $phan_tram_xau,
            'averageRating' => $averageRating,
            'npsScore' => $npsScore,
            'npsLabel' => $npsLabel,
            'userReviewChartData' => $userReviewChartData,
            'totalGoodCount' => $totalGoodCount,
            'totalBadCount' => $totalBadCount,
            'userLabels'=> $userLabels,
            'goodPercents'=> $goodPercents,
            'badPercents'=> $badPercents,
            'rawreviews'=>$rawreviews,
        ];
    }

    public function chart($id)
    {
        $data = $this->getChartData($id);
        return view('chart_partial', $data);
    }

    public function soSanhPlace($id1, $id2)
    {
        $data1 = $this->getChartData($id1); 
        $data2 = $this->getChartData($id2); 

        return view('sosanh_place', [
            'data1' => $data1,
            'data2' => $data2,
        ]);
    }
// cập nhật user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->username = $request->username;
        $user->fullname = $request->fullname;
        $user->email = $request->email;

        // Nếu có nhập mật khẩu mới thì mã hóa lại
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Cập nhật người dùng thành công!');
    }
// lịch sử người dùng
    public function saveHistory(Request $request)
    {
        $user = Auth::user();
        $newPlace = $request->input('place');

        if (!$newPlace) {
            return response()->json(['message' => 'Thiếu dữ liệu địa điểm'], 400);
        }

        $history = $user->history ?? [];

        if (is_string($history)) {
            $history = json_decode($history, true);
        }

        if (!is_array($history)) {
            $history = [];
        }

        // Không lưu trùng
        if (!in_array($newPlace, $history)) {
            $history[] = $newPlace;
        }

        $user->history = json_encode($history);
        $user->save();

        return response()->json(['message' => 'Lưu lịch sử thành công']);
    }
    
    public function showChart1($location_id = null )
    {
        if (!$location_id) {
            // Nếu không truyền vào → lấy từ session → nếu vẫn không có → mặc định là 1
            $location_id = session('current_location_id', 22);
        } else {
            // Nếu truyền location_id → cập nhật lại session
            session(['current_location_id' => $location_id]);
        }
    
        $user = Auth::user();
        $history = $user->history ? json_decode($user->history, true) : [];

        $locations = Location::all();
        $chartData = $locations->map(function ($loc) {
            $llm = json_decode($loc->data_llm, true);

            return [
                'name' => $loc->name,
                'phan_tram_tot' => floatval($llm['GPT']['phan_tram_tot'] ?? 0),
                'phan_tram_xau' => floatval($llm['GPT']['phan_tram_xau'] ?? 0),
            ];
        });
        $recentPlaces = $locations->slice(-5)->pluck('name');

        return view('nguoidung', [
            'chartData' => $chartData,
            'recentPlaces'=> $recentPlaces,
            'history'=>$history
        ]);
    }
    public function clearHistory()
    {
        $user = Auth::user();
        $user->history = json_encode([]);
        $user->save();

        return redirect()->route('nguoidung.dashboard')->with('success', 'Đã xóa lịch sử thành công!');
    }
}
