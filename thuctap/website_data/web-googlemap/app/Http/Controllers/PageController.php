<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    private $pages;

    public function __construct()
    {
        // Dữ liệu giả lập, thay cho database
        $this->pages = collect([
            (object) ['id' => 1, 'title' => 'About Us', 'slug' => 'about-us', 'created_at' => '2025-03-18'],
            (object) ['id' => 2, 'title' => 'Contact', 'slug' => 'contact', 'created_at' => '2025-03-17'],
        ]);
    }

    public function index()
    {
        return view('pages.index', ['pages' => $this->pages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|max:255|unique:pages,slug',
        ]);

        // Giả lập thêm dữ liệu vào danh sách
        $newPage = (object) [
            'id' => $this->pages->max('id') + 1,
            'title' => $request->title,
            'slug' => $request->slug,
            'created_at' => now()->format('Y-m-d'),
        ];

        $this->pages->push($newPage);

        return redirect()->route('pages.index')->with('success', 'Page added successfully.');
    }

    public function edit($id)
    {
        $page = $this->pages->firstWhere('id', $id);

        if (!$page) {
            return redirect()->route('pages.index')->with('error', 'Page not found.');
        }

        return view('pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'slug' => 'required|max:255',
        ]);

        $page = $this->pages->firstWhere('id', $id);

        if ($page) {
            $page->title = $request->title;
            $page->slug = $request->slug;
        }

        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }

    public function destroy($id)
    {
        $this->pages = $this->pages->reject(function ($page) use ($id) {
            return $page->id == $id;
        });

        return response()->json(['success' => true]);
    }
}
