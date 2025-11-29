<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceModule;
use App\Models\ServiceApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceModuleController extends Controller
{
    /**
     * Display service modules dashboard
     */
    public function index(): Response
    {
        $categories = ServiceCategory::with(['modules' => function ($query) {
            $query->withCount(['applications', 'reviews']);
        }])
        ->ordered()
        ->get()
        ->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'color' => $category->color,
                'description' => $category->description,
                'is_active' => $category->is_active,
                'modules_count' => $category->modules->count(),
                'active_modules_count' => $category->modules->where('is_active', true)->count(),
                'modules' => $category->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'slug' => $module->slug,
                        'short_description' => $module->short_description,
                        'is_active' => $module->is_active,
                        'is_featured' => $module->is_featured,
                        'coming_soon' => $module->coming_soon,
                        'formatted_price' => $module->formatted_price,
                        'price_type' => $module->price_type,
                        'base_price' => $module->base_price,
                        'currency' => $module->currency,
                        'applications_count' => $module->applications_count,
                        'reviews_count' => $module->reviews_count,
                        'completion_rate' => $module->completion_rate,
                        'views_count' => $module->views_count,
                        'route_prefix' => $module->route_prefix,
                        'controller' => $module->controller,
                    ];
                }),
            ];
        });

        // Statistics
        $stats = [
            'total_services' => ServiceModule::count(),
            'active_services' => ServiceModule::where('is_active', true)->count(),
            'coming_soon' => ServiceModule::where('is_active', false)->count(),
            'total_applications' => ServiceApplication::count(),
            'pending_applications' => ServiceApplication::pending()->count(),
            'completed_applications' => ServiceApplication::where('status', 'completed')->count(),
            'total_revenue' => ServiceApplication::where('payment_status', 'paid')->sum('amount'),
        ];

        return Inertia::render('Admin/ServiceModules/Index', [
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }

    /**
     * Show service module details
     */
    public function show(ServiceModule $serviceModule): Response
    {
        $serviceModule->load([
            'category',
            'applications' => function ($query) {
                $query->with('user')->latest()->take(10);
            },
            'reviews' => function ($query) {
                $query->with('user')->approved()->latest()->take(5);
            }
        ]);

        return Inertia::render('Admin/ServiceModules/Show', [
            'service' => [
                'id' => $serviceModule->id,
                'name' => $serviceModule->name,
                'slug' => $serviceModule->slug,
                'short_description' => $serviceModule->short_description,
                'full_description' => $serviceModule->full_description,
                'icon' => $serviceModule->icon,
                'image' => $serviceModule->image,
                'is_active' => $serviceModule->is_active,
                'is_featured' => $serviceModule->is_featured,
                'coming_soon' => $serviceModule->coming_soon,
                'launch_date' => $serviceModule->launch_date,
                'base_price' => $serviceModule->base_price,
                'price_type' => $serviceModule->price_type,
                'currency' => $serviceModule->currency,
                'formatted_price' => $serviceModule->formatted_price,
                'requirements' => $serviceModule->requirements,
                'documents_required' => $serviceModule->documents_required,
                'processing_time' => $serviceModule->processing_time,
                'processing_time_text' => $serviceModule->processing_time_text,
                'settings' => $serviceModule->settings,
                'route_prefix' => $serviceModule->route_prefix,
                'controller' => $serviceModule->controller,
                'allowed_roles' => $serviceModule->allowed_roles,
                'min_profile_completion' => $serviceModule->min_profile_completion,
                'meta_title' => $serviceModule->meta_title,
                'meta_description' => $serviceModule->meta_description,
                'meta_keywords' => $serviceModule->meta_keywords,
                'views_count' => $serviceModule->views_count,
                'applications_count' => $serviceModule->applications_count,
                'completions_count' => $serviceModule->completions_count,
                'completion_rate' => $serviceModule->completion_rate,
                'average_rating' => $serviceModule->average_rating,
                'reviews_count' => $serviceModule->reviews_count,
                'category' => $serviceModule->category,
                'recent_applications' => $serviceModule->applications,
                'recent_reviews' => $serviceModule->reviews,
            ],
        ]);
    }

    /**
     * Update service module
     */
    public function update(Request $request, ServiceModule $serviceModule)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'short_description' => 'nullable|string',
            'full_description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
            'coming_soon' => 'sometimes|boolean',
            'launch_date' => 'nullable|date',
            'base_price' => 'sometimes|numeric|min:0',
            'price_type' => 'sometimes|in:fixed,variable,free,quote',
            'currency' => 'sometimes|string',
            'requirements' => 'nullable|array',
            'documents_required' => 'nullable|array',
            'processing_time' => 'nullable|array',
            'settings' => 'nullable|array',
            'route_prefix' => 'nullable|string',
            'controller' => 'nullable|string',
            'allowed_roles' => 'nullable|array',
            'min_profile_completion' => 'nullable|integer|min:0|max:100',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $serviceModule->update($validated);

        return redirect()->back()->with('success', 'Service module updated successfully!');
    }

    /**
     * Toggle service active status
     */
    public function toggleActive(ServiceModule $serviceModule)
    {
        $serviceModule->update([
            'is_active' => !$serviceModule->is_active,
        ]);

        $status = $serviceModule->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Service {$status} successfully!");
    }

    /**
     * Toggle service featured status
     */
    public function toggleFeatured(ServiceModule $serviceModule)
    {
        $serviceModule->update([
            'is_featured' => !$serviceModule->is_featured,
        ]);

        return redirect()->back()->with('success', 'Service featured status updated!');
    }

    /**
     * Toggle coming soon status
     */
    public function toggleComingSoon(ServiceModule $serviceModule)
    {
        $serviceModule->update([
            'coming_soon' => !$serviceModule->coming_soon,
            'launch_date' => !$serviceModule->coming_soon ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Service coming soon status updated!');
    }

    /**
     * Bulk update services
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:service_modules,id',
            'action' => 'required|in:activate,deactivate,feature,unfeature',
        ]);

        $updates = match($validated['action']) {
            'activate' => ['is_active' => true],
            'deactivate' => ['is_active' => false],
            'feature' => ['is_featured' => true],
            'unfeature' => ['is_featured' => false],
        };

        ServiceModule::whereIn('id', $validated['service_ids'])->update($updates);

        return redirect()->back()->with('success', 'Services updated successfully!');
    }

    /**
     * Get service analytics
     */
    public function analytics(ServiceModule $serviceModule)
    {
        $analytics = [
            'views_trend' => [], // Would need additional tracking table
            'applications_by_status' => ServiceApplication::where('service_module_id', $serviceModule->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'revenue_by_month' => ServiceApplication::where('service_module_id', $serviceModule->id)
                ->where('payment_status', 'paid')
                ->selectRaw('DATE_FORMAT(payment_date, "%Y-%m") as month, SUM(amount) as revenue')
                ->groupBy('month')
                ->pluck('revenue', 'month'),
            'avg_processing_time' => ServiceApplication::where('service_module_id', $serviceModule->id)
                ->whereNotNull('completed_at')
                ->whereNotNull('submitted_at')
                ->selectRaw('AVG(DATEDIFF(completed_at, submitted_at)) as avg_days')
                ->value('avg_days'),
        ];

        return response()->json($analytics);
    }
}
