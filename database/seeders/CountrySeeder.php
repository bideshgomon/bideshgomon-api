<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds all countries from CSV with nationality information.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('PRAGMA foreign_keys = OFF;');
        
        // Clear existing countries
        DB::table('countries')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON;');

        // Read CSV file
        $csvFile = database_path('seeders/csv/countries_complete.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("CSV file not found: {$csvFile}");
            return;
        }

        $rows = array_map('str_getcsv', file($csvFile));
        $header = array_shift($rows); // Remove header row

        $insertedCount = 0;

        foreach ($rows as $row) {
            if (count($row) < 10) continue; // Skip incomplete rows

            DB::table('countries')->insert([
                'name' => $row[0],
                'name_bn' => $row[1],
                'iso_code_2' => $row[2],
                'iso_code_3' => $row[3],
                'phone_code' => $row[4],
                'currency_code' => $row[5],
                'flag_emoji' => $row[6],
                'region' => $row[7],
                'nationality' => $row[8],
                'is_active' => (bool)$row[9],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $insertedCount++;
        }

        $this->command->info("Seeded {$insertedCount} countries with nationality data.");
    }
}
