<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============================================
        // PHASE 1: Reference Tables (Foundation)
        // ============================================
        // These MUST run first - all other data depends on them
        $this->call([
            CountrySeeder::class,      // 50+ countries
            CurrencySeeder::class,     // 40+ currencies
            DegreeSeeder::class,       // 30+ degrees
            LanguageSeeder::class,     // 27+ languages
            CitySeeder::class,         // 100+ cities
        ]);
        
        // ============================================
        // PHASE 2: User Management
        // ============================================
        $this->call(RolesSeeder::class);
        $this->call(UserSeeder::class);  // Admin, Agency, Consultant users
        $this->call(ProfileManagementSeeder::class);
        $this->call(SimpleBangladeshiSeeder::class);
        
        // ============================================
        // PHASE 3: Additional Features
        // ============================================
        $this->call(ServiceModulesSeeder::class);  // Service categories and 39 service modules
        $this->call(JobPostingSeeder::class);
        $this->call(SettingsSeeder::class);
        $this->call(TravelInsurancePackageSeeder::class);
        $this->call(CvTemplateSeeder::class);
        $this->call(FlightRouteSeeder::class);
        // $this->call(BlogSeeder::class);
        // $this->call(ServiceSeeder::class);
    }
}
