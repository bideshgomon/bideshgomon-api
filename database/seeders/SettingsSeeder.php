<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'Bidesh Gomon', 'group' => 'general', 'type' => 'text', 'description' => 'Website name displayed in header and emails', 'is_public' => true],
            ['key' => 'site_description', 'value' => 'Your trusted platform for international migration services', 'group' => 'general', 'type' => 'text', 'description' => 'Website description for SEO', 'is_public' => true],
            ['key' => 'site_logo', 'value' => '/images/logo.png', 'group' => 'general', 'type' => 'text', 'description' => 'Website logo URL', 'is_public' => true],
            ['key' => 'contact_email', 'value' => 'support@bideshgomon.com', 'group' => 'general', 'type' => 'email', 'description' => 'Primary contact email address', 'is_public' => true],
            ['key' => 'contact_phone', 'value' => '+880 1234567890', 'group' => 'general', 'type' => 'text', 'description' => 'Primary contact phone number', 'is_public' => true],
            ['key' => 'support_hours', 'value' => '9:00 AM - 6:00 PM (GMT+6)', 'group' => 'general', 'type' => 'text', 'description' => 'Customer support hours', 'is_public' => true],
            ['key' => 'timezone', 'value' => 'Asia/Dhaka', 'group' => 'general', 'type' => 'text', 'description' => 'Default timezone for the platform', 'is_public' => false],
            
            // Email Settings
            ['key' => 'email_from_name', 'value' => 'Bidesh Gomon', 'group' => 'email', 'type' => 'text', 'description' => 'Sender name for outgoing emails', 'is_public' => false],
            ['key' => 'email_from_address', 'value' => 'noreply@bideshgomon.com', 'group' => 'email', 'type' => 'email', 'description' => 'Sender email address', 'is_public' => false],
            ['key' => 'email_footer', 'value' => 'Thank you for using Bidesh Gomon!', 'group' => 'email', 'type' => 'text', 'description' => 'Footer text for all emails', 'is_public' => false],
            ['key' => 'email_signature', 'value' => 'Best regards,<br>The Bidesh Gomon Team', 'group' => 'email', 'type' => 'text', 'description' => 'Email signature', 'is_public' => false],
            
            // Job Settings
            ['key' => 'job_application_fee', 'value' => '500', 'group' => 'jobs', 'type' => 'number', 'description' => 'Default job application fee (BDT)', 'is_public' => true],
            ['key' => 'job_posting_duration', 'value' => '30', 'group' => 'jobs', 'type' => 'number', 'description' => 'Default job posting duration in days', 'is_public' => false],
            ['key' => 'max_applications_per_user', 'value' => '10', 'group' => 'jobs', 'type' => 'number', 'description' => 'Maximum applications per user per day', 'is_public' => false],
            ['key' => 'featured_job_price', 'value' => '2000', 'group' => 'jobs', 'type' => 'number', 'description' => 'Price for featuring a job (BDT)', 'is_public' => true],
            
            // Wallet Settings
            ['key' => 'min_withdrawal_amount', 'value' => '1000', 'group' => 'wallet', 'type' => 'number', 'description' => 'Minimum withdrawal amount (BDT)', 'is_public' => true],
            ['key' => 'max_withdrawal_amount', 'value' => '50000', 'group' => 'wallet', 'type' => 'number', 'description' => 'Maximum withdrawal amount per transaction (BDT)', 'is_public' => true],
            ['key' => 'referral_bonus', 'value' => '100', 'group' => 'wallet', 'type' => 'number', 'description' => 'Referral bonus amount (BDT)', 'is_public' => true],
            ['key' => 'cashback_percentage', 'value' => '5', 'group' => 'wallet', 'type' => 'number', 'description' => 'Cashback percentage on transactions', 'is_public' => true],
            ['key' => 'withdrawal_processing_fee', 'value' => '50', 'group' => 'wallet', 'type' => 'number', 'description' => 'Processing fee for withdrawals (BDT)', 'is_public' => true],
            
            // Feature Flags
            ['key' => 'enable_registrations', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable new user registrations', 'is_public' => true],
            ['key' => 'enable_job_applications', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable job application submissions', 'is_public' => true],
            ['key' => 'enable_referrals', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable referral system', 'is_public' => true],
            ['key' => 'enable_cv_builder', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable CV builder feature', 'is_public' => true],
            ['key' => 'enable_insurance', 'value' => '1', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable travel insurance bookings', 'is_public' => true],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'features', 'type' => 'boolean', 'description' => 'Enable maintenance mode (disables public access)', 'is_public' => true],
            
            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'Facebook page URL', 'is_public' => true],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'Twitter profile URL', 'is_public' => true],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'LinkedIn page URL', 'is_public' => true],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'Instagram profile URL', 'is_public' => true],
            ['key' => 'youtube_url', 'value' => 'https://youtube.com/@bideshgomon', 'group' => 'social', 'type' => 'url', 'description' => 'YouTube channel URL', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
