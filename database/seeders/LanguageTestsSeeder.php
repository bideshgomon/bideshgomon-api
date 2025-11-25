<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageTestsSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/csv/language_tests.csv');
        $rows = array_map('str_getcsv', file($csvFile));
        $header = array_shift($rows);
        
        foreach ($rows as $row) {
            DB::table('language_tests')->insert([
                'language_id' => (int)$row[0],
                'name' => $row[1],
                'name_bn' => $row[2],
                'code' => $row[3],
                'min_score' => $row[4],
                'max_score' => $row[5],
                'score_type' => $row[6],
                'description' => $row[7],
                'is_active' => (int)$row[8],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        echo "Seeded " . count($rows) . " language tests\n";
    }
}
