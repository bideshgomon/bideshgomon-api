<?php

namespace Database\Seeders;

use App\Models\CvTemplate;
use Illuminate\Database\Seeder;

class CvTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Modern Professional',
                'slug' => 'modern-professional',
                'description' => 'Clean and modern design perfect for IT professionals, developers, and tech industry roles. Features a sleek layout with emphasis on skills and experience.',
                'category' => 'modern',
                'is_premium' => false,
                'price' => 0,
                'color_scheme' => [
                    'primary' => '#059669',
                    'secondary' => '#10b981',
                    'accent' => '#064e3b',
                    'text' => '#1f2937',
                ],
                'sections' => ['personal_info', 'summary', 'experience', 'education', 'skills', 'languages'],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Classic ATS Friendly',
                'slug' => 'classic-ats-friendly',
                'description' => 'Optimized for Applicant Tracking Systems (ATS). Simple, clean format that passes through automated screening systems. Best for corporate jobs and large companies.',
                'category' => 'ats-friendly',
                'is_premium' => false,
                'price' => 0,
                'color_scheme' => [
                    'primary' => '#1f2937',
                    'secondary' => '#4b5563',
                    'accent' => '#6b7280',
                    'text' => '#111827',
                ],
                'sections' => ['personal_info', 'summary', 'experience', 'education', 'skills', 'certifications'],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Executive Premium',
                'slug' => 'executive-premium',
                'description' => 'Sophisticated design for senior managers, executives, and C-level positions. Emphasizes leadership experience and strategic achievements.',
                'category' => 'executive',
                'is_premium' => true,
                'price' => 500,
                'color_scheme' => [
                    'primary' => '#1e40af',
                    'secondary' => '#3b82f6',
                    'accent' => '#1e3a8a',
                    'text' => '#1f2937',
                ],
                'sections' => ['personal_info', 'executive_summary', 'experience', 'education', 'skills', 'certifications', 'awards'],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Creative Portfolio',
                'slug' => 'creative-portfolio',
                'description' => 'Eye-catching design for creative professionals, designers, marketers, and content creators. Showcases projects and creative work beautifully.',
                'category' => 'creative',
                'is_premium' => true,
                'price' => 400,
                'color_scheme' => [
                    'primary' => '#dc2626',
                    'secondary' => '#ef4444',
                    'accent' => '#991b1b',
                    'text' => '#1f2937',
                ],
                'sections' => ['personal_info', 'summary', 'portfolio', 'experience', 'skills', 'education', 'projects'],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Professional Blue',
                'slug' => 'professional-blue',
                'description' => 'Traditional professional design suitable for banking, finance, healthcare, and formal industries. Conservative yet impressive layout.',
                'category' => 'professional',
                'is_premium' => false,
                'price' => 0,
                'color_scheme' => [
                    'primary' => '#0369a1',
                    'secondary' => '#0284c7',
                    'accent' => '#075985',
                    'text' => '#1f2937',
                ],
                'sections' => ['personal_info', 'summary', 'experience', 'education', 'skills', 'languages', 'references'],
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Tech Minimalist',
                'slug' => 'tech-minimalist',
                'description' => 'Minimalist design focused on content. Perfect for software engineers, data scientists, and technical roles. Highlights technical skills and project work.',
                'category' => 'modern',
                'is_premium' => true,
                'price' => 300,
                'color_scheme' => [
                    'primary' => '#6366f1',
                    'secondary' => '#818cf8',
                    'accent' => '#4f46e5',
                    'text' => '#1f2937',
                ],
                'sections' => ['personal_info', 'summary', 'technical_skills', 'experience', 'projects', 'education', 'certifications'],
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($templates as $templateData) {
            CvTemplate::create($templateData);
        }

        $this->command->info('✅ Created ' . count($templates) . ' CV templates successfully!');
    }
}
