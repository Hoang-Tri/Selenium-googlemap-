<?php
namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\UserReview;
use Illuminate\Http\Request;

class UserReviewController extends Controller
{
    public function index()
    {
        $usersreview = UserReview::with('location')->get(); // Lấy tất cả các location từ cơ sở dữ liệu
        return view('usersreview.index', compact('usersreview'));  // Truyền vào view
    }
    
}
