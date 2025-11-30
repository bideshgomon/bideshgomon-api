<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SkillsQuickSeeder extends Seeder
{
    /**
     * Run the database seeds - Quick essential skills for profile section
     */
    public function run(): void
    {
        $skills = [
            // Technical Skills
            ['name' => 'PHP', 'category' => 'Programming Languages', 'is_active' => true],
            ['name' => 'JavaScript', 'category' => 'Programming Languages', 'is_active' => true],
            ['name' => 'Python', 'category' => 'Programming Languages', 'is_active' => true],
            ['name' => 'Java', 'category' => 'Programming Languages', 'is_active' => true],
            ['name' => 'C-Sharp', 'category' => 'Programming Languages', 'is_active' => true],
            ['name' => 'C-Plus-Plus', 'category' => 'Programming Languages', 'is_active' => true],
            
            // Web Development
            ['name' => 'HTML/CSS', 'category' => 'Web Development', 'is_active' => true],
            ['name' => 'Laravel', 'category' => 'Web Development', 'is_active' => true],
            ['name' => 'Vue.js', 'category' => 'Web Development', 'is_active' => true],
            ['name' => 'React', 'category' => 'Web Development', 'is_active' => true],
            ['name' => 'Node.js', 'category' => 'Web Development', 'is_active' => true],
            ['name' => 'WordPress', 'category' => 'Web Development', 'is_active' => true],
            
            // Database
            ['name' => 'MySQL', 'category' => 'Database', 'is_active' => true],
            ['name' => 'PostgreSQL', 'category' => 'Database', 'is_active' => true],
            ['name' => 'MongoDB', 'category' => 'Database', 'is_active' => true],
            
            // Design
            ['name' => 'Adobe Photoshop', 'category' => 'Design', 'is_active' => true],
            ['name' => 'Adobe Illustrator', 'category' => 'Design', 'is_active' => true],
            ['name' => 'Figma', 'category' => 'Design', 'is_active' => true],
            ['name' => 'Adobe XD', 'category' => 'Design', 'is_active' => true],
            
            // Business & Management
            ['name' => 'Project Management', 'category' => 'Business', 'is_active' => true],
            ['name' => 'Microsoft Excel', 'category' => 'Business', 'is_active' => true],
            ['name' => 'Microsoft Word', 'category' => 'Business', 'is_active' => true],
            ['name' => 'Microsoft PowerPoint', 'category' => 'Business', 'is_active' => true],
            ['name' => 'Data Analysis', 'category' => 'Business', 'is_active' => true],
            ['name' => 'Financial Analysis', 'category' => 'Business', 'is_active' => true],
            
            // Marketing & Sales
            ['name' => 'Digital Marketing', 'category' => 'Marketing', 'is_active' => true],
            ['name' => 'SEO', 'category' => 'Marketing', 'is_active' => true],
            ['name' => 'Social Media Marketing', 'category' => 'Marketing', 'is_active' => true],
            ['name' => 'Content Writing', 'category' => 'Marketing', 'is_active' => true],
            ['name' => 'Sales', 'category' => 'Marketing', 'is_active' => true],
            
            // Languages
            ['name' => 'English Communication', 'category' => 'Communication', 'is_active' => true],
            ['name' => 'Public Speaking', 'category' => 'Communication', 'is_active' => true],
            ['name' => 'Business Writing', 'category' => 'Communication', 'is_active' => true],
            
            // IT Support
            ['name' => 'Technical Support', 'category' => 'IT Support', 'is_active' => true],
            ['name' => 'Network Administration', 'category' => 'IT Support', 'is_active' => true],
            ['name' => 'System Administration', 'category' => 'IT Support', 'is_active' => true],
            
            // Manufacturing & Engineering
            ['name' => 'AutoCAD', 'category' => 'Engineering', 'is_active' => true],
            ['name' => 'Electrical Engineering', 'category' => 'Engineering', 'is_active' => true],
            ['name' => 'Mechanical Engineering', 'category' => 'Engineering', 'is_active' => true],
            ['name' => 'Quality Control', 'category' => 'Engineering', 'is_active' => true],
            
            // Healthcare
            ['name' => 'Patient Care', 'category' => 'Healthcare', 'is_active' => true],
            ['name' => 'Medical Records', 'category' => 'Healthcare', 'is_active' => true],
            ['name' => 'Nursing', 'category' => 'Healthcare', 'is_active' => true],
            
            // Hospitality
            ['name' => 'Customer Service', 'category' => 'Hospitality', 'is_active' => true],
            ['name' => 'Food Service', 'category' => 'Hospitality', 'is_active' => true],
            ['name' => 'Hotel Management', 'category' => 'Hospitality', 'is_active' => true],
            
            // Construction
            ['name' => 'Carpentry', 'category' => 'Construction', 'is_active' => true],
            ['name' => 'Plumbing', 'category' => 'Construction', 'is_active' => true],
            ['name' => 'Electrical Work', 'category' => 'Construction', 'is_active' => true],
            ['name' => 'Welding', 'category' => 'Construction', 'is_active' => true],
        ];

        foreach ($skills as $skill) {
            DB::table('skills')->insert([
                'name' => $skill['name'],
                'slug' => Str::slug($skill['name']),
                'category' => $skill['category'],
                'is_active' => $skill['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Seeded ' . count($skills) . ' essential skills across multiple categories.');
    }
}
