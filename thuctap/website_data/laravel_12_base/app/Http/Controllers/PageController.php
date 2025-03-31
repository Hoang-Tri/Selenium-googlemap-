<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        return view('pages.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_page' => 'required|string|max:255',
            'author_page' => 'required|string|max:255',
            'content_page' => 'required|string',
            'status_page' => 'required|in:Draft,Published,Pending',
        ]);

        $page = new Page();
        $page->title_page = $request->title_page;
        $page->slug_page = convertToSlug($request->title_page);
        $page->author_page = $request->author_page;
        $page->content_page = $request->content_page;
        $page->status_page = $request->status_page;

        if ($page->save()) {
            return redirect()->route('pages.index')->with('success', 'Page created successfully!');
        } else {
            return back()->withErrors('Failed to add page, please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title_page' => 'required|string|max:255',
            'author_page' => 'required|string|max:255',
            'content_page' => 'required|string',
            'status_page' => 'required|in:Draft,Published,Pending',
        ]);

        $page = Page::findOrFail($id);
        $page->title_page = $request->title_page;
        $page->slug_page = convertToSlug($request->title_page);
        $page->author_page = $request->author_page;
        $page->content_page = $request->content_page;
        $page->status_page = $request->status_page;

        if ($page->save()) {
            return redirect()->route('pages.index')->with('success', 'Page updated successfully!');
        } else {
            return back()->withErrors('Failed to update page, please try again.');
        }
    }

    public function destroy($id)
    {
        $page = Page::find($id);
    
        $page->delete();
        return redirect()->route('pages.index')->with('success', 'Page deleted successfully!');
    }
}
