<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Category;
use App\Http\Controllers\Controller; 

class CategoryController extends Controller
{
    public function __construct() {
        session()->start(); // Nếu thực sự cần session
    }

    // public function index(): View {
    //     $category = Category::where("status", 1)->get();
    //     return view('admin.pages.category.index', compact('category'));
    // }

    public function index(): View {
        $categories = Category::where("status", 1)->get();
        return view('admin.pages.category.index', compact('categories'));
    }

    public function add(): View {

        return view('admin.pages.category.add');
    }

    public function add_post(Request $request) {
        $request->validate([
            'title_cate' => 'required|string|max:255|unique:category,title_cate',
        ]);

        $category = new Category();
        $category->title_cate = $request->title_cate;
        $category->link_cate = convertToSlug($request->title_cate); // Gọi hàm Helper
        $category->status = 1;

        if ($category->save()) {
            return redirect()->route('category.index')->with('noti', 'Category added successfully!');
        } else {
            return back()->withErrors('Failed to add category, please try again.');
        }
    }
}