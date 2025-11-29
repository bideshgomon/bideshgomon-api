<?php

namespace Database\Seeders;

use App\Models\RelationshipType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RelationshipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds - Bangladesh context
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('relationship_types')->truncate();
        Schema::enableForeignKeyConstraints();

        $relationshipTypes = [
            // Immediate Family
            ['name' => 'Spouse', 'name_bn' => 'স্বামী/স্ত্রী', 'slug' => 'spouse', 'description' => 'Husband or Wife', 'category' => 'family', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Father', 'name_bn' => 'পিতা', 'slug' => 'father', 'description' => 'Biological or adoptive father', 'category' => 'family', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Mother', 'name_bn' => 'মাতা', 'slug' => 'mother', 'description' => 'Biological or adoptive mother', 'category' => 'family', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Son', 'name_bn' => 'পুত্র', 'slug' => 'son', 'description' => 'Male child', 'category' => 'family', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Daughter', 'name_bn' => 'কন্যা', 'slug' => 'daughter', 'description' => 'Female child', 'category' => 'family', 'is_active' => true, 'sort_order' => 5],
            ['name' => 'Brother', 'name_bn' => 'ভাই', 'slug' => 'brother', 'description' => 'Male sibling', 'category' => 'family', 'is_active' => true, 'sort_order' => 10],
            ['name' => 'Sister', 'name_bn' => 'বোন', 'slug' => 'sister', 'description' => 'Female sibling', 'category' => 'family', 'is_active' => true, 'sort_order' => 11],
            // Extended Family
            ['name' => 'Grandfather', 'name_bn' => 'দাদা/নানা', 'slug' => 'grandfather', 'description' => 'Father or mother\'s father', 'category' => 'family', 'is_active' => true, 'sort_order' => 20],
            ['name' => 'Grandmother', 'name_bn' => 'দাদি/নানি', 'slug' => 'grandmother', 'description' => 'Father or mother\'s mother', 'category' => 'family', 'is_active' => true, 'sort_order' => 21],
            ['name' => 'Uncle', 'name_bn' => 'চাচা/মামা', 'slug' => 'uncle', 'description' => 'Father or mother\'s brother', 'category' => 'family', 'is_active' => true, 'sort_order' => 24],
            ['name' => 'Aunt', 'name_bn' => 'চাচি/মামি', 'slug' => 'aunt', 'description' => 'Father or mother\'s sister', 'category' => 'family', 'is_active' => true, 'sort_order' => 25],
            // In-laws
            ['name' => 'Father-in-law', 'name_bn' => 'শ্বশুর', 'slug' => 'father-in-law', 'description' => 'Spouse\'s father', 'category' => 'family', 'is_active' => true, 'sort_order' => 30],
            ['name' => 'Mother-in-law', 'name_bn' => 'শাশুড়ি', 'slug' => 'mother-in-law', 'description' => 'Spouse\'s mother', 'category' => 'family', 'is_active' => true, 'sort_order' => 31],
            // Professional
            ['name' => 'Employer', 'name_bn' => 'নিয়োগকর্তা', 'slug' => 'employer', 'description' => 'Current or previous employer', 'category' => 'professional', 'is_active' => true, 'sort_order' => 40],
            ['name' => 'Colleague', 'name_bn' => 'সহকর্মী', 'slug' => 'colleague', 'description' => 'Work colleague', 'category' => 'professional', 'is_active' => true, 'sort_order' => 41],
            // Other
            ['name' => 'Friend', 'name_bn' => 'বন্ধু', 'slug' => 'friend', 'description' => 'Personal friend', 'category' => 'other', 'is_active' => true, 'sort_order' => 50],
            ['name' => 'Guardian', 'name_bn' => 'অভিভাবক', 'slug' => 'guardian', 'description' => 'Legal guardian', 'category' => 'other', 'is_active' => true, 'sort_order' => 51],
            ['name' => 'Sponsor', 'name_bn' => 'স্পন্সর', 'slug' => 'sponsor', 'description' => 'Financial or visa sponsor', 'category' => 'other', 'is_active' => true, 'sort_order' => 52],
        ];

        foreach ($relationshipTypes as $type) {
            RelationshipType::create($type);
        }

        $this->command->info('Relationship types seeded: ' . count($relationshipTypes) . ' entries');
    }
}
