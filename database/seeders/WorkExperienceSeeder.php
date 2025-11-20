<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserWorkExperience;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WorkExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $bangladeshCompanies = [
            'Grameenphone Ltd.',
            'Banglalink Digital Communications',
            'Robi Axiata Limited',
            'BRAC Bank Limited',
            'Dutch-Bangla Bank',
            'Beximco Pharmaceuticals Ltd.',
            'Square Pharmaceuticals Ltd.',
            'Walton Group',
            'ACI Limited',
            'Bashundhara Group',
            'Pran-RFL Group',
            'Unilever Bangladesh',
            'British American Tobacco Bangladesh',
            'Nestlé Bangladesh',
            'bKash Limited',
            'Nagad',
            'Pathao',
            'Chaldal',
            'Brain Station 23',
            'SSL Wireless',
        ];

        $positions = [
            'Software Engineer',
            'Senior Software Engineer',
            'Business Analyst',
            'Project Manager',
            'Marketing Manager',
            'Sales Executive',
            'HR Manager',
            'Accountant',
            'Financial Analyst',
            'Operations Manager',
            'Product Manager',
            'Data Analyst',
            'Quality Assurance Engineer',
            'DevOps Engineer',
            'UI/UX Designer',
        ];

        $employmentTypes = ['Full-time', 'Part-time', 'Contract', 'Internship'];
        $industries = [
            'Telecommunications',
            'Banking & Finance',
            'Pharmaceuticals',
            'Technology',
            'Manufacturing',
            'Retail',
            'E-commerce',
            'Consulting',
        ];

        foreach ($users as $user) {
            // Generate 1-4 work experience records per user
            $recordCount = rand(1, 4);

            for ($i = 0; $i < $recordCount; $i++) {
                $startYear = rand(2015, 2023);
                $duration = rand(6, 36); // 6 months to 3 years
                $endDate = Carbon::create($startYear)->addMonths($duration);
                $isCurrent = ($i === 0 && rand(0, 2) === 0); // 33% chance latest is current

                $position = $positions[array_rand($positions)];
                $responsibilities = $this->generateResponsibilities($position);

                UserWorkExperience::create([
                    'user_id' => $user->id,
                    'company_name' => $bangladeshCompanies[array_rand($bangladeshCompanies)],
                    'position' => $position,
                    'employment_type' => strtolower(str_replace('-', '_', $employmentTypes[array_rand($employmentTypes)])),
                    'start_date' => Carbon::create($startYear, rand(1, 12), 1),
                    'end_date' => $isCurrent ? null : $endDate,
                    'is_current_employment' => $isCurrent,
                    'city' => $this->getRandomBDCity(),
                    'country' => 'BD',
                    'job_description' => $this->generateDescription($position),
                    'salary' => rand(30000, 150000),
                    'currency' => 'BDT',
                    'salary_period' => 'monthly',
                    'supervisor_name' => $this->generateBangladeshiName(),
                    'supervisor_phone' => '+880171' . rand(1000000, 9999999),
                    'supervisor_email' => 'supervisor' . rand(1000, 9999) . '@company.com',
                    'employment_letter_path' => rand(0, 1) ? 'work-experience/letters/sample_letter_' . $user->id . '_' . $i . '.pdf' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ Work experience records created for ' . $users->count() . ' users');
    }

    private function generateResponsibilities(string $position): array
    {
        $commonResponsibilities = [
            'Led team meetings and coordinated project deliverables',
            'Collaborated with cross-functional teams to achieve business objectives',
            'Prepared detailed reports and presentations for management review',
            'Maintained documentation and ensured compliance with company policies',
        ];

        $specificResponsibilities = match (true) {
            str_contains($position, 'Software') => [
                'Developed and maintained web applications using modern frameworks',
                'Conducted code reviews and implemented best practices',
                'Optimized application performance and resolved technical issues',
            ],
            str_contains($position, 'Manager') => [
                'Managed team of 5-10 professionals',
                'Developed and implemented strategic plans',
                'Monitored KPIs and drove continuous improvement',
            ],
            str_contains($position, 'Analyst') => [
                'Analyzed data trends and prepared actionable insights',
                'Created dashboards and visualization reports',
                'Recommended process improvements based on data analysis',
            ],
            default => [
                'Executed day-to-day operational tasks efficiently',
                'Contributed to team goals and company growth',
            ]
        };

        return array_merge(
            array_slice($commonResponsibilities, 0, 2),
            $specificResponsibilities
        );
    }

    private function generateDescription(string $position): string
    {
        return "Responsible for overseeing {$position} duties including strategic planning, execution, and continuous improvement of processes. Worked closely with stakeholders to deliver high-quality results.";
    }

    private function getRandomBDCity(): string
    {
        $cities = ['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barisal', 'Rangpur', 'Mymensingh'];
        return $cities[array_rand($cities)];
    }

    private function getJobLevel(string $position): string
    {
        if (str_contains($position, 'Senior') || str_contains($position, 'Manager')) {
            return 'Senior';
        }
        if (str_contains($position, 'Lead') || str_contains($position, 'Principal')) {
            return 'Lead';
        }
        return 'Mid-Level';
    }

    private function generateBangladeshiName(): string
    {
        $firstNames = ['Mohammed', 'Abdul', 'Md.', 'Fatema', 'Ayesha', 'Shahana', 'Tanvir', 'Rahim', 'Karim', 'Nasrin'];
        $lastNames = ['Rahman', 'Hossain', 'Ahmed', 'Ali', 'Khan', 'Chowdhury', 'Islam', 'Begum', 'Akter', 'Sultana'];
        
        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }
}
