<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'BideshGomon', 'group' => 'general', 'type' => 'text', 'description' => 'Website name displayed across the site', 'sort_order' => 1],
            ['key' => 'site_name_bn', 'value' => 'বিদেশগমন', 'group' => 'general', 'type' => 'text', 'description' => 'Website name in Bengali', 'sort_order' => 2],
            ['key' => 'site_tagline', 'value' => 'Your Gateway to Global Opportunities', 'group' => 'general', 'type' => 'text', 'description' => 'Short tagline or slogan', 'sort_order' => 3],
            ['key' => 'site_tagline_bn', 'value' => 'বিশ্বব্যাপী সুযোগের প্রবেশদ্বার', 'group' => 'general', 'type' => 'text', 'description' => 'Tagline in Bengali', 'sort_order' => 4],
            ['key' => 'site_description', 'value' => 'Leading platform for overseas employment and education opportunities', 'group' => 'general', 'type' => 'textarea', 'description' => 'Brief description of your website', 'sort_order' => 5],
            ['key' => 'timezone', 'value' => 'Asia/Dhaka', 'group' => 'general', 'type' => 'text', 'description' => 'Default timezone', 'sort_order' => 6],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'group' => 'general', 'type' => 'text', 'description' => 'Date format (PHP format)', 'sort_order' => 7],
            ['key' => 'time_format', 'value' => 'H:i:s', 'group' => 'general', 'type' => 'text', 'description' => 'Time format (PHP format)', 'sort_order' => 8],

            // Branding Settings
            ['key' => 'logo', 'value' => null, 'group' => 'branding', 'type' => 'image', 'description' => 'Main logo (recommended: 200x60px PNG)', 'sort_order' => 1],
            ['key' => 'logo_dark', 'value' => null, 'group' => 'branding', 'type' => 'image', 'description' => 'Dark mode logo', 'sort_order' => 2],
            ['key' => 'logo_small', 'value' => null, 'group' => 'branding', 'type' => 'image', 'description' => 'Small logo for mobile (50x50px)', 'sort_order' => 3],
            ['key' => 'favicon', 'value' => null, 'group' => 'branding', 'type' => 'image', 'description' => 'Favicon (32x32px ICO or PNG)', 'sort_order' => 4],
            ['key' => 'apple_touch_icon', 'value' => null, 'group' => 'branding', 'type' => 'image', 'description' => 'Apple touch icon (180x180px)', 'sort_order' => 5],
            ['key' => 'og_image', 'value' => null, 'group' => 'branding', 'type' => 'image', 'description' => 'Open Graph default image (1200x630px)', 'sort_order' => 6],
            ['key' => 'primary_color', 'value' => '#3B82F6', 'group' => 'branding', 'type' => 'color', 'description' => 'Primary brand color', 'sort_order' => 7],
            ['key' => 'secondary_color', 'value' => '#8B5CF6', 'group' => 'branding', 'type' => 'color', 'description' => 'Secondary brand color', 'sort_order' => 8],

            // SEO Settings
            ['key' => 'meta_title', 'value' => 'BideshGomon - Overseas Employment & Education Platform', 'group' => 'seo', 'type' => 'text', 'description' => 'Default meta title', 'sort_order' => 1],
            ['key' => 'meta_description', 'value' => 'Find overseas job opportunities, study abroad programs, and immigration services. Your trusted partner for global career advancement.', 'group' => 'seo', 'type' => 'textarea', 'description' => 'Default meta description (max 160 characters)', 'sort_order' => 2],
            ['key' => 'meta_keywords', 'value' => 'overseas jobs, study abroad, immigration, work visa, education abroad, job placement', 'group' => 'seo', 'type' => 'text', 'description' => 'Default meta keywords (comma separated)', 'sort_order' => 3],
            ['key' => 'google_analytics_id', 'value' => null, 'group' => 'seo', 'type' => 'text', 'description' => 'Google Analytics Measurement ID (G-XXXXXXXXXX)', 'sort_order' => 4],
            ['key' => 'google_tag_manager_id', 'value' => null, 'group' => 'seo', 'type' => 'text', 'description' => 'Google Tag Manager ID (GTM-XXXXXXX)', 'sort_order' => 5],
            ['key' => 'google_site_verification', 'value' => null, 'group' => 'seo', 'type' => 'text', 'description' => 'Google Search Console verification code', 'sort_order' => 6],
            ['key' => 'facebook_pixel_id', 'value' => null, 'group' => 'seo', 'type' => 'text', 'description' => 'Facebook Pixel ID', 'sort_order' => 7],
            ['key' => 'robots_txt', 'value' => "User-agent: *\nDisallow: /admin/\nDisallow: /api/\nSitemap: /sitemap.xml", 'group' => 'seo', 'type' => 'textarea', 'description' => 'Robots.txt content', 'sort_order' => 8],

            // Social Media Settings
            ['key' => 'facebook_url', 'value' => null, 'group' => 'social', 'type' => 'text', 'description' => 'Facebook page URL', 'sort_order' => 1],
            ['key' => 'twitter_url', 'value' => null, 'group' => 'social', 'type' => 'text', 'description' => 'Twitter/X profile URL', 'sort_order' => 2],
            ['key' => 'instagram_url', 'value' => null, 'group' => 'social', 'type' => 'text', 'description' => 'Instagram profile URL', 'sort_order' => 3],
            ['key' => 'linkedin_url', 'value' => null, 'group' => 'social', 'type' => 'text', 'description' => 'LinkedIn company page URL', 'sort_order' => 4],
            ['key' => 'youtube_url', 'value' => null, 'group' => 'social', 'type' => 'text', 'description' => 'YouTube channel URL', 'sort_order' => 5],
            ['key' => 'whatsapp_number', 'value' => null, 'group' => 'social', 'type' => 'text', 'description' => 'WhatsApp business number', 'sort_order' => 6],
            ['key' => 'telegram_url', 'value' => null, 'group' => 'social', 'type' => 'text', 'description' => 'Telegram group/channel URL', 'sort_order' => 7],

            // Contact Settings
            ['key' => 'contact_email', 'value' => 'info@bideshgomon.com', 'group' => 'contact', 'type' => 'text', 'description' => 'Primary contact email', 'sort_order' => 1],
            ['key' => 'support_email', 'value' => 'support@bideshgomon.com', 'group' => 'contact', 'type' => 'text', 'description' => 'Support email address', 'sort_order' => 2],
            ['key' => 'contact_phone', 'value' => '+880 1XXX-XXXXXX', 'group' => 'contact', 'type' => 'text', 'description' => 'Primary contact phone', 'sort_order' => 3],
            ['key' => 'contact_phone_alt', 'value' => null, 'group' => 'contact', 'type' => 'text', 'description' => 'Alternative contact phone', 'sort_order' => 4],
            ['key' => 'office_address', 'value' => 'Dhaka, Bangladesh', 'group' => 'contact', 'type' => 'textarea', 'description' => 'Office address', 'sort_order' => 5],
            ['key' => 'office_address_bn', 'value' => 'ঢাকা, বাংলাদেশ', 'group' => 'contact', 'type' => 'textarea', 'description' => 'Office address in Bengali', 'sort_order' => 6],
            ['key' => 'office_hours', 'value' => 'Sunday - Thursday: 9:00 AM - 6:00 PM', 'group' => 'contact', 'type' => 'text', 'description' => 'Office working hours', 'sort_order' => 7],
            ['key' => 'google_maps_embed', 'value' => null, 'group' => 'contact', 'type' => 'textarea', 'description' => 'Google Maps embed iframe code', 'sort_order' => 8],

            // API Keys
            ['key' => 'google_maps_api_key', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Google Maps API Key', 'sort_order' => 1],
            ['key' => 'google_oauth_client_id', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Google OAuth Client ID', 'sort_order' => 2],
            ['key' => 'google_oauth_client_secret', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Google OAuth Client Secret', 'sort_order' => 3],
            ['key' => 'facebook_app_id', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Facebook App ID', 'sort_order' => 4],
            ['key' => 'facebook_app_secret', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Facebook App Secret', 'sort_order' => 5],
            ['key' => 'stripe_publishable_key', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Stripe Publishable Key', 'sort_order' => 6],
            ['key' => 'stripe_secret_key', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Stripe Secret Key', 'sort_order' => 7],
            ['key' => 'paypal_client_id', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'PayPal Client ID', 'sort_order' => 8],
            ['key' => 'paypal_secret', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'PayPal Secret', 'sort_order' => 9],
            ['key' => 'sslcommerz_store_id', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'SSLCommerz Store ID', 'sort_order' => 10],
            ['key' => 'sslcommerz_store_password', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'SSLCommerz Store Password', 'sort_order' => 11],
            ['key' => 'bkash_app_key', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'bKash App Key', 'sort_order' => 12],
            ['key' => 'bkash_app_secret', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'bKash App Secret', 'sort_order' => 13],
            ['key' => 'nagad_merchant_id', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Nagad Merchant ID', 'sort_order' => 14],
            ['key' => 'nagad_merchant_key', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Nagad Merchant Key', 'sort_order' => 15],
            ['key' => 'aws_access_key_id', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'AWS Access Key ID', 'sort_order' => 16],
            ['key' => 'aws_secret_access_key', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'AWS Secret Access Key', 'sort_order' => 17],
            ['key' => 'aws_default_region', 'value' => 'us-east-1', 'group' => 'api', 'type' => 'text', 'description' => 'AWS Default Region', 'sort_order' => 18],
            ['key' => 'aws_bucket', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'AWS S3 Bucket Name', 'sort_order' => 19],
            ['key' => 'pusher_app_id', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Pusher App ID', 'sort_order' => 20],
            ['key' => 'pusher_app_key', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Pusher App Key', 'sort_order' => 21],
            ['key' => 'pusher_app_secret', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Pusher App Secret', 'sort_order' => 22],
            ['key' => 'pusher_app_cluster', 'value' => 'ap2', 'group' => 'api', 'type' => 'text', 'description' => 'Pusher App Cluster', 'sort_order' => 23],
            ['key' => 'mailgun_domain', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Mailgun Domain', 'sort_order' => 24],
            ['key' => 'mailgun_secret', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Mailgun Secret', 'sort_order' => 25],
            ['key' => 'twilio_sid', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Twilio Account SID', 'sort_order' => 26],
            ['key' => 'twilio_auth_token', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Twilio Auth Token', 'sort_order' => 27],
            ['key' => 'openai_api_key', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'OpenAI API Key', 'sort_order' => 28],
            ['key' => 'recaptcha_site_key', 'value' => null, 'group' => 'api', 'type' => 'text', 'description' => 'Google reCAPTCHA Site Key', 'sort_order' => 29],
            ['key' => 'recaptcha_secret_key', 'value' => null, 'group' => 'api', 'type' => 'password', 'description' => 'Google reCAPTCHA Secret Key', 'sort_order' => 30],

            // Advanced Settings
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'advanced', 'type' => 'boolean', 'description' => 'Enable maintenance mode', 'sort_order' => 1],
            ['key' => 'maintenance_message', 'value' => 'We are currently performing scheduled maintenance. We will be back soon!', 'group' => 'advanced', 'type' => 'textarea', 'description' => 'Maintenance mode message', 'sort_order' => 2],
            ['key' => 'site_offline', 'value' => '0', 'group' => 'advanced', 'type' => 'boolean', 'description' => 'Make site offline (except admin)', 'sort_order' => 3],
            ['key' => 'custom_css', 'value' => null, 'group' => 'advanced', 'type' => 'textarea', 'description' => 'Custom CSS code', 'sort_order' => 4],
            ['key' => 'custom_js', 'value' => null, 'group' => 'advanced', 'type' => 'textarea', 'description' => 'Custom JavaScript code (in <head>)', 'sort_order' => 5],
            ['key' => 'custom_js_footer', 'value' => null, 'group' => 'advanced', 'type' => 'textarea', 'description' => 'Custom JavaScript code (before </body>)', 'sort_order' => 6],
            ['key' => 'cookie_consent_enabled', 'value' => '1', 'group' => 'advanced', 'type' => 'boolean', 'description' => 'Enable cookie consent banner', 'sort_order' => 7],
            ['key' => 'cookie_consent_text', 'value' => 'We use cookies to improve your experience. By continuing to use this site, you accept our use of cookies.', 'group' => 'advanced', 'type' => 'textarea', 'description' => 'Cookie consent message', 'sort_order' => 8],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
