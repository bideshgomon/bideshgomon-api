<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceModule;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index(Request $request): Response
    {
        $query = ServiceModule::with('category')
            ->when($request->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            })
            ->when($request->category, function ($q, $category) {
                $q->where('service_category_id', $category);
            })
            ->when($request->status, function ($q, $status) {
                if ($status === 'active') {
                    $q->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $q->where('is_active', false);
                } elseif ($status === 'coming_soon') {
                    $q->where('coming_soon', true);
                }
            })
            ->orderBy('sort_order');

        $services = $query->paginate(20)->withQueryString();

        $categories = ServiceCategory::active()->ordered()->get();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new service
     */
    public function create(): Response
    {
        $categories = ServiceCategory::active()->ordered()->get();

        return Inertia::render('Admin/Services/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:service_modules,slug',
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'description' => 'nullable|string',
            'service_type' => 'nullable|string',
            'icon' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'coming_soon' => 'boolean',
            'requires_approval' => 'boolean',
            'base_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'processing_days' => 'nullable|integer|min:1',
            'requirements' => 'nullable|array',
            'documents_required' => 'nullable|array',
            'config' => 'nullable|array',
            'settings' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        // Set default sort_order if not provided
        if (!isset($validated['sort_order'])) {
            $maxOrder = ServiceModule::max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        $service = ServiceModule::create($validated);

        return redirect()
            ->route('admin.services.edit', $service)
            ->with('success', 'Service created successfully. Now add form fields to make it functional.');
    }

    /**
     * Display the specified service
     */
    public function show(ServiceModule $service): Response
    {
        $service->load([
            'category',
            'formFields' => fn($q) => $q->ordered(),
            'applications' => fn($q) => $q->latest()->limit(10),
        ]);

        $stats = [
            'total_applications' => $service->applications()->count(),
            'pending' => $service->applications()->where('status', 'pending')->count(),
            'approved' => $service->applications()->where('status', 'approved')->count(),
            'rejected' => $service->applications()->where('status', 'rejected')->count(),
        ];

        return Inertia::render('Admin/Services/Show', [
            'service' => $service,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit(ServiceModule $service): Response
    {
        $service->load('category');
        $categories = ServiceCategory::active()->ordered()->get();

        return Inertia::render('Admin/Services/Edit', [
            'service' => $service,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, ServiceModule $service)
    {
        $validated = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:service_modules,slug,' . $service->id,
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'description' => 'nullable|string',
            'service_type' => 'nullable|string',
            'icon' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'coming_soon' => 'boolean',
            'requires_approval' => 'boolean',
            'base_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'processing_days' => 'nullable|integer|min:1',
            'requirements' => 'nullable|array',
            'documents_required' => 'nullable|array',
            'config' => 'nullable|array',
            'settings' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                \Storage::disk('public')->delete($service->image);
            }
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($validated);

        return back()->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service
     */
    public function destroy(ServiceModule $service)
    {
        // Check if service has applications
        if ($service->applications()->exists()) {
            return back()->with('error', 'Cannot delete service with existing applications. Please archive it instead.');
        }

        // Delete image if exists
        if ($service->image) {
            \Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Reorder services (drag and drop)
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|exists:service_modules,id',
            'services.*.sort_order' => 'required|integer',
        ]);

        foreach ($validated['services'] as $serviceData) {
            ServiceModule::where('id', $serviceData['id'])
                ->update(['sort_order' => $serviceData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Services reordered successfully.',
        ]);
    }

    /**
     * Toggle service status (quick action)
     */
    public function toggleStatus(ServiceModule $service, Request $request)
    {
        $validated = $request->validate([
            'field' => 'required|in:is_active,is_featured,coming_soon',
        ]);

        $field = $validated['field'];
        $service->update([
            $field => !$service->$field,
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Duplicate a service
     */
    public function duplicate(ServiceModule $service)
    {
        $newService = $service->replicate();
        $newService->name = $service->name . ' (Copy)';
        $newService->slug = $service->slug . '-copy-' . time();
        $newService->is_active = false;
        $newService->applications_count = 0;
        $newService->views_count = 0;
        $newService->save();

        // Duplicate form fields
        foreach ($service->formFields as $field) {
            $newField = $field->replicate();
            $newField->service_module_id = $newService->id;
            $newField->save();
        }

        return redirect()
            ->route('admin.services.edit', $newService)
            ->with('success', 'Service duplicated successfully.');
    }

    /**
     * Get service statistics
     */
    public function statistics(ServiceModule $service)
    {
        return response()->json([
            'total_applications' => $service->applications()->count(),
            'pending' => $service->applications()->where('status', 'pending')->count(),
            'under_review' => $service->applications()->where('status', 'under_review')->count(),
            'approved' => $service->applications()->where('status', 'approved')->count(),
            'rejected' => $service->applications()->where('status', 'rejected')->count(),
            'completed' => $service->applications()->where('status', 'completed')->count(),
            'views' => $service->views_count,
            'avg_processing_days' => $service->applications()
                ->whereNotNull('approved_at')
                ->whereNotNull('submitted_at')
                ->selectRaw('AVG(DATEDIFF(approved_at, submitted_at)) as avg_days')
                ->value('avg_days'),
        ]);
    }
}
