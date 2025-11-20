<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeoSettingsController extends Controller
{
    protected $pageTypes = [
        'home' => 'Home Page',
        'services' => 'Services',
        'jobs' => 'Jobs',
        'blog' => 'Blog',
        'visa' => 'Visa Applications',
        'insurance' => 'Travel Insurance',
        'hotels' => 'Hotels',
        'flights' => 'Flights',
        'cv-builder' => 'CV Builder',
        'about' => 'About Us',
        'contact' => 'Contact',
    ];

    public function index()
    {
        $seoSettings = SeoSetting::all()->keyBy('page_type');
        
        return Inertia::render('Admin/SeoSettings/Index', [
            'settings' => $seoSettings,
            'pageTypes' => array_keys($this->pageTypes),
        ]);
    }

    public function update(Request $request, string $pageType)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'keywords' => ['nullable', 'string'],
            'canonical_url' => ['nullable', 'url'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'url'],
            'og_type' => ['nullable', 'string'],
            'twitter_card' => ['nullable', 'string'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'twitter_image' => ['nullable', 'url'],
            'twitter_site' => ['nullable', 'string'],
            'schema_markup' => ['nullable', 'array'],
            'index' => ['boolean'],
            'follow' => ['boolean'],
            'robots' => ['nullable', 'string'],
        ]);

        $seoSetting = SeoSetting::updateOrCreate(
            ['page_type' => $pageType],
            $validated
        );

        return back()->with('success', 'SEO settings updated successfully.');
    }

    public function destroy(string $pageType)
    {
        $seoSetting = SeoSetting::where('page_type', $pageType)->first();
        
        if ($seoSetting) {
            $seoSetting->delete();
            return back()->with('success', 'SEO settings deleted successfully.');
        }

        return back()->with('error', 'SEO settings not found.');
    }

    public function generateSitemap()
    {
        // Generate XML sitemap
        $pages = SeoSetting::where('index', true)->get();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        $baseUrl = config('app.url');
        
        foreach ($pages as $page) {
            // Use canonical URL if provided, otherwise construct from page type
            $url = $page->canonical_url ?: ($page->page_type === 'home' ? $baseUrl : $baseUrl . '/' . $page->page_type);
            
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url) . '</loc>';
            $xml .= '<lastmod>' . $page->updated_at->toW3cString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        // Save sitemap
        file_put_contents(public_path('sitemap.xml'), $xml);
        
        return back()->with('success', 'Sitemap generated successfully.');
    }
}
