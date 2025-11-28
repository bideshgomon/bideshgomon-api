<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Directory;
use App\Models\DirectoryCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DirectoryController extends Controller
{
    /**
     * Display a listing of directories.
     */
    public function index(Request $request)
    {
        $query = Directory::with(['category', 'country'])
            ->where('is_published', true)
            ->where('is_verified', true);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('meta_description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by country
        if ($request->filled('country')) {
            $query->where('country_id', $request->country);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'like', "%{$request->city}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if ($sortBy === 'views') {
            $query->orderBy('view_count', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $directories = $query->paginate(12)->withQueryString();

        // Get categories for filter
        $categories = DirectoryCategory::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon', 'color']);

        return Inertia::render('User/Directory/Index', [
            'directories' => $directories,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'country', 'city', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Display the specified directory.
     */
    public function show(string $slug)
    {
        $directory = Directory::with(['category', 'country'])
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('is_verified', true)
            ->firstOrFail();

        // Increment view count
        $directory->increment('view_count');

        // Parse opening hours if JSON
        $openingHours = $directory->opening_hours;
        if (is_string($openingHours)) {
            $openingHours = json_decode($openingHours, true);
        }

        // Get related directories (same category, different directory)
        $relatedDirectories = Directory::with(['category', 'country'])
            ->where('directory_category_id', $directory->directory_category_id)
            ->where('id', '!=', $directory->id)
            ->where('is_published', true)
            ->where('is_verified', true)
            ->limit(4)
            ->get();

        return Inertia::render('User/Directory/Show', [
            'directory' => array_merge($directory->toArray(), [
                'opening_hours' => $openingHours,
            ]),
            'relatedDirectories' => $relatedDirectories,
            'seo' => [
                'title' => $directory->meta_title ?? $directory->name,
                'description' => $directory->meta_description ?? substr($directory->description, 0, 160),
                'keywords' => $directory->meta_keywords,
            ],
        ]);
    }

    /**
     * Display directories by category.
     */
    public function byCategory(string $slug)
    {
        $category = DirectoryCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $directories = Directory::with(['category', 'country'])
            ->where('directory_category_id', $category->id)
            ->where('is_published', true)
            ->where('is_verified', true)
            ->orderBy('name')
            ->paginate(12);

        return Inertia::render('User/Directory/Category', [
            'category' => $category,
            'directories' => $directories,
            'seo' => [
                'title' => $category->meta_title ?? "{$category->name} Directory",
                'description' => $category->meta_description ?? "Browse all {$category->name} listings",
                'keywords' => $category->meta_keywords,
            ],
        ]);
    }

    /**
     * Search directories via AJAX.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $results = Directory::with(['category', 'country'])
            ->where('is_published', true)
            ->where('is_verified', true)
            ->where(function ($q) use ($request) {
                $search = $request->query;
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'slug', 'address', 'city', 'phone', 'directory_category_id']);

        return response()->json($results);
    }
}
