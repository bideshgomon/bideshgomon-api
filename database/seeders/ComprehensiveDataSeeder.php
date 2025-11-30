<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\VisaType;
use App\Models\BlogCategory;
use App\Models\BlogTag;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Seed all missing reference data for Data Management system
     * Based on Bangladesh/Middle East migration context
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive Data Seeding...');
        
        // 1. Visa Types (currently 0 records)
        $this->seedVisaTypes();
        
        // 2. Blog Categories (currently 0 records)
        $this->seedBlogCategories();
        
        // 3. Blog Tags (currently 0 records)
        $this->seedBlogTags();
        
        $this->command->info('✅ All reference data seeded successfully!');
    }
    
    /**
     * Seed Visa Types - Bangladesh context
     */
    private function seedVisaTypes(): void
    {
        $this->command->info('📝 Seeding Visa Types...');
        
        // Note: visa_types table doesn't have 'slug' column, only: id, country_id, name, code, description, processing_time_days, validity_days, base_fee, is_active
        // We'll seed with simplified data that matches actual schema
        
        $visaTypes = [
            // Work Visas (Most common for Bangladeshis)
            ['name' => 'Work Visa', 'code' => 'WORK', 'description' => 'For employment in foreign countries. Popular for Middle East, Malaysia, Singapore.'],
            ['name' => 'Skilled Worker Visa', 'code' => 'SKILLED', 'description' => 'For professionals with specialized skills (Engineers, IT, Healthcare).'],
            ['name' => 'Labor Visa', 'code' => 'LABOR', 'description' => 'For construction, hospitality, domestic workers in Middle East.'],
            
            // Tourist Visas
            ['name' => 'Tourist Visa', 'code' => 'TOURIST', 'description' => 'For leisure travel, sightseeing, and vacation purposes.'],
            ['name' => 'Visit Visa', 'code' => 'VISIT', 'description' => 'For visiting family members or friends abroad.'],
            ['name' => 'Business Visit Visa', 'code' => 'BIZ_VISIT', 'description' => 'For business meetings, conferences, and trade events.'],
            
            // Student Visas
            ['name' => 'Student Visa', 'code' => 'STUDENT', 'description' => 'For international students pursuing education abroad.'],
            ['name' => 'Student Dependent Visa', 'code' => 'STU_DEP', 'description' => 'For dependents (spouse/children) of international students.'],
            
            // Family Visas
            ['name' => 'Family Visa', 'code' => 'FAMILY', 'description' => 'For joining family members residing abroad.'],
            ['name' => 'Spouse Visa', 'code' => 'SPOUSE', 'description' => 'For spouses of citizens or permanent residents.'],
            ['name' => 'Dependent Visa', 'code' => 'DEPENDENT', 'description' => 'For children and dependents of visa holders.'],
            
            // Medical Visas
            ['name' => 'Medical Visa', 'code' => 'MEDICAL', 'description' => 'For medical treatment in foreign countries (India, Thailand, Singapore).'],
            ['name' => 'Medical Attendant Visa', 'code' => 'MED_ATT', 'description' => 'For companions accompanying medical patients.'],
            
            // Permanent Residence
            ['name' => 'Permanent Residence Visa', 'code' => 'PR', 'description' => 'For permanent residence in countries like Canada, Australia.'],
            ['name' => 'Investor Visa', 'code' => 'INVESTOR', 'description' => 'For individuals investing in foreign businesses or real estate.'],
            
            // E-Visas
            ['name' => 'E-Visa', 'code' => 'E_VISA', 'description' => 'Electronic visa obtained online (Turkey, Malaysia, India, etc.).'],
            ['name' => 'Visa on Arrival', 'code' => 'VOA', 'description' => 'Visa issued upon arrival at destination airport.'],
            
            // Religious Visas
            ['name' => 'Umrah Visa', 'code' => 'UMRAH', 'description' => 'For performing Umrah pilgrimage to Saudi Arabia.'],
            ['name' => 'Hajj Visa', 'code' => 'HAJJ', 'description' => 'For performing Hajj pilgrimage to Saudi Arabia.'],
        ];
        
        // Note: visa_types requires country_id but we'll use Bangladesh (id=1) as default
        $bangladeshId = DB::table('countries')->where('name', 'Bangladesh')->value('id') ?? 1;
        
        foreach ($visaTypes as $type) {
            VisaType::create([
                'country_id' => $bangladeshId,
                'name' => $type['name'],
                'code' => $type['code'],
                'description' => $type['description'],
                'processing_time_days' => 15, // Default
                'validity_days' => 365, // Default 1 year
                'base_fee' => 5000.00, // Default ৳5,000
                'is_active' => true,
            ]);
        }
        
        $this->command->info('  ✅ Seeded ' . count($visaTypes) . ' visa types');
    }
    
    /**
     * Seed Blog Categories - Bangladesh migration context
     */
    private function seedBlogCategories(): void
    {
        $this->command->info('📝 Seeding Blog Categories...');
        
        $categories = [
            [
                'name' => 'Visa Guides',
                'slug' => 'visa-guides',
                'description' => 'Complete guides for visa applications to different countries',
                'order' => 1,
            ],
            [
                'name' => 'Study Abroad',
                'slug' => 'study-abroad',
                'description' => 'Everything about studying overseas - universities, scholarships, admission',
                'order' => 2,
            ],
            [
                'name' => 'Work Abroad',
                'slug' => 'work-abroad',
                'description' => 'Opportunities for working overseas, job markets, salary expectations',
                'order' => 3,
            ],
            [
                'name' => 'Travel Tips',
                'slug' => 'travel-tips',
                'description' => 'Tips for international travelers - packing, budgeting, safety',
                'order' => 4,
            ],
            [
                'name' => 'Immigration News',
                'slug' => 'immigration-news',
                'description' => 'Latest immigration news, policy changes, visa updates',
                'order' => 5,
            ],
            [
                'name' => 'Country Guides',
                'slug' => 'country-guides',
                'description' => 'Complete guides about living, working, and studying in different countries',
                'order' => 6,
            ],
            [
                'name' => 'Middle East Jobs',
                'slug' => 'middle-east-jobs',
                'description' => 'Job opportunities in UAE, Saudi Arabia, Qatar, Kuwait, Oman, Bahrain',
                'order' => 7,
            ],
            [
                'name' => 'Success Stories',
                'slug' => 'success-stories',
                'description' => 'Inspiring stories of Bangladeshi migrants succeeding abroad',
                'order' => 8,
            ],
        ];
        
        foreach ($categories as $category) {
            BlogCategory::create($category);
        }
        
        $this->command->info('  ✅ Seeded ' . count($categories) . ' blog categories');
    }
    
    /**
     * Seed Blog Tags - Bangladesh migration keywords
     */
    private function seedBlogTags(): void
    {
        $this->command->info('📝 Seeding Blog Tags...');
        
        $tagNames = [
            // Countries
            'Bangladesh', 'USA', 'Canada', 'UK', 'Australia', 'Germany', 
            'UAE', 'Dubai', 'Saudi Arabia', 'Qatar', 'Kuwait', 'Oman', 'Bahrain',
            'Malaysia', 'Singapore', 'Japan', 'Korea', 'Thailand', 'Turkey',
            
            // Visa Types
            'Tourist Visa', 'Student Visa', 'Work Visa', 'Family Visa', 
            'Business Visa', 'Medical Visa', 'Schengen Visa', 'E-Visa',
            'Work Permit', 'PR Visa', 'Umrah Visa', 'Hajj Visa',
            
            // Topics
            'Immigration', 'Migration', 'Overseas Jobs', 'Study Abroad',
            'IELTS', 'TOEFL', 'Scholarship', 'Embassy', 'Documentation',
            'Bank Statement', 'Travel Insurance', 'Flight Booking',
            
            // Professions (Bangladesh workers)
            'Construction Worker', 'Hospitality', 'Healthcare', 'IT Jobs',
            'Engineering', 'Teaching', 'Nursing', 'Domestic Worker',
            
            // Other
            'Tips & Tricks', 'Success Stories', 'Policy Changes', 
            'Application Process', 'Interview Preparation', 'Visa Fees',
        ];
        
        foreach ($tagNames as $name) {
            BlogTag::create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
            ]);
        }
        
        $this->command->info('  ✅ Seeded ' . count($tagNames) . ' blog tags');
    }
}
