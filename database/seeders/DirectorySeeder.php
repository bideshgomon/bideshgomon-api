<?php

namespace Database\Seeders;

use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\Country;
use Illuminate\Database\Seeder;

class DirectorySeeder extends Seeder
{
    public function run(): void
    {
        $embassyCategory = DirectoryCategory::where('slug', 'embassy')->first();
        $airlineCategory = DirectoryCategory::where('slug', 'airline')->first();
        $trainingCategory = DirectoryCategory::where('slug', 'training-center')->first();
        $medicalCategory = DirectoryCategory::where('slug', 'medical-center')->first();
        
        $bangladesh = Country::where('iso_code_2', 'BD')->first();
        $bangladeshId = $bangladesh ? $bangladesh->id : null;
        
        // Embassies
        $embassies = [
            [
                'directory_category_id' => $embassyCategory->id,
                'name' => 'Embassy of the United Arab Emirates',
                'name_bn' => 'সংযুক্ত আরব আমিরাত দূতাবাস',
                'description' => 'The Embassy of the United Arab Emirates in Dhaka provides consular services including visa applications, passport services, and assistance to UAE nationals.',
                'description_bn' => 'ঢাকায় সংযুক্ত আরব আমিরাতের দূতাবাস ভিসা আবেদন, পাসপোর্ট সেবা এবং সংযুক্ত আরব আমিরাতের নাগরিকদের সহায়তা সহ কনস্যুলার সেবা প্রদান করে।',
                'country_id' => $bangladeshId,
                'city' => 'Dhaka',
                'address' => 'House 12, Road 108, Gulshan 2, Dhaka 1212',
                'phone' => '+880-2-9881112',
                'email' => 'embassy.dhaka@mofa.gov.ae',
                'website' => 'https://www.mofa.gov.ae',
                'latitude' => 23.7939,
                'longitude' => 90.4152,
                'operating_hours' => [
                    'sunday' => '9:00 AM - 4:00 PM',
                    'monday' => '9:00 AM - 4:00 PM',
                    'tuesday' => '9:00 AM - 4:00 PM',
                    'wednesday' => '9:00 AM - 4:00 PM',
                    'thursday' => '9:00 AM - 4:00 PM',
                    'friday' => 'Closed',
                    'saturday' => 'Closed',
                ],
                'services' => ['Visa Processing', 'Passport Services', 'Attestation', 'Consular Assistance'],
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/uaeembassydhaka',
                    'twitter' => 'https://twitter.com/UAEembassyDhaka',
                ],
                'meta_title' => 'UAE Embassy in Dhaka - Visa & Consular Services',
                'meta_description' => 'Contact UAE Embassy in Dhaka for visa applications, passport services, and consular assistance. Operating hours, location, and contact information.',
                'meta_keywords' => 'UAE embassy Dhaka, UAE visa Bangladesh, Dubai visa Dhaka',
                'is_verified' => true,
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'directory_category_id' => $embassyCategory->id,
                'name' => 'Embassy of Saudi Arabia',
                'name_bn' => 'সৌদি আরব দূতাবাস',
                'description' => 'The Royal Embassy of Saudi Arabia in Dhaka handles visa applications for Hajj, Umrah, work permits, and provides consular services.',
                'description_bn' => 'ঢাকায় সৌদি আরবের রাজকীয় দূতাবাস হজ্জ, উমরাহ, কর্ম পারমিট ভিসা আবেদন পরিচালনা করে এবং কনস্যুলার সেবা প্রদান করে।',
                'country_id' => $bangladeshId,
                'city' => 'Dhaka',
                'address' => 'House 4, Road 83, Gulshan 2, Dhaka 1212',
                'phone' => '+880-2-9882781',
                'email' => 'info@mofa.gov.sa',
                'website' => 'https://www.mofa.gov.sa',
                'latitude' => 23.7896,
                'longitude' => 90.4128,
                'operating_hours' => [
                    'sunday' => '9:00 AM - 3:00 PM',
                    'monday' => '9:00 AM - 3:00 PM',
                    'tuesday' => '9:00 AM - 3:00 PM',
                    'wednesday' => '9:00 AM - 3:00 PM',
                    'thursday' => '9:00 AM - 3:00 PM',
                    'friday' => 'Closed',
                    'saturday' => 'Closed',
                ],
                'services' => ['Work Visa', 'Hajj Visa', 'Umrah Visa', 'Passport Services', 'Attestation'],
                'is_verified' => true,
                'is_featured' => true,
                'is_published' => true,
            ],
        ];

