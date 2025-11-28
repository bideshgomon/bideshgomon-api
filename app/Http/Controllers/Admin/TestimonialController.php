<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::with('updatedBy:id,name')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('author_name', 'like', '%' . $request->search . '%')
                  ->orWhere('author_name_bn', 'like', '%' . $request->search . '%')
                  ->orWhere('company', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            }
        }

        if ($request->has('rating') && $request->rating !== 'all') {
            $query->where('rating', $request->rating);
        }

        $testimonials = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Testimonials/Index', [
            'testimonials' => $testimonials,
            'filters' => $request->only(['search', 'status', 'rating'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Testimonials/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:255',
            'author_name_bn' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'position_bn' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'company_bn' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'location' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $validated['updated_by'] = auth()->id();

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully');
    }

    public function edit(Testimonial $testimonial)
    {
        return Inertia::render('Admin/Testimonials/Edit', [
            'testimonial' => $testimonial
        ]);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'author_name' => 'required|string|max:255',
            'author_name_bn' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'position_bn' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'company_bn' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_bn' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'location' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($request->hasFile('photo')) {
            if ($testimonial->photo && Storage::disk('public')->exists($testimonial->photo)) {
                Storage::disk('public')->delete($testimonial->photo);
            }
            $validated['photo'] = $request->file('photo')->store('testimonials', 'public');
        }

        $validated['updated_by'] = auth()->id();

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully');
    }

    public function toggleApproved(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_approved' => !$testimonial->is_approved,
            'updated_by' => auth()->id()
        ]);

        return back()->with('success', 'Testimonial approval status updated');
    }

    public function toggleFeatured(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_featured' => !$testimonial->is_featured,
            'updated_by' => auth()->id()
        ]);

        return back()->with('success', 'Testimonial featured status updated');
    }

    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_approved' => !$testimonial->is_approved,
            'updated_by' => auth()->id()
        ]);

        return back()->with('success', 'Testimonial approval status updated');
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'testimonials' => 'required|array',
            'testimonials.*.id' => 'required|exists:testimonials,id',
            'testimonials.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($validated['testimonials'] as $item) {
            Testimonial::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order']
            ]);
        }

        return back()->with('success', 'Order updated successfully');
    }
}
