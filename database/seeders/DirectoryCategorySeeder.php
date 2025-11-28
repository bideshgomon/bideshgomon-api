<?php

namespace Database\Seeders;

use App\Models\DirectoryCategory;
use Illuminate\Database\Seeder;

class DirectoryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Embassy',
                'name_bn' => 'দূতাবাস',
                'slug' => 'embassy',
                'description' => 'Foreign embassies and consulates in Bangladesh',
                'icon' => 'building-library',
                'color' => 'blue',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Airline',
                'name_bn' => 'এয়ারলাইন',
                'slug' => 'airline',
                'description' => 'International airlines operating in Bangladesh',
                'icon' => 'airplane',
                'color' => 'indigo',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Training Center',
                'name_bn' => 'প্রশিক্ষণ কেন্দ্র',
                'slug' => 'training-center',
                'description' => 'Language and skill training centers for overseas workers',
                'icon' => 'academic-cap',
                'color' => 'green',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Medical Center',
                'name_bn' => 'চিকিৎসা কেন্দ্র',
                'slug' => 'medical-center',
                'description' => 'Medical examination centers for overseas workers',
                'icon' => 'heart',
                'color' => 'red',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'University',
                'name_bn' => 'বিশ্ববিদ্যালয়',
                'slug' => 'university',
                'description' => 'Universities and higher education institutions abroad',
                'icon' => 'building-columns',
                'color' => 'purple',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Recruiting Agency',
                'name_bn' => 'নিয়োগ সংস্থা',
                'slug' => 'recruiting-agency',
                'description' => 'Licensed overseas recruitment agencies',
                'icon' => 'users',
                'color' => 'yellow',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Bank & Exchange',
                'name_bn' => 'ব্যাংক ও মুদ্রা বিনিময়',
                'slug' => 'bank-exchange',
                'description' => 'Banks and money exchange services',
                'icon' => 'banknotes',
                'color' => 'green',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Legal Services',
                'name_bn' => 'আইনি সেবা',
                'slug' => 'legal-services',
                'description' => 'Legal consultants and immigration lawyers',
                'icon' => 'scale',
                'color' => 'gray',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            DirectoryCategory::create($category);
        }
    }
}
