<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobCategory;
use Illuminate\Support\Str;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // IT & Technology
            [
                'name' => 'IT & Technology',
                'name_bn' => 'আইটি ও প্রযুক্তি',
                'description' => 'Information technology, software development, and technical roles',
                'order' => 1,
                'children' => [
                    ['name' => 'Software Development', 'name_bn' => 'সফটওয়্যার উন্নয়ন', 'description' => 'Programming, coding, and software engineering'],
                    ['name' => 'Web Development', 'name_bn' => 'ওয়েব উন্নয়ন', 'description' => 'Frontend, backend, and full-stack web development'],
                    ['name' => 'Mobile App Development', 'name_bn' => 'মোবাইল অ্যাপ উন্নয়ন', 'description' => 'iOS, Android, and cross-platform mobile apps'],
                    ['name' => 'Data Science & AI', 'name_bn' => 'ডেটা সায়েন্স ও এআই', 'description' => 'Machine learning, artificial intelligence, data analysis'],
                    ['name' => 'Network & Systems', 'name_bn' => 'নেটওয়ার্ক ও সিস্টেম', 'description' => 'Network administration, system engineering, DevOps'],
                    ['name' => 'Cybersecurity', 'name_bn' => 'সাইবার নিরাপত্তা', 'description' => 'Information security, ethical hacking, security analysis'],
                    ['name' => 'IT Support', 'name_bn' => 'আইটি সাপোর্ট', 'description' => 'Technical support, help desk, IT maintenance'],
                ],
            ],

            // Healthcare
            [
                'name' => 'Healthcare',
                'name_bn' => 'স্বাস্থ্যসেবা',
                'description' => 'Medical, nursing, and healthcare services',
                'order' => 2,
                'children' => [
                    ['name' => 'Doctors & Physicians', 'name_bn' => 'চিকিৎসক', 'description' => 'Medical doctors, specialists, surgeons'],
                    ['name' => 'Nursing', 'name_bn' => 'নার্সিং', 'description' => 'Registered nurses, nurse practitioners, nursing assistants'],
                    ['name' => 'Pharmacy', 'name_bn' => 'ফার্মেসি', 'description' => 'Pharmacists, pharmacy technicians'],
                    ['name' => 'Medical Lab', 'name_bn' => 'মেডিকেল ল্যাব', 'description' => 'Lab technicians, medical laboratory scientists'],
                    ['name' => 'Physiotherapy', 'name_bn' => 'ফিজিওথেরাপি', 'description' => 'Physical therapists, rehabilitation specialists'],
                    ['name' => 'Dental', 'name_bn' => 'ডেন্টাল', 'description' => 'Dentists, dental hygienists, dental assistants'],
                ],
            ],

            // Construction & Engineering
            [
                'name' => 'Construction',
                'name_bn' => 'নির্মাণ',
                'description' => 'Construction, civil engineering, and building trades',
                'order' => 3,
                'children' => [
                    ['name' => 'Civil Engineering', 'name_bn' => 'সিভিল ইঞ্জিনিয়ারিং', 'description' => 'Civil engineers, structural engineers, project managers'],
                    ['name' => 'Electricians', 'name_bn' => 'ইলেকট্রিশিয়ান', 'description' => 'Electrical work, wiring, maintenance'],
                    ['name' => 'Plumbing', 'name_bn' => 'প্লাম্বিং', 'description' => 'Plumbers, pipefitters, drainage specialists'],
                    ['name' => 'Carpentry', 'name_bn' => 'কাঠমিস্ত্রি', 'description' => 'Carpenters, woodworkers, furniture makers'],
                    ['name' => 'Masonry', 'name_bn' => 'রাজমিস্ত্রি', 'description' => 'Masons, bricklayers, concrete workers'],
                    ['name' => 'Heavy Equipment Operators', 'name_bn' => 'ভারী যন্ত্রপাতি পরিচালক', 'description' => 'Crane operators, excavator operators, bulldozer operators'],
                ],
            ],

            // Hospitality & Tourism
            [
                'name' => 'Hospitality',
                'name_bn' => 'হোটেল ও পর্যটন',
                'description' => 'Hotels, restaurants, tourism, and hospitality services',
                'order' => 4,
                'children' => [
                    ['name' => 'Hotel Management', 'name_bn' => 'হোটেল ম্যানেজমেন্ট', 'description' => 'Hotel managers, front desk, concierge'],
                    ['name' => 'Chefs & Cooks', 'name_bn' => 'শেফ ও রাঁধুনি', 'description' => 'Executive chefs, line cooks, sous chefs'],
                    ['name' => 'Waiters & Servers', 'name_bn' => 'ওয়েটার ও পরিবেশক', 'description' => 'Restaurant servers, food service workers'],
                    ['name' => 'Housekeeping', 'name_bn' => 'হাউসকিপিং', 'description' => 'Housekeepers, room attendants, cleaning staff'],
                    ['name' => 'Tour Guides', 'name_bn' => 'ট্যুর গাইড', 'description' => 'Tourism guides, travel coordinators'],
                    ['name' => 'Bartenders', 'name_bn' => 'বারটেন্ডার', 'description' => 'Bartenders, mixologists, bar staff'],
                ],
            ],

            // Manufacturing
            [
                'name' => 'Manufacturing',
                'name_bn' => 'উৎপাদন',
                'description' => 'Factory work, production, and industrial manufacturing',
                'order' => 5,
                'children' => [
                    ['name' => 'Garment Workers', 'name_bn' => 'গার্মেন্ট শ্রমিক', 'description' => 'Sewing operators, garment factory workers'],
                    ['name' => 'Quality Control', 'name_bn' => 'মান নিয়ন্ত্রণ', 'description' => 'QC inspectors, quality assurance specialists'],
                    ['name' => 'Production Supervisors', 'name_bn' => 'উৎপাদন তত্ত্বাবধায়ক', 'description' => 'Production managers, line supervisors'],
                    ['name' => 'Machine Operators', 'name_bn' => 'যন্ত্র পরিচালক', 'description' => 'CNC operators, machine technicians'],
                    ['name' => 'Assembly Workers', 'name_bn' => 'সমাবেশ শ্রমিক', 'description' => 'Assembly line workers, production assemblers'],
                    ['name' => 'Packaging', 'name_bn' => 'প্যাকেজিং', 'description' => 'Packaging operators, warehouse packers'],
                ],
            ],

            // Education
            [
                'name' => 'Education',
                'name_bn' => 'শিক্ষা',
                'description' => 'Teaching, training, and educational services',
                'order' => 6,
                'children' => [
                    ['name' => 'School Teachers', 'name_bn' => 'স্কুল শিক্ষক', 'description' => 'Primary, secondary school teachers'],
                    ['name' => 'College/University Teachers', 'name_bn' => 'কলেজ/বিশ্ববিদ্যালয় শিক্ষক', 'description' => 'Professors, lecturers, academic staff'],
                    ['name' => 'Private Tutors', 'name_bn' => 'প্রাইভেট টিউটর', 'description' => 'Home tutors, coaching instructors'],
                    ['name' => 'Training & Development', 'name_bn' => 'প্রশিক্ষণ ও উন্নয়ন', 'description' => 'Corporate trainers, skill development instructors'],
                    ['name' => 'Educational Administration', 'name_bn' => 'শিক্ষা প্রশাসন', 'description' => 'School principals, education coordinators'],
                ],
            ],

            // Retail & Sales
            [
                'name' => 'Retail',
                'name_bn' => 'খুচরা বিক্রয়',
                'description' => 'Retail sales, merchandising, and customer service',
                'order' => 7,
                'children' => [
                    ['name' => 'Sales Associates', 'name_bn' => 'বিক্রয় সহকারী', 'description' => 'Retail sales staff, shop assistants'],
                    ['name' => 'Cashiers', 'name_bn' => 'ক্যাশিয়ার', 'description' => 'Cash register operators, checkout staff'],
                    ['name' => 'Store Managers', 'name_bn' => 'স্টোর ম্যানেজার', 'description' => 'Retail managers, shop supervisors'],
                    ['name' => 'Visual Merchandisers', 'name_bn' => 'ভিজ্যুয়াল মার্চেন্ডাইজার', 'description' => 'Display designers, product merchandisers'],
                    ['name' => 'Customer Service', 'name_bn' => 'গ্রাহক সেবা', 'description' => 'Customer support, service representatives'],
                ],
            ],

            // Transportation & Logistics
            [
                'name' => 'Transportation',
                'name_bn' => 'পরিবহন',
                'description' => 'Driving, delivery, logistics, and transportation',
                'order' => 8,
                'children' => [
                    ['name' => 'Drivers', 'name_bn' => 'চালক', 'description' => 'Truck drivers, delivery drivers, bus drivers'],
                    ['name' => 'Warehouse Workers', 'name_bn' => 'গুদাম কর্মী', 'description' => 'Warehouse staff, inventory workers'],
                    ['name' => 'Logistics Coordinators', 'name_bn' => 'লজিস্টিক সমন্বয়কারী', 'description' => 'Supply chain, logistics managers'],
                    ['name' => 'Delivery Personnel', 'name_bn' => 'ডেলিভারি কর্মী', 'description' => 'Couriers, delivery riders, postal workers'],
                    ['name' => 'Fleet Management', 'name_bn' => 'ফ্লিট ম্যানেজমেন্ট', 'description' => 'Fleet managers, transport coordinators'],
                ],
            ],

            // Security
            [
                'name' => 'Security',
                'name_bn' => 'নিরাপত্তা',
                'description' => 'Security services, guards, and safety personnel',
                'order' => 9,
                'children' => [
                    ['name' => 'Security Guards', 'name_bn' => 'নিরাপত্তা প্রহরী', 'description' => 'Security officers, building guards'],
                    ['name' => 'Security Supervisors', 'name_bn' => 'নিরাপত্তা তত্ত্বাবধায়ক', 'description' => 'Security managers, head guards'],
                    ['name' => 'CCTV Operators', 'name_bn' => 'সিসিটিভি পরিচালক', 'description' => 'Surveillance operators, monitoring staff'],
                    ['name' => 'Fire & Safety', 'name_bn' => 'অগ্নি ও নিরাপত্তা', 'description' => 'Fire safety officers, safety inspectors'],
                ],
            ],

            // Agriculture
            [
                'name' => 'Agriculture',
                'name_bn' => 'কৃষি',
                'description' => 'Farming, agricultural work, and rural development',
                'order' => 10,
                'children' => [
                    ['name' => 'Farm Workers', 'name_bn' => 'খামার শ্রমিক', 'description' => 'Agricultural laborers, field workers'],
                    ['name' => 'Agricultural Engineers', 'name_bn' => 'কৃষি প্রকৌশলী', 'description' => 'Agro engineers, farm mechanization specialists'],
                    ['name' => 'Livestock Workers', 'name_bn' => 'পশুপালন কর্মী', 'description' => 'Animal husbandry, dairy farm workers'],
                    ['name' => 'Fishery Workers', 'name_bn' => 'মৎস্য শ্রমিক', 'description' => 'Fish farm workers, aquaculture specialists'],
                ],
            ],

            // Automotive
            [
                'name' => 'Automotive',
                'name_bn' => 'অটোমোটিভ',
                'description' => 'Vehicle maintenance, repair, and automotive services',
                'order' => 11,
                'children' => [
                    ['name' => 'Mechanics', 'name_bn' => 'মেকানিক', 'description' => 'Auto mechanics, car technicians'],
                    ['name' => 'Body Shop Technicians', 'name_bn' => 'বডি শপ টেকনিশিয়ান', 'description' => 'Auto body repair, paint technicians'],
                    ['name' => 'Service Advisors', 'name_bn' => 'সার্ভিস উপদেষ্টা', 'description' => 'Service coordinators, customer advisors'],
                    ['name' => 'Parts Specialists', 'name_bn' => 'যন্ত্রাংশ বিশেষজ্ঞ', 'description' => 'Auto parts sales, inventory specialists'],
                ],
            ],

            // Domestic Help
            [
                'name' => 'Domestic Services',
                'name_bn' => 'গৃহস্থালী সেবা',
                'description' => 'Household help, domestic workers, and personal services',
                'order' => 12,
                'children' => [
                    ['name' => 'Housemaids', 'name_bn' => 'গৃহকর্মী', 'description' => 'Domestic helpers, housekeepers'],
                    ['name' => 'Nannies & Babysitters', 'name_bn' => 'শিশু পরিচর্যাকারী', 'description' => 'Childcare providers, nannies'],
                    ['name' => 'Elderly Care', 'name_bn' => 'বয়স্ক যত্ন', 'description' => 'Elderly caretakers, nursing aides'],
                    ['name' => 'Gardeners', 'name_bn' => 'মালী', 'description' => 'Garden maintenance, landscaping workers'],
                    ['name' => 'Cooks', 'name_bn' => 'রাঁধুনি', 'description' => 'Personal cooks, home chefs'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            $categoryData['slug'] = Str::slug($categoryData['name']);
            $categoryData['is_active'] = true;
            
            $parent = JobCategory::create($categoryData);
            
            if (!empty($children)) {
                $order = 1;
                foreach ($children as $childData) {
                    $childData['parent_id'] = $parent->id;
                    $childData['slug'] = Str::slug($childData['name']);
                    $childData['order'] = $order++;
                    $childData['is_active'] = true;
                    JobCategory::create($childData);
                }
            }
        }

        $this->command->info('Job categories seeded successfully!');
        $this->command->info('Total parent categories: ' . JobCategory::whereNull('parent_id')->count());
        $this->command->info('Total child categories: ' . JobCategory::whereNotNull('parent_id')->count());
    }
}
