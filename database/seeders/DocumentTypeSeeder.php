<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Passport',
            'National ID (NID)',
            'Resume / CV',
            'IELTS Certificate',
            'TOFEL Certificate',
            'Visa (Stamp/Sticker)',
            'Academic Transcript',
            'Police Clearance',
        ];

        foreach ($types as $type) {
            DocumentType::firstOrCreate(
                ['slug' => Str::slug($type)], // Ensure unique slug
                [
                    'name' => $type,
                    'is_active' => true, // Optional, if your table has this column
                ]
            );
        }
    }
}
