<?php

namespace Database\Seeders;

use App\Models\InstitutionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstitutionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds - Bangladesh education context
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('institution_types')->truncate();
        Schema::enableForeignKeyConstraints();

        $institutions = [
            // Academic - Higher Education
            ['name' => 'Public University', 'name_bn' => 'পাবলিক বিশ্ববিদ্যালয়', 'slug' => 'public-university', 'description' => 'Government-funded university', 'category' => 'academic', 'level' => 'higher', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Private University', 'name_bn' => 'প্রাইভেট বিশ্ববিদ্যালয়', 'slug' => 'private-university', 'description' => 'Private university', 'category' => 'academic', 'level' => 'higher', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Medical College', 'name_bn' => 'মেডিকেল কলেজ', 'slug' => 'medical-college', 'description' => 'Medical education institution', 'category' => 'academic', 'level' => 'higher', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Engineering College', 'name_bn' => 'প্রকৌশল কলেজ', 'slug' => 'engineering-college', 'description' => 'Engineering education', 'category' => 'academic', 'level' => 'higher', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'National University College', 'name_bn' => 'জাতীয় বিশ্ববিদ্যালয় কলেজ', 'slug' => 'national-university-college', 'description' => 'College affiliated with National University', 'category' => 'academic', 'level' => 'higher', 'is_active' => true, 'sort_order' => 5],
            
            // Academic - Secondary
            ['name' => 'School', 'name_bn' => 'স্কুল', 'slug' => 'school', 'description' => 'Primary and secondary school', 'category' => 'academic', 'level' => 'secondary', 'is_active' => true, 'sort_order' => 10],
            ['name' => 'College', 'name_bn' => 'কলেজ', 'slug' => 'college', 'description' => 'Higher secondary college', 'category' => 'academic', 'level' => 'secondary', 'is_active' => true, 'sort_order' => 11],
            ['name' => 'Madrasa', 'name_bn' => 'মাদ্রাসা', 'slug' => 'madrasa', 'description' => 'Islamic education institution', 'category' => 'academic', 'level' => 'secondary', 'is_active' => true, 'sort_order' => 12],
            
            // Vocational & Technical
            ['name' => 'Polytechnic Institute', 'name_bn' => 'পলিটেকনিক ইনস্টিটিউট', 'slug' => 'polytechnic-institute', 'description' => 'Technical education institute', 'category' => 'vocational', 'level' => 'vocational', 'is_active' => true, 'sort_order' => 20],
            ['name' => 'Technical School', 'name_bn' => 'টেকনিক্যাল স্কুল', 'slug' => 'technical-school', 'description' => 'Technical and vocational school', 'category' => 'vocational', 'level' => 'vocational', 'is_active' => true, 'sort_order' => 21],
            ['name' => 'Vocational Training Institute', 'name_bn' => 'কারিগরি প্রশিক্ষণ ইনস্টিটিউট', 'slug' => 'vocational-training-institute', 'description' => 'Skills training institute', 'category' => 'vocational', 'level' => 'vocational', 'is_active' => true, 'sort_order' => 22],
            
            // Language & Professional Training
            ['name' => 'Language Institute', 'name_bn' => 'ভাষা ইনস্টিটিউট', 'slug' => 'language-institute', 'description' => 'Foreign language training center', 'category' => 'language', 'level' => 'professional', 'is_active' => true, 'sort_order' => 30],
            ['name' => 'Professional Training Center', 'name_bn' => 'পেশাদার প্রশিক্ষণ কেন্দ্র', 'slug' => 'professional-training-center', 'description' => 'Professional skills development', 'category' => 'professional', 'level' => 'professional', 'is_active' => true, 'sort_order' => 31],
            ['name' => 'Computer Training Institute', 'name_bn' => 'কম্পিউটার প্রশিক্ষণ ইনস্টিটিউট', 'slug' => 'computer-training-institute', 'description' => 'IT and computer skills training', 'category' => 'professional', 'level' => 'professional', 'is_active' => true, 'sort_order' => 32],
            ['name' => 'Nursing Institute', 'name_bn' => 'নার্সিং ইনস্টিটিউট', 'slug' => 'nursing-institute', 'description' => 'Nursing education and training', 'category' => 'professional', 'level' => 'professional', 'is_active' => true, 'sort_order' => 33],
            
            // Research & Specialized
            ['name' => 'Research Institute', 'name_bn' => 'গবেষণা ইনস্টিটিউট', 'slug' => 'research-institute', 'description' => 'Academic research institution', 'category' => 'academic', 'level' => 'higher', 'is_active' => true, 'sort_order' => 40],
            ['name' => 'Training Academy', 'name_bn' => 'প্রশিক্ষণ একাডেমি', 'slug' => 'training-academy', 'description' => 'Specialized training academy', 'category' => 'professional', 'level' => 'professional', 'is_active' => true, 'sort_order' => 41],
        ];

        foreach ($institutions as $institution) {
            InstitutionType::create($institution);
        }

        $this->command->info('Institution types seeded: ' . count($institutions) . ' entries');
    }
}
