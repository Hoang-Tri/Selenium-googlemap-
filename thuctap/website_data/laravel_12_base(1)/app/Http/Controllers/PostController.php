<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;

class PostController extends Controller
{
    // Hiển thị danh sách bài viết
    public function index()
    {
        $posts = Post::with('category')->get();
        $categories = Category::all(); // Lấy tất cả categories để dùng trong form

        return view('posts.index', compact('posts', 'categories'));
    }

    // Thêm bài viết mới
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'title_post' => 'required|string|max:255',
    //         'id_cate' => 'required|exists:categories,id_cate',
    //         'author' => 'required|string|max:255',
    //         'content_post' => 'required|string',
    //         'status_post' => 'required|in:Draft,Published,Pending',
    //     ]);

    //     Post::create([
    //         'title_post' => $request->title_post,
    //         'id_cate' => $request->id_cate,
    //         'author' => $request->author,
    //         'content_post' => $request->content_post,
    //         'status_post' => $request->status_post, 
    //         'created_at_post' => now(),
    //     ]);

    //     return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'title_post' => 'required|string|max:255',
            'id_cate' => 'required|exists:category,id_cate',
            'author' => 'required|string|max:255',
            'content_post' => 'required|string',
            'status_post' => 'required|in:Draft,Published,Pending',
        ]);

        $post = new Post();
        $post->title_post = $request->title_post;
        $post->id_cate = $request->id_cate; 
        $post->author = $request->author;
        $post->content_post = $request->content_post;
        $post->status_post = $request->status_post;

        if ($post->save()) {
            return redirect()->route('posts.index')->with('success', 'Post created successfully!');
        } else {
            return back()->withErrors('Failed to add post, please try again.');
        }
    }

    // Cập nhật bài viết
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'title_post' => 'required|string|max:255',
    //         'id_cate' => 'required|exists:categories,id_cate',
    //         'author' => 'required|string|max:255',
    //         'content_post' => 'required|string',
    //         'status_post' => 'required|in:Draft,Published,Pending', // 🔥 Thêm validation cho status_post
    //     ]);

    //     $post = Post::findOrFail($id);
    //     $post->update([
    //         'title_post' => $request->title_post,
    //         'id_cate' => $request->id_cate,
    //         'author' => $request->author,
    //         'content_post' => $request->content_post,
    //         'status_post' => $request->status_post, // 🔥 Thêm status_post
    //     ]);

    //     return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title_post' => 'required|string|max:255',
            'id_cate' => 'required|exists:category,id_cate',
            'author' => 'required|string|max:255',
            'content_post' => 'required|string',
            'status_post' => 'required|in:Draft,Published,Pending',
        ]);

        $post = Post::findOrFail($id);
        $post->title_post = $request->title_post;
        $post->id_cate = $request->id_cate;
        $post->author = $request->author;
        $post->content_post = $request->content_post;
        $post->status_post = $request->status_post;

        if ($post->save()) {
            return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
        } else {
            return back()->withErrors('Failed to update post, please try again.');
        }
    }

    // Xóa bài viết
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }
}
