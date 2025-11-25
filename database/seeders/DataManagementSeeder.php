<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DataManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Data Management Seeding...');

        // Seed in order of dependencies
        $this->seedCountries();
        $this->seedCurrencies();
        $this->seedCities();
        $this->seedAirports();
        $this->seedLanguages();
        $this->seedLanguageTests();
        $this->seedDegrees();
        $this->seedSkillCategories();
        $this->seedSkills();
        $this->seedJobCategories();
        $this->seedServiceCategories();
        $this->seedBlogCategories();
        $this->seedBlogTags();

        $this->command->info('✅ Data Management Seeding Completed!');
    }

    private function seedCountries(): void
    {
        $this->command->info('Seeding countries...');
        $csvPath = database_path('seeders/csv/countries.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('countries')->insert([
                'name' => $data['name'],
                'name_bn' => $data['name_bn'],
                'iso_code_2' => $data['iso_code_2'],
                'iso_code_3' => $data['iso_code_3'],
                'phone_code' => $data['phone_code'],
                'currency_code' => $data['currency_code'],
                'flag_emoji' => $data['flag_emoji'],
                'region' => $data['region'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Countries seeded: ' . count($csv));
    }

    private function seedCurrencies(): void
    {
        $this->command->info('Seeding currencies...');
        $csvPath = database_path('seeders/csv/currencies.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('currencies')->insert([
                'code' => $data['code'],
                'name' => $data['name'],
                'symbol' => $data['symbol'],
                'exchange_rate_to_bdt' => (float) $data['exchange_rate_to_bdt'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Currencies seeded: ' . count($csv));
    }

    private function seedCities(): void
    {
        $this->command->info('Seeding cities...');
        $csvPath = database_path('seeders/csv/cities.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('cities')->insert([
                'country_id' => (int) $data['country_id'],
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'state_province' => $data['state_province'] ?? null,
                'timezone' => $data['timezone'] ?? null,
                'latitude' => !empty($data['latitude']) ? (float) $data['latitude'] : null,
                'longitude' => !empty($data['longitude']) ? (float) $data['longitude'] : null,
                'is_capital' => (bool) $data['is_capital'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Cities seeded: ' . count($csv));
    }

    private function seedAirports(): void
    {
        $this->command->info('Seeding airports...');
        $csvPath = database_path('seeders/csv/airports.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('airports')->insert([
                'city_id' => (int) $data['city_id'],
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'iata_code' => $data['iata_code'],
                'icao_code' => $data['icao_code'] ?? null,
                'latitude' => !empty($data['latitude']) ? (float) $data['latitude'] : null,
                'longitude' => !empty($data['longitude']) ? (float) $data['longitude'] : null,
                'is_international' => (bool) $data['is_international'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Airports seeded: ' . count($csv));
    }

    private function seedLanguages(): void
    {
        $this->command->info('Seeding languages...');
        $csvPath = database_path('seeders/csv/languages.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('languages')->insert([
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'code' => $data['code'],
                'native_name' => $data['native_name'] ?? null,
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Languages seeded: ' . count($csv));
    }

    private function seedLanguageTests(): void
    {
        $this->command->info('Seeding language tests...');
        $csvPath = database_path('seeders/csv/language_tests.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('language_tests')->insert([
                'language_id' => !empty($data['language_id']) ? (int) $data['language_id'] : null,
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'code' => $data['code'],
                'min_score' => !empty($data['min_score']) && is_numeric($data['min_score']) ? (float) $data['min_score'] : null,
                'max_score' => !empty($data['max_score']) && is_numeric($data['max_score']) ? (float) $data['max_score'] : null,
                'score_type' => $data['score_type'],
                'description' => $data['description'] ?? null,
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Language tests seeded: ' . count($csv));
    }

    private function seedDegrees(): void
    {
        $this->command->info('Seeding degrees...');
        $csvPath = database_path('seeders/csv/degrees.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('degrees')->insert([
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'short_name' => $data['short_name'],
                'level' => $data['level'],
                'typical_duration_years' => (int) $data['typical_duration_years'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Degrees seeded: ' . count($csv));
    }

    private function seedSkillCategories(): void
    {
        $this->command->info('Seeding skill categories...');
        $csvPath = database_path('seeders/csv/skill_categories.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('skill_categories')->insert([
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'order' => (int) $data['order'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Skill categories seeded: ' . count($csv));
    }

    private function seedSkills(): void
    {
        $this->command->info('Seeding skills...');
        $csvPath = database_path('seeders/csv/skills.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('skills')->insert([
                'skill_category_id' => !empty($data['skill_category_id']) ? (int) $data['skill_category_id'] : null,
                'name' => $data['name'],
                'name_bn' => $data['name_bn'] ?? null,
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Skills seeded: ' . count($csv));
    }

    private function seedJobCategories(): void
    {
        $this->command->info('Seeding job categories...');
        $csvPath = database_path('seeders/csv/job_categories.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        // First pass: Insert root categories (parent_id is empty)
        $rootCategories = [];
        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            if (empty($data['parent_id'])) {
                $id = DB::table('job_categories')->insertGetId([
                    'parent_id' => null,
                    'name' => $data['name'],
                    'name_bn' => $data['name_bn'] ?? null,
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? null,
                    'order' => (int) $data['order'],
                    'is_active' => (bool) $data['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $rootCategories[$data['name']] = $id;
            }
        }

        // Second pass: Insert child categories with proper parent_id
        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            if (!empty($data['parent_id'])) {
                // Find parent by matching row number or name lookup
                $parentId = is_numeric($data['parent_id']) ? (int) $data['parent_id'] : null;
                
                DB::table('job_categories')->insert([
                    'parent_id' => $parentId,
                    'name' => $data['name'],
                    'name_bn' => $data['name_bn'] ?? null,
                    'slug' => $data['slug'],
                    'description' => $data['description'] ?? null,
                    'order' => (int) $data['order'],
                    'is_active' => (bool) $data['is_active'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✓ Job categories seeded: ' . count($csv));
    }

    private function seedServiceCategories(): void
    {
        $this->command->info('Seeding service categories...');
        $csvPath = database_path('seeders/csv/service_categories.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('service_categories')->insert([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'icon' => $data['icon'] ?? null,
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? '#3B82F6',
                'sort_order' => (int) $data['sort_order'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Service categories seeded: ' . count($csv));
    }

    private function seedBlogCategories(): void
    {
        $this->command->info('Seeding blog categories...');
        $csvPath = database_path('seeders/csv/blog_categories.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('blog_categories')->insert([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'color' => $data['color'] ?? '#3B82F6',
                'order' => (int) $data['order'],
                'is_active' => (bool) $data['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Blog categories seeded: ' . count($csv));
    }

    private function seedBlogTags(): void
    {
        $this->command->info('Seeding blog tags...');
        $csvPath = database_path('seeders/csv/blog_tags.csv');
        
        if (!File::exists($csvPath)) {
            $this->command->error("File not found: {$csvPath}");
            return;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $headers = array_shift($csv);

        foreach ($csv as $row) {
            $data = array_combine($headers, $row);
            DB::table('blog_tags')->insert([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✓ Blog tags seeded: ' . count($csv));
    }
}
