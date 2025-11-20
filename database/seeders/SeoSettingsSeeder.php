<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class SeoSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'page_type' => 'home',
                'title' => 'BideshGomon - Your Trusted Partner for Overseas Migration',
                'description' => 'Complete visa application, job placement, and migration services for Bangladeshi citizens. Expert guidance for work permits, student visas, and immigration services.',
                'keywords' => 'bangladesh visa, overseas jobs, migration services, work permit, student visa, immigration bangladesh',
                'og_title' => 'BideshGomon - Migration & Visa Services',
                'og_description' => 'Your trusted partner for visa applications, job placements, and migration services from Bangladesh.',
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'twitter_site' => '@BideshGomon',
                'index' => true,
                'follow' => true,
            ],
            [
                'page_type' => 'services',
                'title' => 'Our Services - Visa, Insurance, Jobs & More',
                'description' => 'Comprehensive migration services including visa processing, travel insurance, hotel bookings, flight tickets, CV building, and job placements.',
                'keywords' => 'visa services, travel insurance, job placement, cv builder, flight booking, hotel reservation',
                'index' => true,
                'follow' => true,
            ],
            [
                'page_type' => 'jobs',
                'title' => 'International Job Opportunities',
                'description' => 'Find international job opportunities in Gulf countries, Europe, USA, Canada, and Australia. Browse thousands of verified job postings.',
                'keywords' => 'overseas jobs, international jobs, gulf jobs, europe jobs, canada jobs, usa jobs',
                'index' => true,
                'follow' => true,
            ],
            [
                'page_type' => 'visa',
                'title' => 'Visa Application Services - All Countries',
                'description' => 'Expert visa application assistance for tourist, work, study, and family visas. High success rate with professional document preparation.',
                'keywords' => 'visa application, tourist visa, work visa, student visa, visa processing, visa assistance',
                'index' => true,
                'follow' => true,
            ],
        ];

        foreach ($settings as $setting) {
            SeoSetting::updateOrCreate(
                ['page_type' => $setting['page_type']],
                $setting
            );
        }
    }
}
