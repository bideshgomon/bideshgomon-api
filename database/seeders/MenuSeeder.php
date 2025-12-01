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
            ['location' => 'header_main', 'label' => 'Jobs', 'route_name' => 'jobs.index', 'icon' => 'BriefcaseIcon', 'order' => 2],
            ['location' => 'header_main', 'label' => 'Universities', 'route_name' => 'universities.index', 'icon' => 'AcademicCapIcon', 'order' => 3],
            ['location' => 'header_main', 'label' => 'Visa Packages', 'route_name' => 'packages.index', 'icon' => 'DocumentTextIcon', 'order' => 4],
            ['location' => 'header_main', 'label' => 'Directory', 'route_name' => 'directory.index', 'icon' => 'BuildingOffice2Icon', 'order' => 5],
            ['location' => 'header_main', 'label' => 'Blogs', 'route_name' => 'blog-posts.index', 'icon' => 'NewspaperIcon', 'order' => 6],
            ['location' => 'header_main', 'label' => 'Contact', 'route_name' => 'contact', 'order' => 7],
        ];

        // Footer Column 1 - Company
        $footerCol1 = [
            ['location' => 'footer_column_1', 'label' => 'About Us', 'route_name' => 'about', 'order' => 1],
            ['location' => 'footer_column_1', 'label' => 'Our Team', 'route_name' => 'team', 'order' => 2],
            ['location' => 'footer_column_1', 'label' => 'Careers', 'route_name' => 'careers', 'order' => 3],
            ['location' => 'footer_column_1', 'label' => 'Contact Us', 'route_name' => 'contact', 'order' => 4],
        ];

        // Footer Column 2 - Services
        $footerCol2 = [
            ['location' => 'footer_column_2', 'label' => 'Find Jobs', 'route_name' => 'jobs.index', 'order' => 1],
            ['location' => 'footer_column_2', 'label' => 'Study Abroad', 'route_name' => 'universities.index', 'order' => 2],
            ['location' => 'footer_column_2', 'label' => 'Visa Services', 'route_name' => 'packages.index', 'order' => 3],
            ['location' => 'footer_column_2', 'label' => 'CV Builder', 'route_name' => 'cv-builder.index', 'order' => 4],
        ];

        // Footer Column 3 - Legal
        $footerCol3 = [
            ['location' => 'footer_column_3', 'label' => 'Privacy Policy', 'route_name' => 'privacy-policy', 'order' => 1],
            ['location' => 'footer_column_3', 'label' => 'Terms of Service', 'route_name' => 'terms-of-service', 'order' => 2],
            ['location' => 'footer_column_3', 'label' => 'Refund Policy', 'route_name' => 'refund-policy', 'order' => 3],
            ['location' => 'footer_column_3', 'label' => 'FAQ', 'route_name' => 'faq', 'order' => 4],
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
