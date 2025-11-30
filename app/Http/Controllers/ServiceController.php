<?php

namespace App\Http\Controllers;

use App\Models\ServiceModule;
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
    public function index()
    {
        $services = ServiceModule::with('category')
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
                'sort_order'
            ])
            // Only show revenue-generating services
            ->where('service_type', 'revenue_service')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($service) {
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
                    'category' => [
                        'id' => $service->category->id ?? null,
                        'name' => $service->category->name ?? 'Other',
                        'slug' => $service->category->slug ?? 'other',
                    ],
                ];
            });

        $featured = ServiceModule::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            // Only show revenue-generating services
            ->where('service_type', 'revenue_service')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $categories = DB::table('service_categories')
            ->join('service_modules', 'service_categories.id', '=', 'service_modules.service_category_id')
            ->select('service_categories.id', 'service_categories.name', 'service_categories.slug')
            ->distinct()
            ->orderBy('service_categories.name')
            ->get();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'featured' => $featured,
            'categories' => $categories,
        ]);
    }

    /**
     * Display a specific service - routes to dedicated service pages
     */
    public function show($slug)
    {
        $service = ServiceModule::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Route to dedicated service pages based on slug
        $routeMap = [
            'travel-insurance' => 'travel-insurance.index',
            'flight-requests' => 'flight-requests.index',
            'cv-builder' => 'cv-builder.index',
        ];

        // If service has a dedicated page, redirect to it
        if (isset($routeMap[$slug])) {
            return redirect()->route($routeMap[$slug]);
        }

        // Otherwise show generic service detail page
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
        ]);
    }
}
