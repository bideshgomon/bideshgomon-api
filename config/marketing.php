<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Tag Manager
    |--------------------------------------------------------------------------
    |
    | Your Google Tag Manager container ID (e.g., GTM-XXXXXXX)
    | Can also be set via Admin Panel: /admin/settings/seo
    |
    */
    'gtm_id' => env('GTM_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Google Analytics
    |--------------------------------------------------------------------------
    |
    | Your Google Analytics 4 measurement ID (e.g., G-XXXXXXXXXX)
    | Can also be set via Admin Panel: /admin/settings/seo
    |
    */
    'ga4_id' => env('GA4_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Google Search Console
    |--------------------------------------------------------------------------
    |
    | Your Google Search Console verification meta tag content
    | Can also be set via Admin Panel: /admin/settings/seo
    |
    */
    'gsc_verification' => env('GSC_VERIFICATION', ''),

    /*
    |--------------------------------------------------------------------------
    | Facebook Pixel
    |--------------------------------------------------------------------------
    |
    | Your Facebook Pixel ID for conversion tracking
    |
    */
    'facebook_pixel_id' => env('FACEBOOK_PIXEL_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Default SEO Settings
    |--------------------------------------------------------------------------
    */
    'default_seo' => [
        'title' => env('APP_NAME', 'BideshGomon'),
        'title_suffix' => ' | BideshGomon',
        'description' => 'Find your dream university abroad, apply for student visas, work visas, tourist visas, and purchase travel insurance.',
        'keywords' => 'study abroad, student visa, work visa, tourist visa, travel insurance, universities abroad, international education',
        'og_image' => '/images/og-default.jpg',
        'twitter_site' => '@bideshgomon',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sitemap Settings
    |--------------------------------------------------------------------------
    */
    'sitemap' => [
        'cache_duration' => 3600, // Cache sitemap for 1 hour
        'max_urls_per_sitemap' => 50000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Settings
    |--------------------------------------------------------------------------
    */
    'email' => [
        'from_name' => env('MAIL_FROM_NAME', 'BideshGomon'),
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@bideshgomon.com'),
        'footer_text' => 'BideshGomon - Your trusted partner for studying abroad',
        'unsubscribe_url' => env('APP_URL').'/unsubscribe',
    ],

    /*
    |--------------------------------------------------------------------------
    | Campaign Settings
    |--------------------------------------------------------------------------
    */
    'campaigns' => [
        'max_recipients_per_batch' => 1000,
        'batch_delay_seconds' => 60,
        'track_opens' => true,
        'track_clicks' => true,
    ],

];
