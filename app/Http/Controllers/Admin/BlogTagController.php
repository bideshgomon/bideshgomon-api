<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogTagController extends Controller
{
    public function index()
    {
        $tags = BlogTag::withCount('posts')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Blog/Tags/Index', [
            'tags' => $tags,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blog_tags,slug',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        BlogTag::create($validated);

        return back()->with('success', 'Tag created successfully.');
    }

    public function update(Request $request, BlogTag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:blog_tags,slug,' . $tag->id,
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tag->update($validated);

        return back()->with('success', 'Tag updated successfully.');
    }

    public function destroy(BlogTag $tag)
    {
        $tag->delete();

        return back()->with('success', 'Tag deleted successfully.');
    }
}
