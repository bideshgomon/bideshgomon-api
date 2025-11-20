<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\LanguageTest;
use Illuminate\Database\Seeder;

class LanguageTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get English language ID for English proficiency tests
        $english = Language::where('code', 'en')->first();
        $englishId = $english ? $english->id : null;

        $tests = [
            // English proficiency tests
            [
                'language_id' => $englishId,
                'name' => 'IELTS',
                'name_bn' => 'আইইএলটিএস',
                'code' => 'ielts',
                'min_score' => 0,
                'max_score' => 9.0,
                'score_type' => 'band',
                'description' => 'International English Language Testing System',
                'is_active' => true,
            ],
            [
                'language_id' => $englishId,
                'name' => 'TOEFL iBT',
                'name_bn' => 'টোফেল',
                'code' => 'toefl',
                'min_score' => 0,
                'max_score' => 120,
                'score_type' => 'score',
                'description' => 'Test of English as a Foreign Language (Internet-Based Test)',
                'is_active' => true,
            ],
            [
                'language_id' => $englishId,
                'name' => 'PTE Academic',
                'name_bn' => 'পিটিই',
                'code' => 'pte',
                'min_score' => 10,
                'max_score' => 90,
                'score_type' => 'score',
                'description' => 'Pearson Test of English Academic',
                'is_active' => true,
            ],
            [
                'language_id' => $englishId,
                'name' => 'Duolingo English Test',
                'name_bn' => 'ডুয়োলিঙ্গো',
                'code' => 'duolingo',
                'min_score' => 10,
                'max_score' => 160,
                'score_type' => 'score',
                'description' => 'Duolingo English Test',
                'is_active' => true,
            ],
            [
                'language_id' => $englishId,
                'name' => 'Cambridge English',
                'name_bn' => 'ক্যামব্রিজ ইংলিশ',
                'code' => 'cambridge',
                'min_score' => 0,
                'max_score' => 230,
                'score_type' => 'score',
                'description' => 'Cambridge English Qualifications',
                'is_active' => true,
            ],
            
            // Japanese proficiency tests
            [
                'language_id' => null, // Will be set if Japanese language exists
                'name' => 'JLPT N1',
                'name_bn' => 'জেএলপিটি এন১',
                'code' => 'jlpt_n1',
                'min_score' => 0,
                'max_score' => 180,
                'score_type' => 'score',
                'description' => 'Japanese Language Proficiency Test - Level N1 (Advanced)',
                'is_active' => true,
            ],
            [
                'language_id' => null,
                'name' => 'JLPT N2',
                'name_bn' => 'জেএলপিটি এন২',
                'code' => 'jlpt_n2',
                'min_score' => 0,
                'max_score' => 180,
                'score_type' => 'score',
                'description' => 'Japanese Language Proficiency Test - Level N2 (Upper Intermediate)',
                'is_active' => true,
            ],
            [
                'language_id' => null,
                'name' => 'JLPT N3',
                'name_bn' => 'জেএলপিটি এন৩',
                'code' => 'jlpt_n3',
                'min_score' => 0,
                'max_score' => 180,
                'score_type' => 'score',
                'description' => 'Japanese Language Proficiency Test - Level N3 (Intermediate)',
                'is_active' => true,
            ],
            [
                'language_id' => null,
                'name' => 'JLPT N4',
                'name_bn' => 'জেএলপিটি এন৪',
                'code' => 'jlpt_n4',
                'min_score' => 0,
                'max_score' => 180,
                'score_type' => 'score',
                'description' => 'Japanese Language Proficiency Test - Level N4 (Elementary)',
                'is_active' => true,
            ],
            [
                'language_id' => null,
                'name' => 'JLPT N5',
                'name_bn' => 'জেএলপিটি এন৫',
                'code' => 'jlpt_n5',
                'min_score' => 0,
                'max_score' => 180,
                'score_type' => 'score',
                'description' => 'Japanese Language Proficiency Test - Level N5 (Beginner)',
                'is_active' => true,
            ],
            
            // German proficiency tests
            [
                'language_id' => null,
                'name' => 'TestDaF',
                'name_bn' => 'টেস্টডাফ',
                'code' => 'testdaf',
                'min_score' => 3,
                'max_score' => 5,
                'score_type' => 'level',
                'description' => 'Test Deutsch als Fremdsprache (Test of German as a Foreign Language)',
                'is_active' => true,
            ],
            [
                'language_id' => null,
                'name' => 'Goethe-Zertifikat',
                'name_bn' => 'গোয়েথে সার্টিফিকেট',
                'code' => 'goethe',
                'min_score' => 0,
                'max_score' => 100,
                'score_type' => 'score',
                'description' => 'Goethe-Institut German Language Certificate',
                'is_active' => true,
            ],
            
            // French proficiency tests
            [
                'language_id' => null,
                'name' => 'DELF',
                'name_bn' => 'ডেলফ',
                'code' => 'delf',
                'min_score' => 0,
                'max_score' => 100,
                'score_type' => 'score',
                'description' => 'Diplôme d\'Études en Langue Française',
                'is_active' => true,
            ],
            [
                'language_id' => null,
                'name' => 'DALF',
                'name_bn' => 'ডালফ',
                'code' => 'dalf',
                'min_score' => 0,
                'max_score' => 100,
                'score_type' => 'score',
                'description' => 'Diplôme Approfondi de Langue Française',
                'is_active' => true,
            ],
            [
                'language_id' => null,
                'name' => 'TEF',
                'name_bn' => 'টিইএফ',
                'code' => 'tef',
                'min_score' => 0,
                'max_score' => 699,
                'score_type' => 'score',
                'description' => 'Test d\'Évaluation de Français',
                'is_active' => true,
            ],
            
            // Spanish proficiency tests
            [
                'language_id' => null,
                'name' => 'DELE',
                'name_bn' => 'ডিইএলই',
                'code' => 'dele',
                'min_score' => 0,
                'max_score' => 100,
                'score_type' => 'score',
                'description' => 'Diplomas de Español como Lengua Extranjera',
                'is_active' => true,
            ],
            
            // Chinese proficiency tests
            [
                'language_id' => null,
                'name' => 'HSK',
                'name_bn' => 'এইচএসকে',
                'code' => 'hsk',
                'min_score' => 1,
                'max_score' => 6,
                'score_type' => 'level',
                'description' => 'Hanyu Shuiping Kaoshi (Chinese Proficiency Test)',
                'is_active' => true,
            ],
            
            // Korean proficiency tests
            [
                'language_id' => null,
                'name' => 'TOPIK',
                'name_bn' => 'টোপিক',
                'code' => 'topik',
                'min_score' => 1,
                'max_score' => 6,
                'score_type' => 'level',
                'description' => 'Test of Proficiency in Korean',
                'is_active' => true,
            ],
            
            // Other/General
            [
                'language_id' => null,
                'name' => 'Other',
                'name_bn' => 'অন্যান্য',
                'code' => 'other',
                'min_score' => null,
                'max_score' => null,
                'score_type' => 'score',
                'description' => 'Other language proficiency test',
                'is_active' => true,
            ],
        ];

        foreach ($tests as $test) {
            LanguageTest::updateOrCreate(
                ['code' => $test['code']],
                $test
            );
        }

        $this->command->info('Language tests seeded successfully!');
    }
}