        // Airlines
        $airlines = [
            [
                'directory_category_id' => $airlineCategory->id,
                'name' => 'Emirates Airlines',
                'name_bn' => 'এমিরেটস এয়ারলাইন্স',
                'description' => 'Emirates operates daily flights from Dhaka to Dubai with connections to over 150 destinations worldwide. Known for premium service and modern fleet.',
                'description_bn' => 'এমিরেটস ঢাকা থেকে দুবাইতে প্রতিদিন ফ্লাইট পরিচালনা করে এবং বিশ্বব্যাপী ১৫০টিরও বেশি গন্তব্যে সংযোগ প্রদান করে। প্রিমিয়াম সেবা এবং আধুনিক বিমানের জন্য পরিচিত।',
                'country_id' => $bangladeshId,
                'city' => 'Dhaka',
                'address' => 'Gulshan Tower, Plot 32, Road 34, Gulshan 1, Dhaka 1212',
                'phone' => '+880-2-9892121',
                'email' => 'ek.dhaka@emirates.com',
                'website' => 'https://www.emirates.com',
                'latitude' => 23.7806,
                'longitude' => 90.4193,
                'operating_hours' => [
                    'sunday' => '9:00 AM - 6:00 PM',
                    'monday' => '9:00 AM - 6:00 PM',
                    'tuesday' => '9:00 AM - 6:00 PM',
                    'wednesday' => '9:00 AM - 6:00 PM',
                    'thursday' => '9:00 AM - 6:00 PM',
                    'friday' => '10:00 AM - 4:00 PM',
                    'saturday' => '9:00 AM - 6:00 PM',
                ],
                'services' => ['Flight Booking', 'Baggage Services', 'Special Assistance', 'Frequent Flyer Program'],
                'social_media' => [
                    'facebook' => 'https://www.facebook.com/EmiratesBD',
                    'instagram' => 'https://www.instagram.com/emirates',
                ],
                'meta_title' => 'Emirates Airlines Dhaka Office - Flight Booking & Services',
                'meta_description' => 'Book Emirates flights from Dhaka to Dubai and worldwide destinations. Visit our Gulshan office for bookings, baggage services, and customer support.',
                'meta_keywords' => 'Emirates Dhaka, Dubai flight Dhaka, Emirates booking',
                'is_verified' => true,
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'directory_category_id' => $airlineCategory->id,
                'name' => 'Qatar Airways',
                'name_bn' => 'কাতার এয়ারওয়েজ',
                'description' => 'Qatar Airways offers multiple daily flights from Dhaka to Doha with seamless connections to destinations across the globe.',
                'description_bn' => 'কাতার এয়ারওয়েজ ঢাকা থেকে দোহা পর্যন্ত দৈনিক একাধিক ফ্লাইট প্রদান করে এবং বিশ্বব্যাপী গন্তব্যে সহজ সংযোগ প্রদান করে।',
                'country_id' => $bangladeshId,
                'city' => 'Dhaka',
                'address' => 'Sena Kalyan Bhaban, 195 Motijheel C/A, Dhaka 1000',
                'phone' => '+880-2-9558727',
                'email' => 'dhacityo@qatarairways.com.qa',
                'website' => 'https://www.qatarairways.com',
                'latitude' => 23.7325,
                'longitude' => 90.4170,
                'operating_hours' => [
                    'sunday' => '9:00 AM - 6:00 PM',
                    'monday' => '9:00 AM - 6:00 PM',
                    'tuesday' => '9:00 AM - 6:00 PM',
                    'wednesday' => '9:00 AM - 6:00 PM',
                    'thursday' => '9:00 AM - 6:00 PM',
                    'friday' => 'Closed',
                    'saturday' => '9:00 AM - 6:00 PM',
                ],
                'services' => ['Flight Booking', 'Privilege Club', 'Cargo Services', 'Special Needs'],
                'is_verified' => true,
                'is_featured' => true,
                'is_published' => true,
            ],
        ];

