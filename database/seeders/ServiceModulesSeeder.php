<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceModule;
use Illuminate\Database\Seeder;

class ServiceModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. VISA SERVICES CATEGORY
        $visaCategory = ServiceCategory::create([
            'name' => 'Visa Services',
            'slug' => 'visa',
            'icon' => 'fa-passport',
            'description' => 'Comprehensive visa application services for multiple visa types',
            'color' => '#3B82F6',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $visaServices = [
            // Note: General Visa Application removed - bgproject has dedicated Tourist Visa system
            [
                'name' => 'Tourist Visa',
                'slug' => 'tourist-visa',
                'service_type' => 'query_based', // User submits request, agencies respond
                'short_description' => 'Tourism and sightseeing visa applications',
                'is_active' => true,
                'coming_soon' => false,
                'base_price' => 4500,
                'route_prefix' => '/profile/tourist-visa',
                'controller' => 'TouristVisaApplicationPageController',
            ],
            [
                'name' => 'Student Visa',
                'slug' => 'student-visa',
                'service_type' => 'query_based',
                'short_description' => 'Study abroad visa processing',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 6000,
            ],
            [
                'name' => 'Work Visa',
                'slug' => 'work-visa',
                'service_type' => 'query_based',
                'short_description' => 'Employment-based visa assistance',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 7500,
            ],
            [
                'name' => 'Business Visa',
                'slug' => 'business-visa',
                'service_type' => 'query_based',
                'short_description' => 'Business travel and trade visas',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 5500,
            ],
            [
                'name' => 'Medical Visa',
                'slug' => 'medical-visa',
                'service_type' => 'query_based',
                'short_description' => 'Medical treatment visa processing',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 6500,
            ],
            [
                'name' => 'Family Visa',
                'slug' => 'family-visa',
                'service_type' => 'query_based',
                'short_description' => 'Family reunion and visit visas',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 5000,
            ],
            [
                'name' => 'Transit Visa',
                'slug' => 'transit-visa',
                'service_type' => 'query_based',
                'short_description' => 'Airport transit visa services',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 2500,
            ],
        ];

        foreach ($visaServices as $index => $service) {
            ServiceModule::create(array_merge($service, [
                'service_category_id' => $visaCategory->id,
                'price_type' => 'fixed',
                'currency' => 'BDT',
                'allowed_roles' => ['user', 'agency', 'consultant'],
                'min_profile_completion' => 60,
                'processing_time' => ['min' => 7, 'max' => 21, 'unit' => 'days'],
                'sort_order' => $index + 1,
            ]));
        }

        // 2. TRAVEL SERVICES CATEGORY
        $travelCategory = ServiceCategory::create([
            'name' => 'Travel Services',
            'slug' => 'travel',
            'icon' => 'fa-plane',
            'description' => 'Complete travel booking and insurance services',
            'color' => '#10B981',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $travelServices = [
            [
                'name' => 'Flight Booking',
                'slug' => 'flight-booking',
                'service_type' => 'query_based', // User requests quotes, agencies respond
                'short_description' => 'Book flights or request quotes from agencies',
                'is_active' => true,
                'coming_soon' => false,
                'base_price' => 0,
                'price_type' => 'variable',
                'route_prefix' => '/services/flights',
                'controller' => 'FlightBookingController',
            ],
            [
                'name' => 'Hotel Booking',
                'slug' => 'hotel-booking',
                'service_type' => 'api_based', // Real-time hotel search APIs
                'short_description' => 'Book hotels worldwide with instant confirmation',
                'is_active' => true,
                'coming_soon' => false,
                'base_price' => 0,
                'price_type' => 'variable',
                'route_prefix' => '/services/hotels',
                'controller' => 'HotelBookingController',
            ],
            [
                'name' => 'Travel Insurance',
                'slug' => 'travel-insurance',
                'service_type' => 'api_based', // Third-party insurance API integration
                'short_description' => 'Comprehensive travel insurance packages',
                'is_active' => true,
                'coming_soon' => false,
                'base_price' => 1500,
                'route_prefix' => '/services/travel-insurance',
                'controller' => 'TravelInsuranceController',
            ],
            [
                'name' => 'Airport Transfer',
                'slug' => 'airport-transfer',
                'service_type' => 'query_based',
                'short_description' => 'Reliable airport pickup and drop services',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 1200,
            ],
            [
                'name' => 'Car Rental',
                'slug' => 'car-rental',
                'service_type' => 'query_based',
                'short_description' => 'Rent cars with driver or self-drive options',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 2500,
                'price_type' => 'variable',
            ],
            [
                'name' => 'Tour Packages',
                'slug' => 'tour-packages',
                'service_type' => 'query_based',
                'short_description' => 'Pre-designed and customizable tour packages',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 15000,
                'price_type' => 'variable',
            ],
        ];

        foreach ($travelServices as $index => $service) {
            ServiceModule::create(array_merge($service, [
                'service_category_id' => $travelCategory->id,
                'currency' => 'BDT',
                'allowed_roles' => ['user', 'agency', 'consultant'],
                'min_profile_completion' => 30,
                'processing_time' => ['min' => 1, 'max' => 3, 'unit' => 'days'],
                'sort_order' => $index + 1,
            ]));
        }

        // 3. EDUCATION SERVICES CATEGORY
        $educationCategory = ServiceCategory::create([
            'name' => 'Education Services',
            'slug' => 'education',
            'icon' => 'fa-graduation-cap',
            'description' => 'Study abroad and education consulting services',
            'color' => '#8B5CF6',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $educationServices = [
            [
                'name' => 'University Application',
                'slug' => 'university-application',
                'service_type' => 'query_based', // User applies, consultants help
                'short_description' => 'University admission assistance worldwide',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 8000,
            ],
            [
                'name' => 'Course Counseling',
                'slug' => 'course-counseling',
                'service_type' => 'query_based',
                'short_description' => 'One-on-one educational guidance',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 3000,
            ],
            [
                'name' => 'Language Test Preparation',
                'slug' => 'language-test-prep',
                'service_type' => 'query_based',
                'short_description' => 'IELTS, TOEFL, PTE preparation courses',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 12000,
            ],
            [
                'name' => 'Scholarship Assistance',
                'slug' => 'scholarship-assistance',
                'service_type' => 'query_based',
                'short_description' => 'Find and apply for scholarships',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 5000,
            ],
        ];

        foreach ($educationServices as $index => $service) {
            ServiceModule::create(array_merge($service, [
                'service_category_id' => $educationCategory->id,
                'price_type' => 'fixed',
                'currency' => 'BDT',
                'allowed_roles' => ['user', 'consultant'],
                'min_profile_completion' => 70,
                'processing_time' => ['min' => 14, 'max' => 30, 'unit' => 'days'],
                'sort_order' => $index + 1,
            ]));
        }

        // 4. EMPLOYMENT SERVICES CATEGORY
        $employmentCategory = ServiceCategory::create([
            'name' => 'Employment Services',
            'slug' => 'employment',
            'icon' => 'fa-briefcase',
            'description' => 'Job search, CV building, and career services',
            'color' => '#F59E0B',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $employmentServices = [
            [
                'name' => 'Job Posting & Search',
                'slug' => 'job-search',
                'service_type' => 'premade', // Pre-built job board system
                'short_description' => 'Find international job opportunities',
                'is_active' => true,
                'coming_soon' => false,
                'base_price' => 0,
                'price_type' => 'free',
                'route_prefix' => '/services/jobs',
                'controller' => 'JobController',
            ],
            [
                'name' => 'CV Builder',
                'slug' => 'cv-builder',
                'service_type' => 'premade', // Pre-made CV templates and builder tool
                'short_description' => 'Create professional CVs with templates',
                'is_active' => true,
                'coming_soon' => false,
                'base_price' => 0,
                'price_type' => 'free',
                'route_prefix' => '/services/cv-builder',
                'controller' => 'CvBuilderController',
            ],
            [
                'name' => 'Interview Preparation',
                'slug' => 'interview-prep',
                'service_type' => 'query_based',
                'short_description' => 'Mock interviews and coaching',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 2500,
            ],
            [
                'name' => 'Skill Verification',
                'slug' => 'skill-verification',
                'service_type' => 'query_based',
                'short_description' => 'Professional credential verification',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 3500,
            ],
            [
                'name' => 'Work Permit Processing',
                'slug' => 'work-permit',
                'service_type' => 'query_based',
                'short_description' => 'Employment authorization assistance',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 6000,
            ],
        ];

        foreach ($employmentServices as $index => $service) {
            ServiceModule::create(array_merge($service, [
                'service_category_id' => $employmentCategory->id,
                'currency' => 'BDT',
                'allowed_roles' => ['user', 'agency', 'consultant'],
                'min_profile_completion' => 50,
                'processing_time' => ['min' => 3, 'max' => 7, 'unit' => 'days'],
                'sort_order' => $index + 1,
            ]));
        }

        // 5. DOCUMENT SERVICES CATEGORY
        $documentCategory = ServiceCategory::create([
            'name' => 'Document Services',
            'slug' => 'documents',
            'icon' => 'fa-file-alt',
            'description' => 'Document translation, attestation, and processing',
            'color' => '#EF4444',
            'sort_order' => 5,
            'is_active' => true,
        ]);

        $documentServices = [
            [
                'name' => 'Translation Services',
                'slug' => 'translation',
                'service_type' => 'query_based', // User submits docs, agencies quote
                'short_description' => 'Professional document translation',
                'is_active' => true,
                'coming_soon' => false,
                'base_price' => 500,
                'price_type' => 'variable',
                'route_prefix' => '/services/translation',
                'controller' => 'TranslationRequestController',
            ],
            [
                'name' => 'Document Attestation',
                'slug' => 'attestation',
                'service_type' => 'query_based',
                'short_description' => 'Embassy and MOFA attestation',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 2000,
            ],
            [
                'name' => 'Police Clearance Certificate',
                'slug' => 'police-clearance',
                'service_type' => 'query_based',
                'short_description' => 'Criminal background verification',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 1500,
            ],
            [
                'name' => 'Birth Certificate Services',
                'slug' => 'birth-certificate',
                'service_type' => 'query_based',
                'short_description' => 'Birth certificate processing',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 1000,
            ],
            [
                'name' => 'Passport Services',
                'slug' => 'passport-services',
                'service_type' => 'query_based',
                'short_description' => 'Passport application and renewal',
                'is_active' => false,
                'coming_soon' => true,
                'base_price' => 3000,
            ],
        ];

        foreach ($documentServices as $index => $service) {
            ServiceModule::create(array_merge($service, [
                'service_category_id' => $documentCategory->id,
                'currency' => 'BDT',
                'allowed_roles' => ['user', 'agency'],
                'min_profile_completion' => 40,
                'processing_time' => ['min' => 5, 'max' => 14, 'unit' => 'days'],
                'sort_order' => $index + 1,
            ]));
        }

        // 6. OTHER SERVICES CATEGORY
        $otherCategory = ServiceCategory::create([
            'name' => 'Other Services',
            'slug' => 'other',
            'icon' => 'fa-ellipsis-h',
            'description' => 'Additional specialized services',
            'color' => '#6366F1',
            'sort_order' => 6,
            'is_active' => true,
        ]);

        $otherServices = [
            ['name' => 'Hajj & Umrah Packages', 'slug' => 'hajj-umrah', 'service_type' => 'query_based', 'short_description' => 'Religious pilgrimage services', 'base_price' => 250000, 'price_type' => 'variable'],
            ['name' => 'Relocation Services', 'slug' => 'relocation', 'service_type' => 'query_based', 'short_description' => 'International moving assistance', 'base_price' => 15000, 'price_type' => 'variable'],
            ['name' => 'Legal Consultation', 'slug' => 'legal-consultation', 'service_type' => 'query_based', 'short_description' => 'Immigration legal advice', 'base_price' => 5000],
            ['name' => 'Medical Certificate', 'slug' => 'medical-certificate', 'service_type' => 'query_based', 'short_description' => 'Medical examination services', 'base_price' => 2000],
            ['name' => 'Bank Account Opening', 'slug' => 'bank-account', 'service_type' => 'query_based', 'short_description' => 'International banking assistance', 'base_price' => 3000],
            ['name' => 'Currency Exchange', 'slug' => 'currency-exchange', 'service_type' => 'query_based', 'short_description' => 'Foreign exchange services', 'base_price' => 0, 'price_type' => 'quote'],
            ['name' => 'SIM Card Services', 'slug' => 'sim-card', 'service_type' => 'query_based', 'short_description' => 'International SIM cards', 'base_price' => 500],
            ['name' => 'Accommodation Finding', 'slug' => 'accommodation', 'service_type' => 'query_based', 'short_description' => 'Long-term housing search', 'base_price' => 4000],
            ['name' => 'Tax Filing Assistance', 'slug' => 'tax-filing', 'service_type' => 'query_based', 'short_description' => 'International tax compliance', 'base_price' => 6000],
            ['name' => 'Cultural Integration Support', 'slug' => 'cultural-support', 'service_type' => 'query_based', 'short_description' => 'Settlement assistance', 'base_price' => 3500],
            ['name' => 'Emergency Assistance', 'slug' => 'emergency-assistance', 'service_type' => 'query_based', 'short_description' => '24/7 emergency support', 'base_price' => 1000, 'price_type' => 'variable'],
        ];

        foreach ($otherServices as $index => $service) {
            ServiceModule::create(array_merge($service, [
                'service_category_id' => $otherCategory->id,
                'is_active' => false,
                'coming_soon' => true,
                'price_type' => $service['price_type'] ?? 'fixed',
                'currency' => 'BDT',
                'allowed_roles' => ['user', 'agency', 'consultant'],
                'min_profile_completion' => 30,
                'processing_time' => ['min' => 3, 'max' => 10, 'unit' => 'days'],
                'sort_order' => $index + 1,
            ]));
        }

        $this->command->info('✅ Service categories and 39 service modules seeded successfully!');
    }
}
