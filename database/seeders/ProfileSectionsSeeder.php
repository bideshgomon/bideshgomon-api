<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FamilyMember;
use App\Models\UserLanguage;
use App\Models\UserSecurityInformation;
use Illuminate\Database\Seeder;

class ProfileSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Delete existing
        $user->familyMembers()->delete();
        $user->languages()->delete();
        $user->securityInformation()->delete();

        // Family Members
        FamilyMember::create([
            'user_id' => $user->id,
            'relationship' => 'spouse',
            'full_name' => 'Sarah Johnson',
            'date_of_birth' => '1992-05-15',
            'nationality' => 'Bangladesh',
        ]);

        FamilyMember::create([
            'user_id' => $user->id,
            'relationship' => 'child',
            'full_name' => 'Michael Johnson',
            'date_of_birth' => '2018-08-20',
        ]);

        // Languages
        UserLanguage::create([
            'user_id' => $user->id,
            'language' => 'Bengali',
            'proficiency' => 'native',
            'is_native' => true,
        ]);

        UserLanguage::create([
            'user_id' => $user->id,
            'language' => 'English',
            'proficiency' => 'advanced',
            'test_taken' => 'ielts',
            'test_score' => '7.5',
        ]);

        // Security
        UserSecurityInformation::create([
            'user_id' => $user->id,
            'has_criminal_record' => false,
            'has_police_clearance' => true,
        ]);

        $this->command->info(' Seeded: 2 family members, 2 languages, 1 security record');
    }
}

