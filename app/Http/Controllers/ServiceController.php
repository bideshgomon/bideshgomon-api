<?php

namespace App\Http\Controllers;

use App\Models\ServiceModule;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display all available services
     * Only shows revenue_service type (services that generate revenue)
     * Profile management is in dashboard, platform tools are user utilities
     */
    public function index(Request $request)
    {
        $query = ServiceModule::with('category')
            ->select([
                'id', 
                'service_category_id', 
                'name', 
                'slug', 
                'short_description', 
                'full_description as description',
                'service_type',
                'is_active',
                'coming_soon',
                'is_featured',
                'sort_order',
                'base_price',
                'icon'
            ])
            // Only show revenue-generating services
            ->where('service_type', 'revenue_service')
            ->where('is_active', true)
            ->when($request->search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('short_description', 'like', "%{$search}%")
                          ->orWhere('full_description', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($q, $category) {
                $q->where('service_category_id', $category);
            })
            ->when($request->featured, function ($q) {
                $q->where('is_featured', true);
            })
            ->orderBy('sort_order')
            ->orderBy('name');

        $services = $query->get()->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'short_description' => $service->short_description,
                    'description' => $service->description,
                    'service_type' => $service->service_type,
                    'is_active' => $service->is_active,
                    'coming_soon' => $service->coming_soon,
                    'is_featured' => $service->is_featured,
                    'base_price' => $service->base_price,
                    'icon' => $service->icon,
                    'category' => [
                        'id' => $service->category->id ?? null,
                        'name' => $service->category->name ?? 'Other',
                        'slug' => $service->category->slug ?? 'other',
                    ],
                ];
            });

        // Get categories for filter
        $categories = ServiceCategory::whereHas('serviceModules', function ($q) {
            $q->where('service_type', 'revenue_service')
              ->where('is_active', true);
        })->orderBy('name')->get();

        $featured = ServiceModule::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            // Only show revenue-generating services
            ->where('service_type', 'revenue_service')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'featured' => $featured,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'featured']),
        ]);
    }

    /**
     * Display a specific service - enhanced with plugin architecture
     */
    public function show($slug)
    {
        $service = ServiceModule::with([
            'category',
            'formFields' => function ($query) {
                $query->active()->ordered();
            }
        ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Route to dedicated service pages based on slug (legacy services)
        $routeMap = [
            'travel-insurance' => 'travel-insurance.index',
            'flight-requests' => 'flight-requests.index',
            'cv-builder' => 'cv-builder.index',
        ];

        // If service has a dedicated page, redirect to it
        if (isset($routeMap[$slug])) {
            return redirect()->route($routeMap[$slug]);
        }

        // Check if user already has an application for this service
        $existingApplication = null;
        if (auth()->check()) {
            $existingApplication = auth()->user()
                ->serviceApplications()
                ->where('service_module_id', $service->id)
                ->whereIn('status', ['draft', 'pending', 'under_review'])
                ->latest()
                ->first();
        }

        // Get related services
        $relatedServices = ServiceModule::with(['category'])
            ->where('is_active', true)
            ->where('service_type', 'revenue_service')
            ->where('id', '!=', $service->id)
            ->where(function ($query) use ($service) {
                $query->where('service_category_id', $service->service_category_id);
            })
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        // Get application statistics
        $stats = [
            'total_applications' => $service->applications()->count(),
            'approved_applications' => $service->applications()->where('status', 'approved')->count(),
            'avg_processing_days' => $service->processing_days ?? 0,
        ];

        // Countries and professions for legacy forms
        $countries = \App\Models\Country::select('id', 'name', 'iso_code_2 as code', 'flag_emoji as flag')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $professions = [
            'Student', 'Engineer', 'Doctor', 'Teacher', 'Business Owner',
            'IT Professional', 'Accountant', 'Manager', 'Consultant', 'Lawyer',
            'Architect', 'Designer', 'Nurse', 'Chef', 'Artist', 'Writer',
            'Freelancer', 'Government Employee', 'Private Employee', 'Retired',
            'Self Employed', 'Other'
        ];

        return Inertia::render('Services/Show', [
            'service' => $service,
            'countries' => $countries,
            'professions' => $professions,
            'relatedServices' => $relatedServices,
            'existingApplication' => $existingApplication,
            'stats' => $stats,
            'canApply' => auth()->check() && !$existingApplication,
        ]);
    }

    /**
     * Search services (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $services = ServiceModule::where('is_active', true)
            ->where('service_type', 'revenue_service')
            ->where(function ($query) use ($validated) {
                $query->where('name', 'like', "%{$validated['q']}%")
                      ->orWhere('short_description', 'like', "%{$validated['q']}%")
                      ->orWhere('full_description', 'like', "%{$validated['q']}%");
            })
            ->orderBy('sort_order')
            ->limit($validated['limit'] ?? 10)
            ->get(['id', 'name', 'slug', 'icon', 'base_price', 'short_description']);

        return response()->json([
            'services' => $services,
        ]);
    }
}
