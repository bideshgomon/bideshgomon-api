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
            ['location' => 'header_main', 'label' => 'Home', 'route_name' => 'welcome', 'order' => 1],
            ['location' => 'header_main', 'label' => 'Services', 'route_name' => 'services.index', 'icon' => 'RectangleStackIcon', 'order' => 2],
            ['location' => 'header_main', 'label' => 'CV Builder', 'route_name' => 'cv-builder.index', 'icon' => 'DocumentTextIcon', 'order' => 3],
            ['location' => 'header_main', 'label' => 'Contact', 'url' => '/contact', 'order' => 4],
        ];

        // Footer Column 1 - Company
        $footerCol1 = [
            ['location' => 'footer_column_1', 'label' => 'About Us', 'url' => '/about', 'order' => 1],
            ['location' => 'footer_column_1', 'label' => 'Services', 'route_name' => 'services.index', 'order' => 2],
            ['location' => 'footer_column_1', 'label' => 'Contact Us', 'url' => '/contact', 'order' => 3],
        ];

        // Footer Column 2 - Services
        $footerCol2 = [
            ['location' => 'footer_column_2', 'label' => 'Browse Services', 'route_name' => 'services.index', 'order' => 1],
            ['location' => 'footer_column_2', 'label' => 'CV Builder', 'route_name' => 'cv-builder.index', 'order' => 2],
            ['location' => 'footer_column_2', 'label' => 'My Applications', 'route_name' => 'dashboard', 'order' => 3],
        ];

        // Footer Column 3 - Legal
        $footerCol3 = [
            ['location' => 'footer_column_3', 'label' => 'Privacy Policy', 'route_name' => 'legal.privacy', 'order' => 1],
            ['location' => 'footer_column_3', 'label' => 'Terms of Service', 'route_name' => 'legal.terms', 'order' => 2],
            ['location' => 'footer_column_3', 'label' => 'Refund Policy', 'route_name' => 'legal.refund', 'order' => 3],
        ];

        $allMenus = array_merge($headerMenus, $footerCol1, $footerCol2, $footerCol3);

        foreach ($allMenus as $menu) {
            Menu::updateOrCreate(
                ['location' => $menu['location'], 'label' => $menu['label']],
                $menu
            );
        }
    }
}
