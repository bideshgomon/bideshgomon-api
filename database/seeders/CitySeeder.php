<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get countries for foreign keys
        $bangladesh = Country::where('iso_code_2', 'BD')->first();
        $india = Country::where('iso_code_2', 'IN')->first();
        $usa = Country::where('iso_code_2', 'US')->first();
        $uk = Country::where('iso_code_2', 'GB')->first();
        $canada = Country::where('iso_code_2', 'CA')->first();
        $australia = Country::where('iso_code_2', 'AU')->first();
        $saudi = Country::where('iso_code_2', 'SA')->first();
        $uae = Country::where('iso_code_2', 'AE')->first();
        $malaysia = Country::where('iso_code_2', 'MY')->first();
        $singapore = Country::where('iso_code_2', 'SG')->first();
        
        $cities = [];
        
        // Bangladesh cities (all 8 divisions)
        if ($bangladesh) {
            $cities = array_merge($cities, [
                ['country_id' => $bangladesh->id, 'name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'state_province' => 'Dhaka', 'timezone' => 'Asia/Dhaka', 'latitude' => 23.8103, 'longitude' => 90.4125, 'is_capital' => true],
                ['country_id' => $bangladesh->id, 'name' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম', 'state_province' => 'Chittagong', 'timezone' => 'Asia/Dhaka', 'latitude' => 22.3569, 'longitude' => 91.7832, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Sylhet', 'name_bn' => 'সিলেট', 'state_province' => 'Sylhet', 'timezone' => 'Asia/Dhaka', 'latitude' => 24.8949, 'longitude' => 91.8687, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Rajshahi', 'name_bn' => 'রাজশাহী', 'state_province' => 'Rajshahi', 'timezone' => 'Asia/Dhaka', 'latitude' => 24.3745, 'longitude' => 88.6042, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Khulna', 'name_bn' => 'খুলনা', 'state_province' => 'Khulna', 'timezone' => 'Asia/Dhaka', 'latitude' => 22.8456, 'longitude' => 89.5403, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Barisal', 'name_bn' => 'বরিশাল', 'state_province' => 'Barisal', 'timezone' => 'Asia/Dhaka', 'latitude' => 22.7010, 'longitude' => 90.3535, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Rangpur', 'name_bn' => 'রংপুর', 'state_province' => 'Rangpur', 'timezone' => 'Asia/Dhaka', 'latitude' => 25.7439, 'longitude' => 89.2752, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ', 'state_province' => 'Mymensingh', 'timezone' => 'Asia/Dhaka', 'latitude' => 24.7471, 'longitude' => 90.4203, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Comilla', 'name_bn' => 'কুমিল্লা', 'state_province' => 'Chittagong', 'timezone' => 'Asia/Dhaka', 'latitude' => 23.4607, 'longitude' => 91.1809, 'is_capital' => false],
                ['country_id' => $bangladesh->id, 'name' => 'Gazipur', 'name_bn' => 'গাজীপুর', 'state_province' => 'Dhaka', 'timezone' => 'Asia/Dhaka', 'latitude' => 23.9999, 'longitude' => 90.4203, 'is_capital' => false],
            ]);
        }
        
        // Major Indian cities
        if ($india) {
            $cities = array_merge($cities, [
                ['country_id' => $india->id, 'name' => 'New Delhi', 'name_bn' => 'নয়া দিল্লি', 'state_province' => 'Delhi', 'timezone' => 'Asia/Kolkata', 'latitude' => 28.6139, 'longitude' => 77.2090, 'is_capital' => true],
                ['country_id' => $india->id, 'name' => 'Mumbai', 'name_bn' => 'মুম্বাই', 'state_province' => 'Maharashtra', 'timezone' => 'Asia/Kolkata', 'latitude' => 19.0760, 'longitude' => 72.8777, 'is_capital' => false],
                ['country_id' => $india->id, 'name' => 'Kolkata', 'name_bn' => 'কলকাতা', 'state_province' => 'West Bengal', 'timezone' => 'Asia/Kolkata', 'latitude' => 22.5726, 'longitude' => 88.3639, 'is_capital' => false],
                ['country_id' => $india->id, 'name' => 'Bengaluru', 'name_bn' => 'বেঙ্গালুরু', 'state_province' => 'Karnataka', 'timezone' => 'Asia/Kolkata', 'latitude' => 12.9716, 'longitude' => 77.5946, 'is_capital' => false],
                ['country_id' => $india->id, 'name' => 'Chennai', 'name_bn' => 'চেন্নাই', 'state_province' => 'Tamil Nadu', 'timezone' => 'Asia/Kolkata', 'latitude' => 13.0827, 'longitude' => 80.2707, 'is_capital' => false],
                ['country_id' => $india->id, 'name' => 'Hyderabad', 'name_bn' => 'হায়দরাবাদ', 'state_province' => 'Telangana', 'timezone' => 'Asia/Kolkata', 'latitude' => 17.3850, 'longitude' => 78.4867, 'is_capital' => false],
            ]);
        }
        
        // USA cities
        if ($usa) {
            $cities = array_merge($cities, [
                ['country_id' => $usa->id, 'name' => 'Washington DC', 'name_bn' => 'ওয়াশিংটন ডিসি', 'state_province' => 'District of Columbia', 'timezone' => 'America/New_York', 'latitude' => 38.9072, 'longitude' => -77.0369, 'is_capital' => true],
                ['country_id' => $usa->id, 'name' => 'New York', 'name_bn' => 'নিউ ইয়র্ক', 'state_province' => 'New York', 'timezone' => 'America/New_York', 'latitude' => 40.7128, 'longitude' => -74.0060, 'is_capital' => false],
                ['country_id' => $usa->id, 'name' => 'Los Angeles', 'name_bn' => 'লস অ্যাঞ্জেলেস', 'state_province' => 'California', 'timezone' => 'America/Los_Angeles', 'latitude' => 34.0522, 'longitude' => -118.2437, 'is_capital' => false],
                ['country_id' => $usa->id, 'name' => 'Chicago', 'name_bn' => 'শিকাগো', 'state_province' => 'Illinois', 'timezone' => 'America/Chicago', 'latitude' => 41.8781, 'longitude' => -87.6298, 'is_capital' => false],
                ['country_id' => $usa->id, 'name' => 'Houston', 'name_bn' => 'হিউস্টন', 'state_province' => 'Texas', 'timezone' => 'America/Chicago', 'latitude' => 29.7604, 'longitude' => -95.3698, 'is_capital' => false],
            ]);
        }
        
        // UK cities
        if ($uk) {
            $cities = array_merge($cities, [
                ['country_id' => $uk->id, 'name' => 'London', 'name_bn' => 'লন্ডন', 'state_province' => 'England', 'timezone' => 'Europe/London', 'latitude' => 51.5074, 'longitude' => -0.1278, 'is_capital' => true],
                ['country_id' => $uk->id, 'name' => 'Manchester', 'name_bn' => 'ম্যানচেস্টার', 'state_province' => 'England', 'timezone' => 'Europe/London', 'latitude' => 53.4808, 'longitude' => -2.2426, 'is_capital' => false],
                ['country_id' => $uk->id, 'name' => 'Birmingham', 'name_bn' => 'বার্মিংহাম', 'state_province' => 'England', 'timezone' => 'Europe/London', 'latitude' => 52.4862, 'longitude' => -1.8904, 'is_capital' => false],
            ]);
        }
        
        // Canada cities
        if ($canada) {
            $cities = array_merge($cities, [
                ['country_id' => $canada->id, 'name' => 'Ottawa', 'name_bn' => 'অটোয়া', 'state_province' => 'Ontario', 'timezone' => 'America/Toronto', 'latitude' => 45.4215, 'longitude' => -75.6972, 'is_capital' => true],
                ['country_id' => $canada->id, 'name' => 'Toronto', 'name_bn' => 'টরন্টো', 'state_province' => 'Ontario', 'timezone' => 'America/Toronto', 'latitude' => 43.6532, 'longitude' => -79.3832, 'is_capital' => false],
                ['country_id' => $canada->id, 'name' => 'Vancouver', 'name_bn' => 'ভ্যাঙ্কুভার', 'state_province' => 'British Columbia', 'timezone' => 'America/Vancouver', 'latitude' => 49.2827, 'longitude' => -123.1207, 'is_capital' => false],
            ]);
        }
        
        // Australia cities
        if ($australia) {
            $cities = array_merge($cities, [
                ['country_id' => $australia->id, 'name' => 'Canberra', 'name_bn' => 'ক্যানবেরা', 'state_province' => 'Australian Capital Territory', 'timezone' => 'Australia/Sydney', 'latitude' => -35.2809, 'longitude' => 149.1300, 'is_capital' => true],
                ['country_id' => $australia->id, 'name' => 'Sydney', 'name_bn' => 'সিডনি', 'state_province' => 'New South Wales', 'timezone' => 'Australia/Sydney', 'latitude' => -33.8688, 'longitude' => 151.2093, 'is_capital' => false],
                ['country_id' => $australia->id, 'name' => 'Melbourne', 'name_bn' => 'মেলবোর্ন', 'state_province' => 'Victoria', 'timezone' => 'Australia/Melbourne', 'latitude' => -37.8136, 'longitude' => 144.9631, 'is_capital' => false],
            ]);
        }
        
        // Saudi Arabia cities
        if ($saudi) {
            $cities = array_merge($cities, [
                ['country_id' => $saudi->id, 'name' => 'Riyadh', 'name_bn' => 'রিয়াদ', 'state_province' => 'Riyadh', 'timezone' => 'Asia/Riyadh', 'latitude' => 24.7136, 'longitude' => 46.6753, 'is_capital' => true],
                ['country_id' => $saudi->id, 'name' => 'Jeddah', 'name_bn' => 'জেদ্দা', 'state_province' => 'Makkah', 'timezone' => 'Asia/Riyadh', 'latitude' => 21.4858, 'longitude' => 39.1925, 'is_capital' => false],
                ['country_id' => $saudi->id, 'name' => 'Dammam', 'name_bn' => 'দাম্মাম', 'state_province' => 'Eastern Province', 'timezone' => 'Asia/Riyadh', 'latitude' => 26.4207, 'longitude' => 50.0888, 'is_capital' => false],
            ]);
        }
        
        // UAE cities
        if ($uae) {
            $cities = array_merge($cities, [
                ['country_id' => $uae->id, 'name' => 'Abu Dhabi', 'name_bn' => 'আবুধাবি', 'state_province' => 'Abu Dhabi', 'timezone' => 'Asia/Dubai', 'latitude' => 24.4539, 'longitude' => 54.3773, 'is_capital' => true],
                ['country_id' => $uae->id, 'name' => 'Dubai', 'name_bn' => 'দুবাই', 'state_province' => 'Dubai', 'timezone' => 'Asia/Dubai', 'latitude' => 25.2048, 'longitude' => 55.2708, 'is_capital' => false],
                ['country_id' => $uae->id, 'name' => 'Sharjah', 'name_bn' => 'শারজাহ', 'state_province' => 'Sharjah', 'timezone' => 'Asia/Dubai', 'latitude' => 25.3463, 'longitude' => 55.4209, 'is_capital' => false],
            ]);
        }
        
        // Malaysia cities
        if ($malaysia) {
            $cities = array_merge($cities, [
                ['country_id' => $malaysia->id, 'name' => 'Kuala Lumpur', 'name_bn' => 'কুয়ালালামপুর', 'state_province' => 'Federal Territory', 'timezone' => 'Asia/Kuala_Lumpur', 'latitude' => 3.1390, 'longitude' => 101.6869, 'is_capital' => true],
            ]);
        }
        
        // Singapore
        if ($singapore) {
            $cities = array_merge($cities, [
                ['country_id' => $singapore->id, 'name' => 'Singapore', 'name_bn' => 'সিঙ্গাপুর', 'state_province' => null, 'timezone' => 'Asia/Singapore', 'latitude' => 1.3521, 'longitude' => 103.8198, 'is_capital' => true],
            ]);
        }
        
        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
