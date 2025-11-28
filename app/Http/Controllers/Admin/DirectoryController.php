<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Directory::with(['category', 'country']);

        if ($request->search) {
            $query->search($request->search);
        }

        if ($request->category) {
            $query->byCategory($request->category);
        }

        if ($request->country) {
            $query->byCountry($request->country);
        }

        if ($request->status === 'published') {
            $query->published();
        } elseif ($request->status === 'draft') {
            $query->where('is_published', false);
        }

        if ($request->verified === 'yes') {
            $query->verified();
        } elseif ($request->verified === 'no') {
            $query->where('is_verified', false);
        }

        if ($request->featured === 'yes') {
            $query->featured();
        }

        $directories = $query->latest()->paginate(20);

        return Inertia::render('Admin/Directories/Index', [
            'directories' => $directories,
            'categories' => DirectoryCategory::active()->ordered()->get(['id', 'name']),
            'countries' => Country::all(['id', 'name']),
            'filters' => $request->only(['search', 'category', 'country', 'status', 'verified', 'featured']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Directories/Create', [
            'categories' => DirectoryCategory::active()->ordered()->get(['id', 'name', 'name_bn']),
            'countries' => Country::all(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'directory_category_id' => 'required|exists:directory_categories,id',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:directories,slug',
            'description' => 'required|string',
            'description_bn' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:500',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'operating_hours' => 'nullable|array',
            'services' => 'nullable|array',
            'social_media' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('directories/logos', 'public');
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('directories/images', 'public');
        }

        Directory::create($validated);

        return redirect()->route('admin.directories.index')
            ->with('success', 'Directory created successfully');
    }

    public function show(Directory $directory)
    {
        $directory->load(['category', 'country']);
        
        return Inertia::render('Admin/Directories/Show', [
            'directory' => $directory,
        ]);
    }

    public function edit(Directory $directory)
    {
        return Inertia::render('Admin/Directories/Edit', [
            'directory' => $directory->load(['category', 'country']),
            'categories' => DirectoryCategory::active()->ordered()->get(['id', 'name', 'name_bn']),
            'countries' => Country::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, Directory $directory)
    {
        $validated = $request->validate([
            'directory_category_id' => 'required|exists:directory_categories,id',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:directories,slug,' . $directory->id,
            'description' => 'required|string',
            'description_bn' => 'nullable|string',
            'country_id' => 'nullable|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:500',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'operating_hours' => 'nullable|array',
            'services' => 'nullable|array',
            'social_media' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($directory->logo && Storage::disk('public')->exists($directory->logo)) {
                Storage::disk('public')->delete($directory->logo);
            }
            $validated['logo'] = $request->file('logo')->store('directories/logos', 'public');
        }

        if ($request->hasFile('image')) {
            if ($directory->image && Storage::disk('public')->exists($directory->image)) {
                Storage::disk('public')->delete($directory->image);
            }
            $validated['image'] = $request->file('image')->store('directories/images', 'public');
        }

        $directory->update($validated);

        return redirect()->route('admin.directories.index')
            ->with('success', 'Directory updated successfully');
    }

    public function destroy(Directory $directory)
    {
        $directory->delete();

        return redirect()->route('admin.directories.index')
            ->with('success', 'Directory deleted successfully');
    }

    public function toggleVerified(Directory $directory)
    {
        $directory->update(['is_verified' => !$directory->is_verified]);

        return back()->with('success', 'Directory verification status updated successfully');
    }

    public function toggleFeatured(Directory $directory)
    {
        $directory->update(['is_featured' => !$directory->is_featured]);

        return back()->with('success', 'Directory featured status updated successfully');
    }

    public function togglePublished(Directory $directory)
    {
        $directory->update(['is_published' => !$directory->is_published]);

        return back()->with('success', 'Directory published status updated successfully');
    }
}
