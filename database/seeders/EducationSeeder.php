<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserEducation;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $bangladeshUniversities = [
            'Dhaka University',
            'Bangladesh University of Engineering and Technology (BUET)',
            'Jahangirnagar University',
            'Rajshahi University',
            'Chittagong University',
            'Islamic University, Bangladesh',
            'Shahjalal University of Science and Technology',
            'North South University',
            'BRAC University',
            'Independent University, Bangladesh (IUB)',
            'East West University',
            'American International University-Bangladesh (AIUB)',
            'Daffodil International University',
            'United International University',
            'University of Asia Pacific',
        ];

        $degrees = [
            'Bachelor of Science (B.Sc.)',
            'Bachelor of Arts (B.A.)',
            'Bachelor of Business Administration (BBA)',
            'Bachelor of Computer Science (B.CS)',
            'Master of Science (M.Sc.)',
            'Master of Arts (M.A.)',
            'Master of Business Administration (MBA)',
            'Bachelor of Engineering (B.Eng)',
            'Master of Engineering (M.Eng)',
            'Doctor of Philosophy (Ph.D.)',
        ];

        $fields = [
            'Computer Science',
            'Business Administration',
            'Electrical Engineering',
            'Civil Engineering',
            'Economics',
            'English Literature',
            'Mathematics',
            'Physics',
            'Chemistry',
            'Accounting',
            'Finance',
            'Marketing',
            'Management',
            'Software Engineering',
            'Data Science',
            'Information Technology',
        ];

        $bangladeshCities = [
            'Dhaka',
            'Chittagong',
            'Sylhet',
            'Rajshahi',
            'Khulna',
            'Barisal',
            'Rangpur',
            'Mymensingh',
        ];

        $results = ['First Class', 'Second Class', 'Third Class', 'Pass'];
        $grades = ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'];

        foreach ($users as $user) {
            // Generate 2-5 education records per user
            $recordCount = rand(2, 5);

            for ($i = 0; $i < $recordCount; $i++) {
                $startYear = rand(2005, 2020);
                $duration = rand(2, 5);
                $endYear = $startYear + $duration;
                $isCurrent = ($i === 0 && rand(0, 3) === 0); // 25% chance latest is current

                UserEducation::create([
                    'user_id' => $user->id,
                    'institution_name' => $bangladeshUniversities[array_rand($bangladeshUniversities)],
                    'degree' => $degrees[array_rand($degrees)],
                    'field_of_study' => $fields[array_rand($fields)],
                    'start_date' => Carbon::create($startYear, rand(1, 12), 1),
                    'end_date' => $isCurrent ? null : Carbon::create($endYear, rand(1, 12), 1),
                    'is_completed' => !$isCurrent,
                    'city' => $bangladeshCities[array_rand($bangladeshCities)],
                    'country' => 'BD',
                    'gpa_or_grade' => $isCurrent ? null : rand(300, 400) / 100,
                    'degree_certificate_path' => rand(0, 1) ? 'education/certificates/sample_certificate_' . $user->id . '_' . $i . '.pdf' : null,
                    'transcript_path' => rand(0, 1) ? 'education/transcripts/sample_transcript_' . $user->id . '_' . $i . '.pdf' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Education records created for ' . $users->count() . ' users');
    }

    private function generateDescription(string $field): string
    {
        $descriptions = [
            'Completed comprehensive coursework in ' . $field . ' with focus on practical applications',
            'Achieved excellence in ' . $field . ' through rigorous academic training',
            'Specialized in ' . $field . ' with additional certifications',
            'Gained in-depth knowledge of ' . $field . ' fundamentals and advanced concepts',
            'Developed strong foundation in ' . $field . ' through project-based learning',
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
