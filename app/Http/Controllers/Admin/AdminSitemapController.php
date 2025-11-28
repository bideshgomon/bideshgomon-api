<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class AdminSitemapController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes())
            ->filter(function ($route) {
                // Only admin routes
                return str_starts_with($route->uri(), 'admin/');
            })
            ->map(function ($route) {
                $methods = implode('|', $route->methods());
                $uri = $route->uri();
                $name = $route->getName();
                $action = $route->getActionName();
                
                // Extract controller and method
                $controllerAction = last(explode('\\', $action));
                
                // Categorize routes
                $category = $this->categorizeRoute($uri);
                
                return [
                    'methods' => $methods,
                    'uri' => $uri,
                    'name' => $name,
                    'action' => $controllerAction,
                    'category' => $category,
                    'url' => url($uri),
                    'has_params' => str_contains($uri, '{'),
                ];
            })
            ->groupBy('category')
            ->map(function ($group) {
                return $group->sortBy('uri')->values();
            })
            ->sortKeys();

        $stats = [
            'total_routes' => $routes->flatten(1)->count(),
            'categories' => $routes->count(),
            'with_params' => $routes->flatten(1)->where('has_params', true)->count(),
            'without_params' => $routes->flatten(1)->where('has_params', false)->count(),
        ];

        return Inertia::render('Admin/Sitemap/Index', [
            'routes' => $routes,
            'stats' => $stats,
        ]);
    }

    private function categorizeRoute(string $uri): string
    {
        // Extract main category from URI
        $parts = explode('/', $uri);
        
        if (count($parts) < 2) {
            return 'General';
        }

        $mainPart = $parts[1] ?? 'general';

        // Map to friendly categories
        $categoryMap = [
            'dashboard' => 'Dashboard',
            'users' => 'User Management',
            'agencies' => 'Agency Management',
            'agency-assignments' => 'Agency Assignments',
            'agency-resources' => 'Agency Resources',
            'applications' => 'Applications',
            'service-applications' => 'Service Applications',
            'service-quotes' => 'Service Quotes',
            'service-modules' => 'Service Management',
            'service-categories' => 'Service Management',
            'visa-requirements' => 'Visa Requirements',
            'document-assignments' => 'Document Management',
            'master-documents' => 'Document Management',
            'wallets' => 'Wallet System',
            'data-management' => 'Data Management',
            'analytics' => 'Analytics',
            'settings' => 'Settings',
            'seo' => 'SEO Management',
            'blog' => 'Blog Management',
            'jobs' => 'Job Management',
            'impersonation' => 'Impersonation',
            'notifications' => 'Notifications',
            'hotels' => 'Hotel Management',
            'flights' => 'Flight Management',
            'translations' => 'Translation Management',
            'rewards' => 'Rewards System',
            'marketing' => 'Marketing',
            'cv-templates' => 'CV Templates',
        ];

        return $categoryMap[$mainPart] ?? ucfirst(str_replace('-', ' ', $mainPart));
    }
}
