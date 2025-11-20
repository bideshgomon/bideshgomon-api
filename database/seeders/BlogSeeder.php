<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            [
                'name' => 'Visa Guides',
                'slug' => 'visa-guides',
                'description' => 'Complete guides for visa applications',
                'icon' => 'DocumentTextIcon',
                'color' => '#3B82F6',
                'order' => 1,
            ],
            [
                'name' => 'Study Abroad',
                'slug' => 'study-abroad',
                'description' => 'Everything about studying overseas',
                'icon' => 'AcademicCapIcon',
                'color' => '#10B981',
                'order' => 2,
            ],
            [
                'name' => 'Travel Tips',
                'slug' => 'travel-tips',
                'description' => 'Tips for international travelers',
                'icon' => 'GlobeAltIcon',
                'color' => '#F59E0B',
                'order' => 3,
            ],
            [
                'name' => 'Work Abroad',
                'slug' => 'work-abroad',
                'description' => 'Opportunities for working overseas',
                'icon' => 'BriefcaseIcon',
                'color' => '#EF4444',
                'order' => 4,
            ],
            [
                'name' => 'Immigration News',
                'slug' => 'immigration-news',
                'description' => 'Latest immigration news and updates',
                'icon' => 'NewspaperIcon',
                'color' => '#8B5CF6',
                'order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }

        // Create tags
        $tagNames = [
            'Bangladesh', 'Tourist Visa', 'Student Visa', 'Work Permit',
            'Schengen', 'USA', 'Canada', 'UK', 'Australia', 'Dubai',
            'Malaysia', 'Singapore', 'Thailand', 'Europe', 'Asia',
            'Scholarship', 'IELTS', 'Job Search', 'Resume Tips', 'Interview'
        ];

        foreach ($tagNames as $tagName) {
            BlogTag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }

        // Get admin user as author
        $author = User::where('email', 'admin@bgplatform.com')->first();
        if (!$author) {
            $author = User::first();
        }

        // Create sample posts
        $posts = [
            [
                'category_id' => 1,
                'title' => 'Complete Guide to Tourist Visa Application from Bangladesh',
                'excerpt' => 'Everything you need to know about applying for a tourist visa from Bangladesh, including requirements, fees, and processing time.',
                'content' => $this->getSampleContent('tourist-visa'),
                'status' => 'published',
                'is_featured' => true,
                'tags' => ['Bangladesh', 'Tourist Visa'],
            ],
            [
                'category_id' => 2,
                'title' => 'Top 10 Universities in Canada for Bangladeshi Students',
                'excerpt' => 'Discover the best Canadian universities offering scholarships and programs for Bangladeshi students in 2024.',
                'content' => $this->getSampleContent('study-canada'),
                'status' => 'published',
                'is_featured' => true,
                'tags' => ['Bangladesh', 'Canada', 'Student Visa', 'Scholarship'],
            ],
            [
                'category_id' => 3,
                'title' => '15 Essential Travel Tips for First-Time International Travelers',
                'excerpt' => 'Make your first international trip smooth and stress-free with these essential travel tips.',
                'content' => $this->getSampleContent('travel-tips'),
                'status' => 'published',
                'is_featured' => false,
                'tags' => ['Travel Tips'],
            ],
        ];

        foreach ($posts as $postData) {
            $tags = $postData['tags'];
            unset($postData['tags']);

            $postData['author_id'] = $author->id;
            $postData['slug'] = Str::slug($postData['title']);
            $postData['published_at'] = now()->subDays(rand(1, 30));
            $postData['reading_time'] = rand(3, 8);
            $postData['views_count'] = rand(100, 5000);

            $post = BlogPost::create($postData);

            // Attach tags
            $tagIds = BlogTag::whereIn('name', $tags)->pluck('id');
            $post->tags()->attach($tagIds);
        }

        $this->command->info('✅ Blog seeded: 5 categories, 20 tags, 3 sample posts');
    }

    private function getSampleContent(string $type): string
    {
        $contents = [
            'tourist-visa' => '<h2>Understanding Tourist Visa Requirements</h2><p>Applying for a tourist visa from Bangladesh requires careful preparation and understanding of the requirements. This comprehensive guide will walk you through every step of the process.</p><h3>Required Documents</h3><ul><li>Valid passport (minimum 6 months validity)</li><li>Completed visa application form</li><li>Recent passport-sized photographs</li><li>Proof of financial means</li><li>Travel itinerary</li><li>Hotel bookings</li></ul><h3>Application Process</h3><p>The visa application process typically takes 2-4 weeks. Here are the steps...</p>',
            'study-canada' => '<h2>Best Canadian Universities for Bangladeshi Students</h2><p>Canada has become one of the top destinations for Bangladeshi students seeking quality education abroad. Here are the top 10 universities...</p><h3>1. University of Toronto</h3><p>Ranked among the world\'s top 20 universities, U of T offers excellent programs in engineering, medicine, and business...</p><h3>Scholarship Opportunities</h3><p>Many Canadian universities offer generous scholarships for international students...</p>',
            'travel-tips' => '<h2>Essential Tips for First-Time International Travelers</h2><p>Traveling internationally for the first time can be exciting yet overwhelming. Here are 15 essential tips to make your journey smooth...</p><h3>Before You Travel</h3><ol><li>Check passport validity and visa requirements</li><li>Get travel insurance</li><li>Make copies of important documents</li><li>Inform your bank about travel plans</li><li>Download offline maps</li></ol><h3>At the Airport</h3><p>Arrive at least 3 hours early for international flights...</p>',
        ];

        return $contents[$type] ?? '<p>Sample blog content</p>';
    }
}