        // Training Centers
        $trainingCenters = [
            [
                'directory_category_id' => $trainingCategory->id,
                'name' => 'Bangladesh Overseas Employment & Services Limited (BOESL)',
                'name_bn' => 'বাংলাদেশ প্রবাসী কর্মসংস্থান ও সেবা লিমিটেড',
                'description' => 'Government training center providing pre-departure orientation, language training (Arabic, English), and skill development for overseas workers.',
                'description_bn' => 'সরকারি প্রশিক্ষণ কেন্দ্র যা বিদেশগামী কর্মীদের জন্য প্রস্থান-পূর্ব প্রশিক্ষণ, ভাষা প্রশিক্ষণ (আরবি, ইংরেজি) এবং দক্ষতা উন্নয়ন প্রদান করে।',
                'country_id' => $bangladeshId,
                'city' => 'Dhaka',
                'address' => 'BOESL Complex, Uttara, Dhaka 1230',
                'phone' => '+880-2-8960801',
                'email' => 'info@boesl.gov.bd',
                'website' => 'http://www.boesl.gov.bd',
                'latitude' => 23.8759,
                'longitude' => 90.3795,
                'services' => ['Arabic Language Training', 'English Language Training', 'Pre-Departure Orientation', 'Skill Development'],
                'is_verified' => true,
                'is_featured' => false,
                'is_published' => true,
            ],
        ];

        // Medical Centers
        $medicalCenters = [
            [
                'directory_category_id' => $medicalCategory->id,
                'name' => 'Gamca Medical Center',
                'name_bn' => 'গামকা মেডিকেল সেন্টার',
                'description' => 'Authorized medical examination center for GCC countries (UAE, Saudi Arabia, Kuwait, Qatar, Oman, Bahrain). Provides complete medical tests required for work visas.',
                'description_bn' => 'জিসিসি দেশগুলির (সংযুক্ত আরব আমিরাত, সৌদি আরব, কুয়েত, কাতার, ওমান, বাহরাইন) জন্য অনুমোদিত চিকিৎসা পরীক্ষা কেন্দ্র। কাজের ভিসার জন্য প্রয়োজনীয় সম্পূর্ণ চিকিৎসা পরীক্ষা প্রদান করে।',
                'country_id' => $bangladeshId,
                'city' => 'Dhaka',
                'address' => 'Rupayan Shelford, 23/6 Mirpur Road, Dhaka 1207',
                'phone' => '+880-2-9137555',
                'email' => 'info@gamcadhaka.com',
                'website' => 'https://www.gamcadhaka.com',
                'latitude' => 23.7545,
                'longitude' => 90.3706,
                'operating_hours' => [
                    'sunday' => '8:00 AM - 4:00 PM',
                    'monday' => '8:00 AM - 4:00 PM',
                    'tuesday' => '8:00 AM - 4:00 PM',
                    'wednesday' => '8:00 AM - 4:00 PM',
                    'thursday' => '8:00 AM - 4:00 PM',
                    'friday' => 'Closed',
                    'saturday' => '8:00 AM - 2:00 PM',
                ],
                'services' => ['Blood Tests', 'X-Ray', 'Physical Examination', 'Vaccination', 'Medical Fitness Certificate'],
                'is_verified' => true,
                'is_featured' => true,
                'is_published' => true,
            ],
        ];

        // Insert all directories
        foreach (array_merge($embassies, $airlines, $trainingCenters, $medicalCenters) as $directory) {
            Directory::create($directory);
        }
    }
}
