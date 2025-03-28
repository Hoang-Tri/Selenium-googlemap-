<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    // public function index()
    // {
    //     return view('posts.index');
    // }
    public function index()
    {
        // Tạo danh sách bài viết giả lập
        $posts = collect([
            (object) ['id' => 1, 'title' => 'First Blog Post', 'author' => 'John Doe', 'created_at' => '2025-03-20'],
            (object) ['id' => 2, 'title' => 'Second Post', 'author' => 'Jane Smith', 'created_at' => '2025-03-19'],
        ]);
    
        return view('posts.index', compact('posts'));
    }

    // Hiển thị form tạo bài viết mới
    public function create()
    {
        return view('posts.create');
    }

    // Xử lý lưu bài viết mới
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:100',
            'content' => 'required',
            'created_at' => 'required|date',
        ]);

        $post = Post::create($request->all());

        return response()->json(['success' => true, 'post' => $post]);
    }


    // Hiển thị form chỉnh sửa bài viết
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    // Xử lý cập nhật bài viết
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:100',
            'content' => 'required',
            'created_at' => 'required|date',
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    // Xóa bài viết
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
    
        return response()->json(['success' => true]);
    }    
}
