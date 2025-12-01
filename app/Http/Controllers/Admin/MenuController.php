<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent', 'children')
            ->orderBy('location')
            ->orderBy('order')
            ->paginate(50);

        $locations = [
            'header_main' => 'Header Main Menu',
            'footer_column_1' => 'Footer Column 1',
            'footer_column_2' => 'Footer Column 2',
            'footer_column_3' => 'Footer Column 3',
            'mobile_menu' => 'Mobile Menu',
        ];

        return Inertia::render('Admin/Menus/Index', [
            'menus' => $menus,
            'locations' => $locations,
        ]);
    }

    public function create()
    {
        $locations = [
            'header_main' => 'Header Main Menu',
            'footer_column_1' => 'Footer Column 1',
            'footer_column_2' => 'Footer Column 2',
            'footer_column_3' => 'Footer Column 3',
            'mobile_menu' => 'Mobile Menu',
        ];

        $parentMenus = Menu::whereNull('parent_id')
            ->orderBy('label')
            ->get(['id', 'label', 'location']);

        // Get all named routes
        $routes = collect(Route::getRoutes())
            ->filter(fn($route) => $route->getName())
            ->map(fn($route) => $route->getName())
            ->sort()
            ->values()
            ->toArray();

        return Inertia::render('Admin/Menus/Create', [
            'locations' => $locations,
            'parentMenus' => $parentMenus,
            'availableRoutes' => $routes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:50',
            'label' => 'required|string|max:100',
            'url' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_external' => 'boolean',
            'target' => 'nullable|in:_self,_blank',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_external'] = $request->boolean('is_external', false);
        $validated['order'] = $validated['order'] ?? 999;

        Menu::create($validated);
        Menu::clearMenuCache();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        $locations = [
            'header_main' => 'Header Main Menu',
            'footer_column_1' => 'Footer Column 1',
            'footer_column_2' => 'Footer Column 2',
            'footer_column_3' => 'Footer Column 3',
            'mobile_menu' => 'Mobile Menu',
        ];

        $parentMenus = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('label')
            ->get(['id', 'label', 'location']);

        $routes = collect(Route::getRoutes())
            ->filter(fn($route) => $route->getName())
            ->map(fn($route) => $route->getName())
            ->sort()
            ->values()
            ->toArray();

        return Inertia::render('Admin/Menus/Edit', [
            'menu' => $menu->load('parent'),
            'locations' => $locations,
            'parentMenus' => $parentMenus,
            'availableRoutes' => $routes,
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:50',
            'label' => 'required|string|max:100',
            'url' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_external' => 'boolean',
            'target' => 'nullable|in:_self,_blank',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_external'] = $request->boolean('is_external');

        $menu->update($validated);
        Menu::clearMenuCache();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        // Delete children first
        $menu->children()->delete();
        $menu->delete();
        Menu::clearMenuCache();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:menus,id',
            'menus.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['menus'] as $menuData) {
            Menu::where('id', $menuData['id'])->update(['order' => $menuData['order']]);
        }

        Menu::clearMenuCache();

        return back()->with('success', 'Menu order updated successfully.');
    }

    public function clearCache()
    {
        Menu::clearMenuCache();
        
        return back()->with('success', 'Menu cache cleared successfully.');
    }
}
