<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class ModuleSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Module Enable/Disable Toggles
            ['key' => 'enable_jobs', 'value' => '1', 'group' => 'modules', 'type' => 'boolean', 'description' => 'Enable Jobs module', 'sort_order' => 1],
            ['key' => 'enable_blogs', 'value' => '1', 'group' => 'modules', 'type' => 'boolean', 'description' => 'Enable Blogs module', 'sort_order' => 2],
            ['key' => 'enable_directory', 'value' => '1', 'group' => 'modules', 'type' => 'boolean', 'description' => 'Enable Directory module', 'sort_order' => 3],
            ['key' => 'enable_university', 'value' => '1', 'group' => 'modules', 'type' => 'boolean', 'description' => 'Enable University/Education module', 'sort_order' => 4],
            ['key' => 'enable_packages', 'value' => '1', 'group' => 'modules', 'type' => 'boolean', 'description' => 'Enable Visa Packages module', 'sort_order' => 5],

            // Jobs Module Settings
            ['key' => 'job_enable_expiry', 'value' => '1', 'group' => 'jobs', 'type' => 'boolean', 'description' => 'Auto-expire jobs after duration', 'sort_order' => 1],
            ['key' => 'job_posting_duration', 'value' => '30', 'group' => 'jobs', 'type' => 'number', 'description' => 'Job posting duration (days)', 'sort_order' => 2],
            ['key' => 'job_currency_default', 'value' => 'BDT', 'group' => 'jobs', 'type' => 'text', 'description' => 'Default currency for salaries', 'sort_order' => 3],
            ['key' => 'job_guest_apply', 'value' => '0', 'group' => 'jobs', 'type' => 'boolean', 'description' => 'Allow non-users to apply', 'sort_order' => 4],
            ['key' => 'job_application_fee', 'value' => '500', 'group' => 'jobs', 'type' => 'number', 'description' => 'Application fee (BDT)', 'sort_order' => 5],
            ['key' => 'max_applications_per_user', 'value' => '10', 'group' => 'jobs', 'type' => 'number', 'description' => 'Max applications per day', 'sort_order' => 6],
            ['key' => 'job_approval_required', 'value' => '1', 'group' => 'jobs', 'type' => 'boolean', 'description' => 'Require admin approval for new jobs', 'sort_order' => 7],
            ['key' => 'jobs_per_page', 'value' => '20', 'group' => 'jobs', 'type' => 'number', 'description' => 'Jobs per page in listings', 'sort_order' => 8],

            // University/Education Module Settings
            ['key' => 'edu_show_scholarships', 'value' => '1', 'group' => 'university', 'type' => 'boolean', 'description' => 'Show scholarship opportunities', 'sort_order' => 1],
            ['key' => 'edu_application_fee', 'value' => '1000', 'group' => 'university', 'type' => 'number', 'description' => 'Default application fee (BDT)', 'sort_order' => 2],
            ['key' => 'edu_posts_per_page', 'value' => '12', 'group' => 'university', 'type' => 'number', 'description' => 'Universities per page', 'sort_order' => 3],
            ['key' => 'edu_approval_required', 'value' => '1', 'group' => 'university', 'type' => 'boolean', 'description' => 'Require admin approval', 'sort_order' => 4],
            ['key' => 'edu_enable_reviews', 'value' => '1', 'group' => 'university', 'type' => 'boolean', 'description' => 'Enable student reviews', 'sort_order' => 5],

            // Directory Module Settings
            ['key' => 'directory_approval_required', 'value' => '1', 'group' => 'directory', 'type' => 'boolean', 'description' => 'Require admin approval for new listings', 'sort_order' => 1],
            ['key' => 'directory_layout', 'value' => 'grid', 'group' => 'directory', 'type' => 'text', 'description' => 'Default layout (grid/list)', 'sort_order' => 2],
            ['key' => 'directory_posts_per_page', 'value' => '20', 'group' => 'directory', 'type' => 'number', 'description' => 'Listings per page', 'sort_order' => 3],
            ['key' => 'directory_enable_reviews', 'value' => '1', 'group' => 'directory', 'type' => 'boolean', 'description' => 'Enable user reviews', 'sort_order' => 4],
            ['key' => 'directory_featured_limit', 'value' => '5', 'group' => 'directory', 'type' => 'number', 'description' => 'Number of featured listings on homepage', 'sort_order' => 5],

            // Blogs Module Settings
            ['key' => 'blog_comments_enabled', 'value' => '1', 'group' => 'blogs', 'type' => 'boolean', 'description' => 'Enable comments on blog posts', 'sort_order' => 1],
            ['key' => 'blog_posts_per_page', 'value' => '12', 'group' => 'blogs', 'type' => 'number', 'description' => 'Blog posts per page', 'sort_order' => 2],
            ['key' => 'blog_show_author', 'value' => '1', 'group' => 'blogs', 'type' => 'boolean', 'description' => 'Show author name and bio', 'sort_order' => 3],
            ['key' => 'blog_approval_required', 'value' => '1', 'group' => 'blogs', 'type' => 'boolean', 'description' => 'Require admin approval', 'sort_order' => 4],
            ['key' => 'blog_allow_guest_comments', 'value' => '0', 'group' => 'blogs', 'type' => 'boolean', 'description' => 'Allow non-users to comment', 'sort_order' => 5],

            // Packages Module Settings
            ['key' => 'package_display_style', 'value' => 'card', 'group' => 'packages', 'type' => 'text', 'description' => 'Display style (card/table)', 'sort_order' => 1],
            ['key' => 'package_inquiry_email', 'value' => 'packages@bideshgomon.com', 'group' => 'packages', 'type' => 'text', 'description' => 'Email for package inquiries', 'sort_order' => 2],
            ['key' => 'packages_per_page', 'value' => '9', 'group' => 'packages', 'type' => 'number', 'description' => 'Packages per page', 'sort_order' => 3],
            ['key' => 'package_enable_booking', 'value' => '1', 'group' => 'packages', 'type' => 'boolean', 'description' => 'Enable online booking', 'sort_order' => 4],
            ['key' => 'package_require_documents', 'value' => '1', 'group' => 'packages', 'type' => 'boolean', 'description' => 'Require document uploads for bookings', 'sort_order' => 5],

            // Homepage Widget Configuration
            ['key' => 'show_hero_search', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show hero search bar', 'sort_order' => 1],
            ['key' => 'show_featured_jobs', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show featured jobs section', 'sort_order' => 2],
            ['key' => 'featured_jobs_count', 'value' => '6', 'group' => 'homepage', 'type' => 'number', 'description' => 'Number of featured jobs to show', 'sort_order' => 3],
            ['key' => 'show_top_universities', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show top universities section', 'sort_order' => 4],
            ['key' => 'top_universities_count', 'value' => '8', 'group' => 'homepage', 'type' => 'number', 'description' => 'Number of top universities to show', 'sort_order' => 5],
            ['key' => 'show_latest_blogs', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show latest blog posts', 'sort_order' => 6],
            ['key' => 'latest_blogs_count', 'value' => '3', 'group' => 'homepage', 'type' => 'number', 'description' => 'Number of blog posts to show', 'sort_order' => 7],
            ['key' => 'show_visa_packages', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show visa packages section', 'sort_order' => 8],
            ['key' => 'visa_packages_count', 'value' => '4', 'group' => 'homepage', 'type' => 'number', 'description' => 'Number of packages to show', 'sort_order' => 9],
            ['key' => 'show_testimonials', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show testimonials section', 'sort_order' => 10],
            ['key' => 'show_stats', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show statistics section', 'sort_order' => 11],
            ['key' => 'show_partners', 'value' => '1', 'group' => 'homepage', 'type' => 'boolean', 'description' => 'Show partners/clients section', 'sort_order' => 12],

            // Feature Flags
            ['key' => 'enable_registrations', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Allow new user registrations', 'sort_order' => 1],
            ['key' => 'enable_job_applications', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Allow job applications', 'sort_order' => 2],
            ['key' => 'enable_referrals', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable referral system', 'sort_order' => 3],
            ['key' => 'enable_wallet', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable wallet system', 'sort_order' => 4],
            ['key' => 'enable_notifications', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable push notifications', 'sort_order' => 5],

            // Wallet Settings
            ['key' => 'min_withdrawal_amount', 'value' => '1000', 'group' => 'wallet', 'type' => 'number', 'description' => 'Minimum withdrawal amount (BDT)', 'sort_order' => 1],
            ['key' => 'referral_bonus', 'value' => '100', 'group' => 'wallet', 'type' => 'number', 'description' => 'Referral bonus amount (BDT)', 'sort_order' => 2],
            ['key' => 'cashback_percentage', 'value' => '5', 'group' => 'wallet', 'type' => 'number', 'description' => 'Cashback percentage on transactions', 'sort_order' => 3],
            ['key' => 'wallet_currency', 'value' => 'BDT', 'group' => 'wallet', 'type' => 'text', 'description' => 'Wallet currency code', 'sort_order' => 4],

            // Email Settings
            ['key' => 'email_from_name', 'value' => 'BideshGomon', 'group' => 'email', 'type' => 'text', 'description' => 'From name for outgoing emails', 'sort_order' => 1],
            ['key' => 'email_from_address', 'value' => 'noreply@bideshgomon.com', 'group' => 'email', 'type' => 'text', 'description' => 'From address for outgoing emails', 'sort_order' => 2],
            ['key' => 'email_footer', 'value' => 'Thank you for using BideshGomon!', 'group' => 'email', 'type' => 'textarea', 'description' => 'Email footer text', 'sort_order' => 3],
            ['key' => 'email_logo', 'value' => null, 'group' => 'email', 'type' => 'image', 'description' => 'Logo for email templates', 'sort_order' => 4],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
