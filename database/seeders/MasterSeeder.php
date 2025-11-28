<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Master Seeder - Intelligent Re-seeding Strategy
 * 
 * This seeder handles both fresh seeding and re-seeding scenarios.
 * It safely clears existing data and rebuilds everything from scratch.
 * 
 * Usage:
 * - Fresh seed: php artisan db:seed --class=MasterSeeder
 * - After migration: php artisan migrate:fresh --seed --seeder=MasterSeeder
 */
class MasterSeeder extends Seeder
{
    /**
     * Tables to truncate in specific order (respects foreign keys)
     */
    private array $truncateOrder = [
        // Dependent tables first
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'user_profiles',
        'user_passports',
        'user_visas',
        'user_travel_history',
        'user_family_members',
        'user_educations',
        'user_work_experiences',
        'user_languages',
        'user_skills',
        'user_references',
        'user_certifications',
        'phone_numbers',
        'wallet_transactions',
        'wallets',
        'job_applications',
        'service_requests',
        'bookings',
        'cv_downloads',
        
        // Main tables
        'users',
        'job_postings',
        'service_modules',
        'service_categories',
        'cv_templates',
        'travel_insurance_packages',
        'flight_routes',
        'settings',
        
        // Reference tables (can be truncated last)
        'cities',
        'languages',
        'degrees',
        'currencies',
        'countries',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('           🚀 MASTER SEEDER - INTELLIGENT RE-SEEDING');
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('');

        // Step 1: Clear existing data safely
        $this->clearExistingData();

        // Step 2: Seed in correct order
        $this->seedData();

        // Step 3: Verify seeding
        $this->verifySeeding();

        $this->command->info('');
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('           ✅ MASTER SEEDING COMPLETED SUCCESSFULLY!');
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('');
    }

    /**
     * Clear existing data in correct order
     */
    private function clearExistingData(): void
    {
        $this->command->warn('🧹 Clearing existing data...');
        
        // Disable foreign key checks temporarily
        Schema::disableForeignKeyConstraints();

        foreach ($this->truncateOrder as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->command->line("  ✓ Cleared: {$table}");
            }
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Data cleared successfully!');
        $this->command->info('');
    }

    /**
     * Seed data in correct dependency order
     */
    private function seedData(): void
    {
        $this->command->info('📦 Seeding data in correct order...');
        $this->command->info('');

        // ============================================
        // PHASE 1: Reference Tables (Foundation)
        // ============================================
        $this->command->warn('PHASE 1: Reference Data (Foundation)');
        $this->call([
            CountrySeeder::class,      // 196 countries
            CurrencySeeder::class,     // 41 currencies
            DegreeSeeder::class,       // 29 degrees
            LanguageSeeder::class,     // 28 languages
            CitySeeder::class,         // 38+ cities
        ]);
        $this->command->info('');

        // ============================================
        // PHASE 2: User Management & Authentication
        // ============================================
        $this->command->warn('PHASE 2: Users & Authentication');
        $this->call([
            RolesSeeder::class,                  // 4 roles (admin, user, agency, consultant)
            UserSeeder::class,                   // 4 main users
            ProfileManagementSeeder::class,      // john@test.com with comprehensive data
            SimpleBangladeshiSeeder::class,      // 3 Bangladeshi profiles
        ]);
        $this->command->info('');

        // ============================================
        // PHASE 3: Service Modules & Categories
        // ============================================
        $this->command->warn('PHASE 3: Service Infrastructure');
        $this->call([
            ServiceModulesSeeder::class,         // 6 categories, 39 service modules
        ]);
        $this->command->info('');

        // ============================================
        // PHASE 4: Job & Employment Services
        // ============================================
        $this->command->warn('PHASE 4: Jobs & Employment');
        $this->call([
            JobPostingSeeder::class,             // 10 job postings
        ]);
        $this->command->info('');

        // ============================================
        // PHASE 5: Travel & Insurance
        // ============================================
        $this->command->warn('PHASE 5: Travel Services');
        $this->call([
            TravelInsurancePackageSeeder::class, // 6 insurance packages
            FlightRouteSeeder::class,            // Popular flight routes
        ]);
        $this->command->info('');

        // ============================================
        // PHASE 6: CV System
        // ============================================
        $this->command->warn('PHASE 6: CV Templates');
        $this->call([
            CvTemplateSeederCompatible::class,   // 48 CV templates (3 free, 45 premium)
        ]);
        $this->command->info('');

        // ============================================
        // PHASE 7: System Configuration
        // ============================================
        $this->command->warn('PHASE 7: System Settings');
        $this->call([
            SettingsSeeder::class,               // Application settings
        ]);
        $this->command->info('');
    }

