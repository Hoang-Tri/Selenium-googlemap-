<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.pages.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'title_cate' => 'required|string|max:255|unique:category,title_cate,'.$id.',id_cate',
        ]);

        $category = Category::findOrFail($id); // Tìm category cần cập nhật
        $category->title_cate = $request->title_cate;
        $category->link_cate = convertToSlug($request->title_cate);
        $category->status = $request->status ? 1 : 0; // Nếu checkbox không được chọn, nó sẽ là NULL

        if ($category->save()) {
            return redirect()->route('category.index')->with('noti', 'Category updated successfully!');
        } else {
            return back()->withErrors('Failed to update category, please try again.');
        }
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->delete()) {
            return redirect()->route('category.index')->with('noti', 'Category deleted successfully!');
        } else {
            return back()->withErrors('Failed to delete category, please try again.');
        }
    }
}
