<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // Top priority countries for Bidesh Gomon users
            ['name' => 'Bangladesh', 'name_bn' => 'বাংলাদেশ', 'iso_code_2' => 'BD', 'iso_code_3' => 'BGD', 'phone_code' => '+880', 'currency_code' => 'BDT', 'flag_emoji' => '🇧🇩', 'region' => 'Asia'],
            ['name' => 'India', 'name_bn' => 'ভারত', 'iso_code_2' => 'IN', 'iso_code_3' => 'IND', 'phone_code' => '+91', 'currency_code' => 'INR', 'flag_emoji' => '🇮🇳', 'region' => 'Asia'],
            ['name' => 'Saudi Arabia', 'name_bn' => 'সৌদি আরব', 'iso_code_2' => 'SA', 'iso_code_3' => 'SAU', 'phone_code' => '+966', 'currency_code' => 'SAR', 'flag_emoji' => '🇸🇦', 'region' => 'Asia'],
            ['name' => 'United Arab Emirates', 'name_bn' => 'সংযুক্ত আরব আমিরাত', 'iso_code_2' => 'AE', 'iso_code_3' => 'ARE', 'phone_code' => '+971', 'currency_code' => 'AED', 'flag_emoji' => '🇦🇪', 'region' => 'Asia'],
            ['name' => 'United States', 'name_bn' => 'যুক্তরাষ্ট্র', 'iso_code_2' => 'US', 'iso_code_3' => 'USA', 'phone_code' => '+1', 'currency_code' => 'USD', 'flag_emoji' => '🇺🇸', 'region' => 'Americas'],
            ['name' => 'United Kingdom', 'name_bn' => 'যুক্তরাজ্য', 'iso_code_2' => 'GB', 'iso_code_3' => 'GBR', 'phone_code' => '+44', 'currency_code' => 'GBP', 'flag_emoji' => '🇬🇧', 'region' => 'Europe'],
            ['name' => 'Canada', 'name_bn' => 'কানাডা', 'iso_code_2' => 'CA', 'iso_code_3' => 'CAN', 'phone_code' => '+1', 'currency_code' => 'CAD', 'flag_emoji' => '🇨🇦', 'region' => 'Americas'],
            ['name' => 'Australia', 'name_bn' => 'অস্ট্রেলিয়া', 'iso_code_2' => 'AU', 'iso_code_3' => 'AUS', 'phone_code' => '+61', 'currency_code' => 'AUD', 'flag_emoji' => '🇦🇺', 'region' => 'Oceania'],
            ['name' => 'Malaysia', 'name_bn' => 'মালয়েশিয়া', 'iso_code_2' => 'MY', 'iso_code_3' => 'MYS', 'phone_code' => '+60', 'currency_code' => 'MYR', 'flag_emoji' => '🇲🇾', 'region' => 'Asia'],
            ['name' => 'Singapore', 'name_bn' => 'সিঙ্গাপুর', 'iso_code_2' => 'SG', 'iso_code_3' => 'SGP', 'phone_code' => '+65', 'currency_code' => 'SGD', 'flag_emoji' => '🇸🇬', 'region' => 'Asia'],
            
            // Additional Asian countries
            ['name' => 'Pakistan', 'name_bn' => 'পাকিস্তান', 'iso_code_2' => 'PK', 'iso_code_3' => 'PAK', 'phone_code' => '+92', 'currency_code' => 'PKR', 'flag_emoji' => '🇵🇰', 'region' => 'Asia'],
            ['name' => 'China', 'name_bn' => 'চীন', 'iso_code_2' => 'CN', 'iso_code_3' => 'CHN', 'phone_code' => '+86', 'currency_code' => 'CNY', 'flag_emoji' => '🇨🇳', 'region' => 'Asia'],
            ['name' => 'Japan', 'name_bn' => 'জাপান', 'iso_code_2' => 'JP', 'iso_code_3' => 'JPN', 'phone_code' => '+81', 'currency_code' => 'JPY', 'flag_emoji' => '🇯🇵', 'region' => 'Asia'],
            ['name' => 'South Korea', 'name_bn' => 'দক্ষিণ কোরিয়া', 'iso_code_2' => 'KR', 'iso_code_3' => 'KOR', 'phone_code' => '+82', 'currency_code' => 'KRW', 'flag_emoji' => '🇰🇷', 'region' => 'Asia'],
            ['name' => 'Thailand', 'name_bn' => 'থাইল্যান্ড', 'iso_code_2' => 'TH', 'iso_code_3' => 'THA', 'phone_code' => '+66', 'currency_code' => 'THB', 'flag_emoji' => '🇹🇭', 'region' => 'Asia'],
            ['name' => 'Indonesia', 'name_bn' => 'ইন্দোনেশিয়া', 'iso_code_2' => 'ID', 'iso_code_3' => 'IDN', 'phone_code' => '+62', 'currency_code' => 'IDR', 'flag_emoji' => '🇮🇩', 'region' => 'Asia'],
            ['name' => 'Philippines', 'name_bn' => 'ফিলিপাইন', 'iso_code_2' => 'PH', 'iso_code_3' => 'PHL', 'phone_code' => '+63', 'currency_code' => 'PHP', 'flag_emoji' => '🇵🇭', 'region' => 'Asia'],
            ['name' => 'Vietnam', 'name_bn' => 'ভিয়েতনাম', 'iso_code_2' => 'VN', 'iso_code_3' => 'VNM', 'phone_code' => '+84', 'currency_code' => 'VND', 'flag_emoji' => '🇻🇳', 'region' => 'Asia'],
            
            // Middle East
            ['name' => 'Qatar', 'name_bn' => 'কাতার', 'iso_code_2' => 'QA', 'iso_code_3' => 'QAT', 'phone_code' => '+974', 'currency_code' => 'QAR', 'flag_emoji' => '🇶🇦', 'region' => 'Asia'],
            ['name' => 'Kuwait', 'name_bn' => 'কুয়েত', 'iso_code_2' => 'KW', 'iso_code_3' => 'KWT', 'phone_code' => '+965', 'currency_code' => 'KWD', 'flag_emoji' => '🇰🇼', 'region' => 'Asia'],
            ['name' => 'Bahrain', 'name_bn' => 'বাহরাইন', 'iso_code_2' => 'BH', 'iso_code_3' => 'BHR', 'phone_code' => '+973', 'currency_code' => 'BHD', 'flag_emoji' => '🇧🇭', 'region' => 'Asia'],
            ['name' => 'Oman', 'name_bn' => 'ওমান', 'iso_code_2' => 'OM', 'iso_code_3' => 'OMN', 'phone_code' => '+968', 'currency_code' => 'OMR', 'flag_emoji' => '🇴🇲', 'region' => 'Asia'],
            ['name' => 'Jordan', 'name_bn' => 'জর্ডান', 'iso_code_2' => 'JO', 'iso_code_3' => 'JOR', 'phone_code' => '+962', 'currency_code' => 'JOD', 'flag_emoji' => '🇯🇴', 'region' => 'Asia'],
            ['name' => 'Lebanon', 'name_bn' => 'লেবানন', 'iso_code_2' => 'LB', 'iso_code_3' => 'LBN', 'phone_code' => '+961', 'currency_code' => 'LBP', 'flag_emoji' => '🇱🇧', 'region' => 'Asia'],
            
            // Europe
            ['name' => 'Germany', 'name_bn' => 'জার্মানি', 'iso_code_2' => 'DE', 'iso_code_3' => 'DEU', 'phone_code' => '+49', 'currency_code' => 'EUR', 'flag_emoji' => '🇩🇪', 'region' => 'Europe'],
            ['name' => 'France', 'name_bn' => 'ফ্রান্স', 'iso_code_2' => 'FR', 'iso_code_3' => 'FRA', 'phone_code' => '+33', 'currency_code' => 'EUR', 'flag_emoji' => '🇫🇷', 'region' => 'Europe'],
            ['name' => 'Italy', 'name_bn' => 'ইতালি', 'iso_code_2' => 'IT', 'iso_code_3' => 'ITA', 'phone_code' => '+39', 'currency_code' => 'EUR', 'flag_emoji' => '🇮🇹', 'region' => 'Europe'],
            ['name' => 'Spain', 'name_bn' => 'স্পেন', 'iso_code_2' => 'ES', 'iso_code_3' => 'ESP', 'phone_code' => '+34', 'currency_code' => 'EUR', 'flag_emoji' => '🇪🇸', 'region' => 'Europe'],
            ['name' => 'Netherlands', 'name_bn' => 'নেদারল্যান্ডস', 'iso_code_2' => 'NL', 'iso_code_3' => 'NLD', 'phone_code' => '+31', 'currency_code' => 'EUR', 'flag_emoji' => '🇳🇱', 'region' => 'Europe'],
            ['name' => 'Sweden', 'name_bn' => 'সুইডেন', 'iso_code_2' => 'SE', 'iso_code_3' => 'SWE', 'phone_code' => '+46', 'currency_code' => 'SEK', 'flag_emoji' => '🇸🇪', 'region' => 'Europe'],
            ['name' => 'Norway', 'name_bn' => 'নরওয়ে', 'iso_code_2' => 'NO', 'iso_code_3' => 'NOR', 'phone_code' => '+47', 'currency_code' => 'NOK', 'flag_emoji' => '🇳🇴', 'region' => 'Europe'],
            ['name' => 'Denmark', 'name_bn' => 'ডেনমার্ক', 'iso_code_2' => 'DK', 'iso_code_3' => 'DNK', 'phone_code' => '+45', 'currency_code' => 'DKK', 'flag_emoji' => '🇩🇰', 'region' => 'Europe'],
            ['name' => 'Switzerland', 'name_bn' => 'সুইজারল্যান্ড', 'iso_code_2' => 'CH', 'iso_code_3' => 'CHE', 'phone_code' => '+41', 'currency_code' => 'CHF', 'flag_emoji' => '🇨🇭', 'region' => 'Europe'],
            ['name' => 'Austria', 'name_bn' => 'অস্ট্রিয়া', 'iso_code_2' => 'AT', 'iso_code_3' => 'AUT', 'phone_code' => '+43', 'currency_code' => 'EUR', 'flag_emoji' => '🇦🇹', 'region' => 'Europe'],
            ['name' => 'Belgium', 'name_bn' => 'বেলজিয়াম', 'iso_code_2' => 'BE', 'iso_code_3' => 'BEL', 'phone_code' => '+32', 'currency_code' => 'EUR', 'flag_emoji' => '🇧🇪', 'region' => 'Europe'],
            
            // Additional countries (total 50+)
            ['name' => 'New Zealand', 'name_bn' => 'নিউজিল্যান্ড', 'iso_code_2' => 'NZ', 'iso_code_3' => 'NZL', 'phone_code' => '+64', 'currency_code' => 'NZD', 'flag_emoji' => '🇳🇿', 'region' => 'Oceania'],
            ['name' => 'Brazil', 'name_bn' => 'ব্রাজিল', 'iso_code_2' => 'BR', 'iso_code_3' => 'BRA', 'phone_code' => '+55', 'currency_code' => 'BRL', 'flag_emoji' => '🇧🇷', 'region' => 'Americas'],
            ['name' => 'Mexico', 'name_bn' => 'মেক্সিকো', 'iso_code_2' => 'MX', 'iso_code_3' => 'MEX', 'phone_code' => '+52', 'currency_code' => 'MXN', 'flag_emoji' => '🇲🇽', 'region' => 'Americas'],
            ['name' => 'Argentina', 'name_bn' => 'আর্জেন্টিনা', 'iso_code_2' => 'AR', 'iso_code_3' => 'ARG', 'phone_code' => '+54', 'currency_code' => 'ARS', 'flag_emoji' => '🇦🇷', 'region' => 'Americas'],
            ['name' => 'South Africa', 'name_bn' => 'দক্ষিণ আফ্রিকা', 'iso_code_2' => 'ZA', 'iso_code_3' => 'ZAF', 'phone_code' => '+27', 'currency_code' => 'ZAR', 'flag_emoji' => '🇿🇦', 'region' => 'Africa'],
            ['name' => 'Egypt', 'name_bn' => 'মিশর', 'iso_code_2' => 'EG', 'iso_code_3' => 'EGY', 'phone_code' => '+20', 'currency_code' => 'EGP', 'flag_emoji' => '🇪🇬', 'region' => 'Africa'],
            ['name' => 'Nigeria', 'name_bn' => 'নাইজেরিয়া', 'iso_code_2' => 'NG', 'iso_code_3' => 'NGA', 'phone_code' => '+234', 'currency_code' => 'NGN', 'flag_emoji' => '🇳🇬', 'region' => 'Africa'],
            ['name' => 'Kenya', 'name_bn' => 'কেনিয়া', 'iso_code_2' => 'KE', 'iso_code_3' => 'KEN', 'phone_code' => '+254', 'currency_code' => 'KES', 'flag_emoji' => '🇰🇪', 'region' => 'Africa'],
            ['name' => 'Turkey', 'name_bn' => 'তুরস্ক', 'iso_code_2' => 'TR', 'iso_code_3' => 'TUR', 'phone_code' => '+90', 'currency_code' => 'TRY', 'flag_emoji' => '🇹🇷', 'region' => 'Europe'],
            ['name' => 'Russia', 'name_bn' => 'রাশিয়া', 'iso_code_2' => 'RU', 'iso_code_3' => 'RUS', 'phone_code' => '+7', 'currency_code' => 'RUB', 'flag_emoji' => '🇷🇺', 'region' => 'Europe'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