    /**
     * Verify seeding results
     */
    private function verifySeeding(): void
    {
        $this->command->info('🔍 Verifying seeding results...');
        $this->command->info('');

        $verifications = [
            // Reference Data
            ['table' => 'countries', 'expected' => 196, 'label' => 'Countries'],
            ['table' => 'currencies', 'expected' => 41, 'label' => 'Currencies'],
            ['table' => 'degrees', 'expected' => 29, 'label' => 'Degrees'],
            ['table' => 'languages', 'expected' => 28, 'label' => 'Languages'],
            ['table' => 'cities', 'expected' => 30, 'label' => 'Cities', 'min' => true],
            
            // Users & Auth
            ['table' => 'users', 'expected' => 7, 'label' => 'Users', 'min' => true],
            ['table' => 'user_profiles', 'expected' => 7, 'label' => 'User Profiles', 'min' => true],
            ['table' => 'roles', 'expected' => 4, 'label' => 'Roles'],
            
            // Services
            ['table' => 'service_categories', 'expected' => 6, 'label' => 'Service Categories'],
            ['table' => 'service_modules', 'expected' => 39, 'label' => 'Service Modules'],
            ['table' => 'job_postings', 'expected' => 10, 'label' => 'Job Postings'],
            
            // Templates & Packages
            ['table' => 'cv_templates', 'expected' => 48, 'label' => 'CV Templates'],
            ['table' => 'travel_insurance_packages', 'expected' => 6, 'label' => 'Insurance Packages'],
        ];

        $allPassed = true;

        foreach ($verifications as $check) {
            $count = DB::table($check['table'])->count();
            $isMin = $check['min'] ?? false;
            $passed = $isMin ? ($count >= $check['expected']) : ($count === $check['expected']);
            
            $symbol = $passed ? '✅' : '❌';
            $comparison = $isMin ? "≥ {$check['expected']}" : "= {$check['expected']}";
            
            $this->command->line(sprintf(
                '  %s %s: %d %s',
                $symbol,
                str_pad($check['label'], 25),
                $count,
                $passed ? '' : "(expected {$comparison})"
            ));

            if (!$passed) {
                $allPassed = false;
            }
        }

        $this->command->info('');

        if ($allPassed) {
            $this->command->info('✅ All verification checks passed!');
        } else {
            $this->command->error('⚠️  Some verification checks failed. Please review the data.');
        }

        // Show active services count
        $activeServices = DB::table('service_modules')->where('is_active', true)->count();
        $this->command->info('');
        $this->command->info("📊 Active Services Available: {$activeServices}/39");
        
        // Show test credentials
        $this->showTestCredentials();
    }

    /**
     * Display test credentials for quick access
     */
    private function showTestCredentials(): void
    {
        $this->command->info('');
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('           🔐 TEST CREDENTIALS (Password: password)');
        $this->command->info('════════════════════════════════════════════════════════════════');
        $this->command->info('');
        
        $credentials = [
            ['email' => 'admin@bgplatform.com', 'role' => 'Admin', 'description' => 'Full system access'],
            ['email' => 'agency@bgplatform.com', 'role' => 'Agency', 'description' => 'Service provider'],
            ['email' => 'consultant@bgplatform.com', 'role' => 'Consultant', 'description' => 'Education consultant'],
            ['email' => 'user@bgplatform.com', 'role' => 'User', 'description' => 'Regular user'],
            ['email' => 'john@test.com', 'role' => 'User', 'description' => 'Test data (visas, travel, red flags)'],
            ['email' => 'rahim.gulf@test.com', 'role' => 'User', 'description' => 'Gulf worker profile'],
            ['email' => 'nafisa.student@test.com', 'role' => 'User', 'description' => 'UK student profile'],
            ['email' => 'tanvir.it@test.com', 'role' => 'User', 'description' => 'IT professional (Canada PR)'],
        ];

        foreach ($credentials as $cred) {
            $this->command->line(sprintf(
                '  📧 %-30s | %-10s | %s',
                $cred['email'],
                $cred['role'],
                $cred['description']
            ));
        }

        $this->command->info('');
    }
}
