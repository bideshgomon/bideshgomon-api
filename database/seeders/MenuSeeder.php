<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Header Main Menu
        $headerMenus = [
            ['location' => 'header_main', 'name' => 'Home', 'slug' => 'home', 'route_name' => 'welcome', 'order' => 1],
            ['location' => 'header_main', 'name' => 'Services', 'slug' => 'services', 'route_name' => 'services.index', 'icon' => 'RectangleStackIcon', 'order' => 2],
            ['location' => 'header_main', 'name' => 'Directory', 'slug' => 'directory', 'route_name' => 'directory.index', 'icon' => 'BuildingOfficeIcon', 'order' => 3],
            ['location' => 'header_main', 'name' => 'Blog', 'slug' => 'blog', 'route_name' => 'blog.index', 'icon' => 'NewspaperIcon', 'order' => 4],
            ['location' => 'header_main', 'name' => 'CV Builder', 'slug' => 'cv-builder', 'route_name' => 'cv-builder.index', 'icon' => 'DocumentTextIcon', 'order' => 5],
            ['location' => 'header_main', 'name' => 'Contact', 'slug' => 'contact', 'url' => '/contact', 'order' => 6],
        ];

        // Footer Column 1 - Company
        $footerCol1 = [
            ['location' => 'footer_column_1', 'name' => 'About Us', 'slug' => 'about-us', 'url' => '/about', 'order' => 1],
            ['location' => 'footer_column_1', 'name' => 'Services', 'slug' => 'footer-services', 'route_name' => 'services.index', 'order' => 2],
            ['location' => 'footer_column_1', 'name' => 'Contact Us', 'slug' => 'contact-us', 'url' => '/contact', 'order' => 3],
        ];

        // Footer Column 2 - Services
        $footerCol2 = [
            ['location' => 'footer_column_2', 'name' => 'Browse Services', 'slug' => 'browse-services', 'route_name' => 'services.index', 'order' => 1],
            ['location' => 'footer_column_2', 'name' => 'CV Builder', 'slug' => 'footer-cv-builder', 'route_name' => 'cv-builder.index', 'order' => 2],
            ['location' => 'footer_column_2', 'name' => 'My Applications', 'slug' => 'my-applications', 'route_name' => 'dashboard', 'order' => 3],
        ];

        // Footer Column 3 - Legal
        $footerCol3 = [
            ['location' => 'footer_column_3', 'name' => 'Privacy Policy', 'slug' => 'privacy-policy', 'route_name' => 'legal.privacy', 'order' => 1],
            ['location' => 'footer_column_3', 'name' => 'Terms of Service', 'slug' => 'terms-of-service', 'route_name' => 'legal.terms', 'order' => 2],
            ['location' => 'footer_column_3', 'name' => 'Refund Policy', 'slug' => 'refund-policy', 'route_name' => 'legal.refund', 'order' => 3],
        ];

        $allMenus = array_merge($headerMenus, $footerCol1, $footerCol2, $footerCol3);

        foreach ($allMenus as $menu) {
            Menu::updateOrCreate(
                ['slug' => $menu['slug']],
                $menu
            );
        }
    }
}
