<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            // Top priority for Bangladesh users
            ['name' => 'Bengali', 'name_bn' => 'বাংলা', 'code' => 'bn', 'native_name' => 'বাংলা'],
            ['name' => 'English', 'name_bn' => 'ইংরেজি', 'code' => 'en', 'native_name' => 'English'],
            ['name' => 'Hindi', 'name_bn' => 'হিন্দি', 'code' => 'hi', 'native_name' => 'हिन्दी'],
            ['name' => 'Urdu', 'name_bn' => 'উর্দু', 'code' => 'ur', 'native_name' => 'اردو'],
            ['name' => 'Arabic', 'name_bn' => 'আরবি', 'code' => 'ar', 'native_name' => 'العربية'],
            
            // Major Asian languages
            ['name' => 'Chinese', 'name_bn' => 'চীনা', 'code' => 'zh', 'native_name' => '中文'],
            ['name' => 'Japanese', 'name_bn' => 'জাপানি', 'code' => 'ja', 'native_name' => '日本語'],
            ['name' => 'Korean', 'name_bn' => 'কোরিয়ান', 'code' => 'ko', 'native_name' => '한국어'],
            ['name' => 'Malay', 'name_bn' => 'মালয়', 'code' => 'ms', 'native_name' => 'Bahasa Melayu'],
            ['name' => 'Thai', 'name_bn' => 'থাই', 'code' => 'th', 'native_name' => 'ไทย'],
            ['name' => 'Vietnamese', 'name_bn' => 'ভিয়েতনামী', 'code' => 'vi', 'native_name' => 'Tiếng Việt'],
            ['name' => 'Indonesian', 'name_bn' => 'ইন্দোনেশিয়ান', 'code' => 'id', 'native_name' => 'Bahasa Indonesia'],
            ['name' => 'Tagalog', 'name_bn' => 'তাগালগ', 'code' => 'tl', 'native_name' => 'Tagalog'],
            ['name' => 'Tamil', 'name_bn' => 'তামিল', 'code' => 'ta', 'native_name' => 'தமிழ்'],
            ['name' => 'Telugu', 'name_bn' => 'তেলুগু', 'code' => 'te', 'native_name' => 'తెలుగు'],
            
            // European languages
            ['name' => 'Spanish', 'name_bn' => 'স্প্যানিশ', 'code' => 'es', 'native_name' => 'Español'],
            ['name' => 'French', 'name_bn' => 'ফরাসি', 'code' => 'fr', 'native_name' => 'Français'],
            ['name' => 'German', 'name_bn' => 'জার্মান', 'code' => 'de', 'native_name' => 'Deutsch'],
            ['name' => 'Italian', 'name_bn' => 'ইতালিয়ান', 'code' => 'it', 'native_name' => 'Italiano'],
            ['name' => 'Portuguese', 'name_bn' => 'পর্তুগিজ', 'code' => 'pt', 'native_name' => 'Português'],
            ['name' => 'Russian', 'name_bn' => 'রাশিয়ান', 'code' => 'ru', 'native_name' => 'Русский'],
            ['name' => 'Dutch', 'name_bn' => 'ডাচ', 'code' => 'nl', 'native_name' => 'Nederlands'],
            ['name' => 'Swedish', 'name_bn' => 'সুইডিশ', 'code' => 'sv', 'native_name' => 'Svenska'],
            ['name' => 'Norwegian', 'name_bn' => 'নরওয়েজিয়ান', 'code' => 'no', 'native_name' => 'Norsk'],
            ['name' => 'Danish', 'name_bn' => 'ডেনিশ', 'code' => 'da', 'native_name' => 'Dansk'],
            ['name' => 'Polish', 'name_bn' => 'পোলিশ', 'code' => 'pl', 'native_name' => 'Polski'],
            ['name' => 'Turkish', 'name_bn' => 'তুর্কি', 'code' => 'tr', 'native_name' => 'Türkçe'],
            ['name' => 'Greek', 'name_bn' => 'গ্রিক', 'code' => 'el', 'native_name' => 'Ελληνικά'],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
