<?php

namespace App\Http\Controllers;

use App\Models\ServiceModule;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    /**
     * Display all available services
     */
    public function index()
    {
        $services = ServiceModule::with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $featured = ServiceModule::with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit(3)
            ->get();

        $categories = \DB::table('service_categories')
            ->join('service_modules', 'service_categories.id', '=', 'service_modules.service_category_id')
            ->where('service_modules.is_active', true)
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
     * Display a specific service
     */
    public function show($slug)
    {
        $service = ServiceModule::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('Services/Show', [
            'service' => $service,
        ]);
    }
}
