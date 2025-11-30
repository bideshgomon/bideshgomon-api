<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BangladeshMiddleEastSkillsSeeder extends Seeder
{
    /**
     * Seed skills relevant for Bangladeshi workers going to Middle East
     * Focus: Construction, Hospitality, Healthcare, Domestic Work, Manufacturing
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate existing skills
        DB::table('skills')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $skills = [
            // ==========================================
            // CONSTRUCTION & BUILDING (Middle East Focus)
            // ==========================================
            ['name' => 'Mason', 'name_bn' => 'রাজমিস্ত্রি', 'category' => 'Construction', 'slug' => 'mason'],
            ['name' => 'Carpenter', 'name_bn' => 'সুতার', 'category' => 'Construction', 'slug' => 'carpenter'],
            ['name' => 'Welder', 'name_bn' => 'ওয়েল্ডার', 'category' => 'Construction', 'slug' => 'welder'],
            ['name' => 'Electrician', 'name_bn' => 'ইলেকট্রিশিয়ান', 'category' => 'Construction', 'slug' => 'electrician'],
            ['name' => 'Plumber', 'name_bn' => 'প্লাম্বার', 'category' => 'Construction', 'slug' => 'plumber'],
            ['name' => 'Painter', 'name_bn' => 'পেইন্টার', 'category' => 'Construction', 'slug' => 'painter'],
            ['name' => 'Steel Fixer', 'name_bn' => 'স্টিল ফিক্সার', 'category' => 'Construction', 'slug' => 'steel-fixer'],
            ['name' => 'Tile Worker', 'name_bn' => 'টাইল ওয়ার্কার', 'category' => 'Construction', 'slug' => 'tile-worker'],
            ['name' => 'Scaffolder', 'name_bn' => 'স্ক্যাফোল্ডার', 'category' => 'Construction', 'slug' => 'scaffolder'],
            ['name' => 'Heavy Equipment Operator', 'name_bn' => 'হেভি ইকুইপমেন্ট অপারেটর', 'category' => 'Construction', 'slug' => 'heavy-equipment-operator'],
            ['name' => 'Site Supervisor', 'name_bn' => 'সাইট সুপারভাইজার', 'category' => 'Construction', 'slug' => 'site-supervisor'],
            ['name' => 'General Labor', 'name_bn' => 'সাধারণ শ্রমিক', 'category' => 'Construction', 'slug' => 'general-labor'],
            
            // ==========================================
            // HOSPITALITY (Hotels, Restaurants, Catering)
            // ==========================================
            ['name' => 'Chef', 'name_bn' => 'রাঁধুনি', 'category' => 'Hospitality', 'slug' => 'chef'],
            ['name' => 'Cook', 'name_bn' => 'কুক', 'category' => 'Hospitality', 'slug' => 'cook'],
            ['name' => 'Waiter', 'name_bn' => 'ওয়েটার', 'category' => 'Hospitality', 'slug' => 'waiter'],
            ['name' => 'Bartender', 'name_bn' => 'বারটেন্ডার', 'category' => 'Hospitality', 'slug' => 'bartender'],
            ['name' => 'Hotel Receptionist', 'name_bn' => 'হোটেল রিসেপশনিস্ট', 'category' => 'Hospitality', 'slug' => 'hotel-receptionist'],
            ['name' => 'Housekeeping', 'name_bn' => 'হাউসকিপিং', 'category' => 'Hospitality', 'slug' => 'housekeeping'],
            ['name' => 'Room Attendant', 'name_bn' => 'রুম অ্যাটেন্ডেন্ট', 'category' => 'Hospitality', 'slug' => 'room-attendant'],
            ['name' => 'Laundry Worker', 'name_bn' => 'লন্ড্রি ওয়ার্কার', 'category' => 'Hospitality', 'slug' => 'laundry-worker'],
            ['name' => 'Kitchen Helper', 'name_bn' => 'কিচেন হেল্পার', 'category' => 'Hospitality', 'slug' => 'kitchen-helper'],
            ['name' => 'Restaurant Manager', 'name_bn' => 'রেস্টুরেন্ট ম্যানেজার', 'category' => 'Hospitality', 'slug' => 'restaurant-manager'],
            
            // ==========================================
            // HEALTHCARE & NURSING
            // ==========================================
            ['name' => 'Nurse', 'name_bn' => 'নার্স', 'category' => 'Healthcare', 'slug' => 'nurse'],
            ['name' => 'Medical Laboratory Technician', 'name_bn' => 'মেডিক্যাল ল্যাব টেকনিশিয়ান', 'category' => 'Healthcare', 'slug' => 'medical-lab-technician'],
            ['name' => 'Pharmacist', 'name_bn' => 'ফার্মাসিস্ট', 'category' => 'Healthcare', 'slug' => 'pharmacist'],
            ['name' => 'Physiotherapist', 'name_bn' => 'ফিজিওথেরাপিস্ট', 'category' => 'Healthcare', 'slug' => 'physiotherapist'],
            ['name' => 'Caregiver', 'name_bn' => 'কেয়ারগিভার', 'category' => 'Healthcare', 'slug' => 'caregiver'],
            ['name' => 'Patient Care Assistant', 'name_bn' => 'পেশেন্ট কেয়ার অ্যাসিস্ট্যান্ট', 'category' => 'Healthcare', 'slug' => 'patient-care-assistant'],
            ['name' => 'Home Care Nurse', 'name_bn' => 'হোম কেয়ার নার্স', 'category' => 'Healthcare', 'slug' => 'home-care-nurse'],
            
            // ==========================================
            // DOMESTIC & HOUSEHOLD SERVICES
            // ==========================================
            ['name' => 'Housemaid', 'name_bn' => 'গৃহকর্মী', 'category' => 'Domestic Services', 'slug' => 'housemaid'],
            ['name' => 'Nanny', 'name_bn' => 'নানী', 'category' => 'Domestic Services', 'slug' => 'nanny'],
            ['name' => 'Babysitter', 'name_bn' => 'বেবিসিটার', 'category' => 'Domestic Services', 'slug' => 'babysitter'],
            ['name' => 'Cleaner', 'name_bn' => 'ক্লিনার', 'category' => 'Domestic Services', 'slug' => 'cleaner'],
            ['name' => 'Gardener', 'name_bn' => 'মালী', 'category' => 'Domestic Services', 'slug' => 'gardener'],
            ['name' => 'Driver', 'name_bn' => 'ড্রাইভার', 'category' => 'Domestic Services', 'slug' => 'driver'],
            ['name' => 'Personal Driver', 'name_bn' => 'ব্যক্তিগত ড্রাইভার', 'category' => 'Domestic Services', 'slug' => 'personal-driver'],
            ['name' => 'Cook (Household)', 'name_bn' => 'রাঁধুনি (পারিবারিক)', 'category' => 'Domestic Services', 'slug' => 'cook-household'],
            
            // ==========================================
            // MANUFACTURING & FACTORY
            // ==========================================
            ['name' => 'Garment Worker', 'name_bn' => 'গার্মেন্ট শ্রমিক', 'category' => 'Manufacturing', 'slug' => 'garment-worker'],
            ['name' => 'Sewing Machine Operator', 'name_bn' => 'সেলাই মেশিন অপারেটর', 'category' => 'Manufacturing', 'slug' => 'sewing-machine-operator'],
            ['name' => 'Quality Control Inspector', 'name_bn' => 'কোয়ালিটি কন্ট্রোল ইন্সপেক্টর', 'category' => 'Manufacturing', 'slug' => 'quality-control-inspector'],
            ['name' => 'Production Supervisor', 'name_bn' => 'প্রোডাকশন সুপারভাইজার', 'category' => 'Manufacturing', 'slug' => 'production-supervisor'],
            ['name' => 'Machine Operator', 'name_bn' => 'মেশিন অপারেটর', 'category' => 'Manufacturing', 'slug' => 'machine-operator'],
            ['name' => 'Warehouse Worker', 'name_bn' => 'গুদাম শ্রমিক', 'category' => 'Manufacturing', 'slug' => 'warehouse-worker'],
            ['name' => 'Forklift Operator', 'name_bn' => 'ফর্কলিফট অপারেটর', 'category' => 'Manufacturing', 'slug' => 'forklift-operator'],
            
            // ==========================================
            // AUTOMOTIVE & MECHANICAL
            // ==========================================
            ['name' => 'Auto Mechanic', 'name_bn' => 'অটো মেকানিক', 'category' => 'Automotive', 'slug' => 'auto-mechanic'],
            ['name' => 'Diesel Mechanic', 'name_bn' => 'ডিজেল মেকানিক', 'category' => 'Automotive', 'slug' => 'diesel-mechanic'],
            ['name' => 'AC Technician', 'name_bn' => 'এসি টেকনিশিয়ান', 'category' => 'Automotive', 'slug' => 'ac-technician'],
            ['name' => 'Car Washer', 'name_bn' => 'কার ওয়াশার', 'category' => 'Automotive', 'slug' => 'car-washer'],
            ['name' => 'Heavy Vehicle Driver', 'name_bn' => 'হেভি ভেহিক্যাল ড্রাইভার', 'category' => 'Automotive', 'slug' => 'heavy-vehicle-driver'],
            ['name' => 'Truck Driver', 'name_bn' => 'ট্রাক ড্রাইভার', 'category' => 'Automotive', 'slug' => 'truck-driver'],
            
            // ==========================================
            // RETAIL & SALES
            // ==========================================
            ['name' => 'Salesperson', 'name_bn' => 'সেলসপারসন', 'category' => 'Retail', 'slug' => 'salesperson'],
            ['name' => 'Cashier', 'name_bn' => 'ক্যাশিয়ার', 'category' => 'Retail', 'slug' => 'cashier'],
            ['name' => 'Store Keeper', 'name_bn' => 'স্টোর কিপার', 'category' => 'Retail', 'slug' => 'store-keeper'],
            ['name' => 'Supermarket Worker', 'name_bn' => 'সুপারমার্কেট ওয়ার্কার', 'category' => 'Retail', 'slug' => 'supermarket-worker'],
            ['name' => 'Customer Service', 'name_bn' => 'কাস্টমার সার্ভিস', 'category' => 'Retail', 'slug' => 'customer-service'],
            
            // ==========================================
            // BEAUTY & PERSONAL CARE
            // ==========================================
            ['name' => 'Barber', 'name_bn' => 'নাপিত', 'category' => 'Beauty & Personal Care', 'slug' => 'barber'],
            ['name' => 'Hair Stylist', 'name_bn' => 'হেয়ার স্টাইলিস্ট', 'category' => 'Beauty & Personal Care', 'slug' => 'hair-stylist'],
            ['name' => 'Beautician', 'name_bn' => 'বিউটিশিয়ান', 'category' => 'Beauty & Personal Care', 'slug' => 'beautician'],
            ['name' => 'Spa Therapist', 'name_bn' => 'স্পা থেরাপিস্ট', 'category' => 'Beauty & Personal Care', 'slug' => 'spa-therapist'],
            
            // ==========================================
            // AGRICULTURE & FARMING
            // ==========================================
            ['name' => 'Farm Worker', 'name_bn' => 'খামার শ্রমিক', 'category' => 'Agriculture', 'slug' => 'farm-worker'],
            ['name' => 'Animal Caretaker', 'name_bn' => 'পশু পরিচর্যাকারী', 'category' => 'Agriculture', 'slug' => 'animal-caretaker'],
            ['name' => 'Livestock Handler', 'name_bn' => 'গবাদিপশু হ্যান্ডলার', 'category' => 'Agriculture', 'slug' => 'livestock-handler'],
            
            // ==========================================
            // OFFICE & ADMINISTRATION
            // ==========================================
            ['name' => 'Office Assistant', 'name_bn' => 'অফিস অ্যাসিস্ট্যান্ট', 'category' => 'Office', 'slug' => 'office-assistant'],
            ['name' => 'Data Entry Operator', 'name_bn' => 'ডাটা এন্ট্রি অপারেটর', 'category' => 'Office', 'slug' => 'data-entry-operator'],
            ['name' => 'Accountant', 'name_bn' => 'হিসাবরক্ষক', 'category' => 'Office', 'slug' => 'accountant'],
            ['name' => 'Receptionist', 'name_bn' => 'রিসেপশনিস্ট', 'category' => 'Office', 'slug' => 'receptionist'],
            ['name' => 'Typist', 'name_bn' => 'টাইপিস্ট', 'category' => 'Office', 'slug' => 'typist'],
            
            // ==========================================
            // SECURITY & SAFETY
            // ==========================================
            ['name' => 'Security Guard', 'name_bn' => 'সিকিউরিটি গার্ড', 'category' => 'Security', 'slug' => 'security-guard'],
            ['name' => 'Watchman', 'name_bn' => 'প্রহরী', 'category' => 'Security', 'slug' => 'watchman'],
            
            // ==========================================
            // TECHNOLOGY (Modern Skills)
            // ==========================================
            ['name' => 'Computer Operator', 'name_bn' => 'কম্পিউটার অপারেটর', 'category' => 'Technology', 'slug' => 'computer-operator'],
            ['name' => 'IT Support', 'name_bn' => 'আইটি সাপোর্ট', 'category' => 'Technology', 'slug' => 'it-support'],
            ['name' => 'Graphic Designer', 'name_bn' => 'গ্রাফিক ডিজাইনার', 'category' => 'Technology', 'slug' => 'graphic-designer'],
            ['name' => 'Web Developer', 'name_bn' => 'ওয়েব ডেভেলপার', 'category' => 'Technology', 'slug' => 'web-developer'],
        ];

        foreach ($skills as $skill) {
            Skill::create([
                'name' => $skill['name'],
                'name_bn' => $skill['name_bn'],
                'category' => $skill['category'],
                'slug' => $skill['slug'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Seeded ' . count($skills) . ' Bangladesh/Middle East focused skills with Bengali names');
    }
}
