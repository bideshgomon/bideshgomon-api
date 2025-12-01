<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Defaults
    |--------------------------------------------------------------------------
    |
    | These are safe fallback values used when settings are not found in the database.
    | This ensures the site NEVER crashes due to missing settings.
    |
    */

    // General
    'site_name' => 'Bidesh Gomon',
    'site_description' => 'Your trusted platform for international migration services',
    'site_logo' => '/images/logo.png',
    'site_favicon' => '/images/favicon.ico',
    'contact_email' => 'support@bideshgomon.com',
    'contact_phone' => '+880 1234567890',
    'support_hours' => '9:00 AM - 6:00 PM (GMT+6)',

    // Feature Flags
    'maintenance_mode' => false,
    'enable_registrations' => true,
    'enable_job_applications' => true,
    'enable_referrals' => true,

    // Module Toggles
    'enable_jobs' => true,
    'enable_blogs' => true,
    'enable_directory' => true,
    'enable_university' => true,
    'enable_packages' => true,

    // Homepage Widgets
    'show_hero_search' => true,
    'show_featured_jobs' => true,
    'featured_jobs_count' => 6,
    'show_top_universities' => true,
    'top_universities_count' => 8,
    'show_latest_blogs' => true,
    'latest_blogs_count' => 3,
    'show_visa_packages' => true,
    'visa_packages_count' => 4,

    // Jobs Module
    'job_application_fee' => 500,
    'job_posting_duration' => 30,
    'max_applications_per_user' => 10,
    'job_currency_default' => 'BDT',
    'job_guest_apply' => false,
    'job_enable_expiry' => true,

    // University Module
    'edu_show_scholarships' => true,
    'edu_application_fee' => 1000,
    'edu_posts_per_page' => 12,

    // Directory Module
    'directory_approval_required' => true,
    'directory_layout' => 'grid',
    'directory_posts_per_page' => 20,

    // Blogs Module
    'blog_comments_enabled' => true,
    'blog_posts_per_page' => 12,
    'blog_show_author' => true,

    // Packages Module
    'package_display_style' => 'card',
    'package_inquiry_email' => 'packages@bideshgomon.com',
    'packages_per_page' => 9,

    // Wallet
    'min_withdrawal_amount' => 1000,
    'referral_bonus' => 100,
    'cashback_percentage' => 5,

    // Social Media
    'facebook_url' => 'https://facebook.com/bideshgomon',
    'twitter_url' => 'https://twitter.com/bideshgomon',
    'linkedin_url' => 'https://linkedin.com/company/bideshgomon',
    'instagram_url' => 'https://instagram.com/bideshgomon',
    'youtube_url' => '',
    'tiktok_url' => '',

    // SEO
    'seo_meta_title' => 'Bidesh Gomon - Your Gateway to Global Opportunities',
    'seo_meta_description' => 'Apply for visas, find jobs abroad, and access travel services on one platform.',
    'seo_meta_keywords' => 'visa, jobs abroad, international migration, travel services',

    // Email
    'email_from_name' => 'Bidesh Gomon',
    'email_from_address' => 'noreply@bideshgomon.com',
];
