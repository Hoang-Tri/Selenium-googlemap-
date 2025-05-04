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
        $phan_tram_tot_raw = floatval($llm['GPT']['phan_tram_tot'] ?? 0);
        $phan_tram_xau_raw = floatval($llm['GPT']['phan_tram_xau'] ?? 0);
        
        //trung bình sao
        $averageRating = DB::table('users_review')
            ->where('location_id', $id)
            ->avg('star');

        $averageRating = round($averageRating, 2);

        // Điểm cảm xúc trung bình (Sentiment Score)
        $Scorecomments = DB::table('users_review')
            ->where('location_id', $id)
            ->select('data_llm')
            ->get();

            $totalSentiment = 0;
            $totalComments = 0;

            foreach ($Scorecomments as $Scorecomment) {
            $llmScore  = json_decode($Scorecomment->data_llm, true);
            $phan_tram_tot = floatval($llmScore ['GPT']['phan_tram_tot'] ?? null);

            if (!is_null($phan_tram_tot)) {
                $sentiment = ($phan_tram_tot - 50) / 50; // Normalize [-1, 1]
                $totalSentiment += $sentiment;
                $totalComments++;
            }
        }

        $sentimentScore = $totalComments > 0 ? round($totalSentiment / $totalComments, 2) : null;
        
        //lịch sử đánh giá
        $userReviewChartData = DB::table('users_review')
            ->where('location_id', $id)
            ->select(
                'user_review as username',
                DB::raw('IFNULL(star, 0) as star'),
                DB::raw('DATE(creat_date) as date'),
                DB::raw('YEAR(creat_date) as year') // Lấy năm từ ngày
            )
            ->orderBy('id')
            ->orderBy('creat_date')
            ->get();

        // Tính trung bình theo ngày
        $averageByDate = $userReviewChartData->groupBy('date')->map(function ($reviews) {
            return $reviews->avg('star'); // Tính trung bình sao cho mỗi ngày
        });

        // Tính trung bình theo năm
        $averageByYear = $userReviewChartData->groupBy('year')->map(function ($reviews) {
            return $reviews->avg('star'); // Tính trung bình sao cho mỗi năm
        });
        $totalReviews = $userReviewChartData->count();

        // Phân loại đánh giá
        $ratingCategories = [
            'Đặc sắc' => $userReviewChartData->filter(fn($r) => $r->star == 5)->count(),
            'Trung tính' => $userReviewChartData->filter(fn($r) => in_array($r->star, [3, 4]))->count(),
            'Tệ' => $userReviewChartData->filter(fn($r) => in_array($r->star, [1, 2]))->count(),
        ];

        // Tính tỷ lệ phần trăm
        $ratingPercentages = collect($ratingCategories)->map(function ($count) use ($totalReviews) {
            return $totalReviews > 0 ? round(($count / $totalReviews) * 100, 2) : 0;
        });
        
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
            $llmcmt = json_decode($comment->data_llm, true);
            $phan_cmt_tram_tot = floatval($llmcmt['GPT']['phan_tram_tot'] ?? 0);
            $phan_cmt_tram_xau = floatval($llmcmt['GPT']['phan_tram_xau'] ?? 0);

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
            $llmfb = json_decode($feedback->data_llm, true);
            $phan_tram_fb_tot = floatval($llmfb['GPT']['phan_tram_tot'] ?? 0);
            $phan_tram_fb_xau = floatval($llmfb['GPT']['phan_tram_xau'] ?? 0);

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
        $conflictCount = 0; // Biến đếm số lượng mâu thuẫn
        $noConflictCount = 0; // Biến đếm số lượng không mâu thuẫn
 
        foreach ($rawReviews as $fb) {
            $data_llm = json_decode($fb->data_llm, true);
            $percent_good = floatval($data_llm["GPT"]["phan_tram_tot"]);
            $percent_bad = floatval($data_llm["GPT"]["phan_tram_xau"]);
            $adjusted_star = round(1 + 4 * ($percent_good / 100), 1);

            // Kiểm tra mâu thuẫn
            $mau_thuan = false;
            if (($fb->star >= 4 && $percent_bad > 50) || ($fb->star <= 2 && $percent_good > 50)) {
                $mau_thuan = true;
                $conflictCount++; // Tăng số lượng mâu thuẫn
            } else {
                $noConflictCount++; // Tăng số lượng không mâu thuẫn
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
        // danh sach tư tot-xau
        // $location = Location::findOrFail($id);
        $listWords = [
            'tot' => [],
            'xau' => [],
        ];

        $llmraw = json_decode($location->data_llm, true);

        if (!empty($llmraw['danh_sach_tu_tot'])) {
            // Chuyển từ chuỗi dạng "['a', 'b']" thành mảng PHP
            $tot_raw = str_replace(["'", "[", "]"], '', $llmraw['danh_sach_tu_tot']);
            $listWords['tot'] = array_map('trim', explode(',', $tot_raw));
        }

        if (!empty($llmraw['danh_sach_tu_xau'])) {
            $xau_raw = str_replace(["'", "[", "]"], '', $llmraw['danh_sach_tu_xau']);
            $listWords['xau'] = array_map('trim', explode(',', $xau_raw));
        }
        $listWords['tot'] = array_count_values($listWords['tot']);
        $listWords['xau'] = array_count_values($listWords['xau']);

        arsort($listWords['tot']);
        arsort($listWords['xau']);
        $listWords_chart['tot'] = array_slice($listWords['tot'], 0, 5, true);
        $listWords_chart['xau'] = array_slice($listWords['xau'], 0, 5, true);

        // top review
        $topreviews = DB::table('users_review')
            ->where('location_id', $id)
            ->select('user_review', 'data_llm')
            ->get();

        $reviewCounts = [];

        foreach ($topreviews as $topreview) {
            $user = $topreview->user_review;
            $data = json_decode($topreview->data_llm, true);

            // Chuyển chuỗi list thành mảng PHP
            $tu_tot = json_decode(str_replace("'", '"', $data['danh_sach_tu_tot']), true) ?? [];
            $tu_xau = json_decode(str_replace("'", '"', $data['danh_sach_tu_xau']), true) ?? [];
            
            $count = count($tu_tot) + count($tu_xau);

            if (!isset($reviewCounts[$user])) {
                $reviewCounts[$user] = 0;
            }

            $reviewCounts[$user] += $count;
        }

        // Sắp xếp và lấy top 5
        arsort($reviewCounts);
        $top5 = array_slice($reviewCounts, 0, 5, true);

        $thongKeThang = DB::table('users_review')
        ->where('location_id', $id) // chỉ lấy theo địa điểm
        ->selectRaw('
            YEAR(creat_date) as nam,
            MONTH(creat_date) as thang,
            SUM(JSON_LENGTH(JSON_EXTRACT(data_llm, "$.danh_sach_tu_tot"))) AS tong_tu_tot,
            SUM(JSON_LENGTH(JSON_EXTRACT(data_llm, "$.danh_sach_tu_xau"))) AS tong_tu_xau
        ')
        ->groupBy(DB::raw('YEAR(creat_date), MONTH(creat_date)'))
        ->orderBy('nam', 'desc')
        ->orderBy('thang', 'desc')
        ->get();

        // Tổng số từ tốt và xấu cho mỗi năm
        $thongKeNam = DB::table('users_review')
            ->where('location_id', $id)
            ->selectRaw('
                YEAR(creat_date) as nam,
                SUM(JSON_LENGTH(JSON_EXTRACT(data_llm, "$.danh_sach_tu_tot"))) AS tong_tu_tot,
                SUM(JSON_LENGTH(JSON_EXTRACT(data_llm, "$.danh_sach_tu_xau"))) AS tong_tu_xau
            ')
            ->groupBy(DB::raw('YEAR(creat_date)'))
            ->orderBy('nam', 'desc')
            ->get();

        $thongKeThang = $thongKeThang->sortBy(function ($item) {
            return $item->nam * 100 + $item->thang;
        })->values();
        
        $thongKeNam = $thongKeNam->sortBy('nam')->values();
        return [
            'trustScore' => $trustScore,
            'phan_tram_tot' => $phan_tram_tot_raw,
            'phan_tram_xau' => $phan_tram_xau_raw,
            'averageRating' => $averageRating,
            'npsScore' => $npsScore,
            'npsLabel' => $npsLabel,
            'userReviewChartData' => $userReviewChartData,
            'ratingPercentages' => $ratingPercentages,
            'totalGoodCount' => $totalGoodCount,
            'totalBadCount' => $totalBadCount,
            'userLabels'=> $userLabels,
            'goodPercents'=> $goodPercents,
            'badPercents'=> $badPercents,
            'rawreviews'=>$rawreviews,
            'conflictCount'=>$conflictCount,
            'noConflictCount'=>$noConflictCount ,
            'sentimentScore' => $sentimentScore,
            'listWords'=> $listWords,
            'listWords_chart'=>$listWords_chart,
            'averageByDate' => $averageByDate,
            'averageByYear'=> $averageByYear,
            'labels' => array_keys($top5),
            'counts' => array_values($top5),
            'thongKeThang' => $thongKeThang,
            'thongKeNam' => $thongKeNam,
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

    //sosanh
    public function chartSoSanh($id)
    {
        $data = $this->getChartData($id);
        return view('chart_sosanh_partial', $data); // View riêng cho so sánh
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
