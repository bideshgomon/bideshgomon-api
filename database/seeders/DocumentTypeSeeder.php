<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds - Bangladesh visa application context
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('document_types')->truncate();
        Schema::enableForeignKeyConstraints();

        $documentTypes = [
            // Identity Documents
            [
                'name' => 'Passport',
                'name_bn' => 'পাসপোর্ট',
                'slug' => 'passport',
                'description' => 'Valid passport with at least 6 months validity',
                'category' => 'Identity',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'National ID Card (NID)',
                'name_bn' => 'জাতীয় পরিচয়পত্র',
                'slug' => 'national-id-card',
                'description' => 'Bangladesh national ID card (both sides)',
                'category' => 'Identity',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Birth Certificate',
                'name_bn' => 'জন্ম নিবন্ধন সনদ',
                'slug' => 'birth-certificate',
                'description' => 'Official birth registration certificate',
                'category' => 'Identity',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Financial Documents
            [
                'name' => 'Bank Statement',
                'name_bn' => 'ব্যাংক স্টেটমেন্ট',
                'slug' => 'bank-statement',
                'description' => 'Last 6 months bank statement showing sufficient funds',
                'category' => 'Financial',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Income Tax Return',
                'name_bn' => 'আয়কর রিটার্ন',
                'slug' => 'income-tax-return',
                'description' => 'Last 2-3 years income tax returns',
                'category' => 'Financial',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Property Documents',
                'name_bn' => 'সম্পত্তির কাগজপত্র',
                'slug' => 'property-documents',
                'description' => 'Land/property ownership documents',
                'category' => 'Financial',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Salary Certificate',
                'name_bn' => 'বেতন সনদ',
                'slug' => 'salary-certificate',
                'description' => 'Employment and salary certificate from employer',
                'category' => 'Financial',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 13,
            ],

            // Academic Documents
            [
                'name' => 'Educational Certificates',
                'name_bn' => 'শিক্ষাগত সনদপত্র',
                'slug' => 'educational-certificates',
                'description' => 'Academic certificates (SSC, HSC, Bachelor, Master, etc.)',
                'category' => 'Academic',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Transcripts',
                'name_bn' => 'ট্রান্সক্রিপ্ট',
                'slug' => 'transcripts',
                'description' => 'Academic transcripts and mark sheets',
                'category' => 'Academic',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 21,
            ],
            [
                'name' => 'Language Test Certificate',
                'name_bn' => 'ভাষা পরীক্ষার সনদ',
                'slug' => 'language-test-certificate',
                'description' => 'IELTS, TOEFL, PTE, or other language proficiency certificates',
                'category' => 'Academic',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 22,
            ],

            // Travel Documents
            [
                'name' => 'Passport Size Photo',
                'name_bn' => 'পাসপোর্ট সাইজ ছবি',
                'slug' => 'passport-photo',
                'description' => 'Recent passport-size photographs (as per country requirements)',
                'category' => 'Travel',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Previous Visa',
                'name_bn' => 'পূর্ববর্তী ভিসা',
                'slug' => 'previous-visa',
                'description' => 'Previous visas and travel history',
                'category' => 'Travel',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 31,
            ],
            [
                'name' => 'Travel Insurance',
                'name_bn' => 'ভ্রমণ বীমা',
                'slug' => 'travel-insurance',
                'description' => 'Travel/health insurance documents',
                'category' => 'Travel',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 32,
            ],
            [
                'name' => 'Flight Itinerary',
                'name_bn' => 'ফ্লাইট সময়সূচী',
                'slug' => 'flight-itinerary',
                'description' => 'Flight booking confirmation or itinerary',
                'category' => 'Travel',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 33,
            ],
            [
                'name' => 'Hotel Booking',
                'name_bn' => 'হোটেল বুকিং',
                'slug' => 'hotel-booking',
                'description' => 'Hotel reservation or accommodation proof',
                'category' => 'Travel',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 34,
            ],

            // Employment Documents
            [
                'name' => 'Job Offer Letter',
                'name_bn' => 'চাকরির প্রস্তাব পত্র',
                'slug' => 'job-offer-letter',
                'description' => 'Official job offer from employer',
                'category' => 'Employment',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'Employment Contract',
                'name_bn' => 'চাকরির চুক্তি',
                'slug' => 'employment-contract',
                'description' => 'Signed employment contract',
                'category' => 'Employment',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 41,
            ],
            [
                'name' => 'Experience Certificate',
                'name_bn' => 'অভিজ্ঞতা সনদ',
                'slug' => 'experience-certificate',
                'description' => 'Work experience certificates from previous employers',
                'category' => 'Employment',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 42,
            ],
            [
                'name' => 'Professional License',
                'name_bn' => 'পেশাদার লাইসেন্স',
                'slug' => 'professional-license',
                'description' => 'Professional certification or license (if applicable)',
                'category' => 'Employment',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 43,
            ],

            // Family/Relationship Documents
            [
                'name' => 'Marriage Certificate',
                'name_bn' => 'বিবাহ সনদ',
                'slug' => 'marriage-certificate',
                'description' => 'Official marriage registration certificate',
                'category' => 'Family',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'Divorce Certificate',
                'name_bn' => 'বিবাহবিচ্ছেদ সনদ',
                'slug' => 'divorce-certificate',
                'description' => 'Divorce decree (if applicable)',
                'category' => 'Family',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 51,
            ],
            [
                'name' => 'Sponsorship Letter',
                'name_bn' => 'স্পন্সরশিপ লেটার',
                'slug' => 'sponsorship-letter',
                'description' => 'Letter from sponsor with financial support proof',
                'category' => 'Family',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 52,
            ],

            // Medical Documents
            [
                'name' => 'Medical Certificate',
                'name_bn' => 'মেডিকেল সার্টিফিকেট',
                'slug' => 'medical-certificate',
                'description' => 'Medical fitness certificate',
                'category' => 'Medical',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 60,
            ],
            [
                'name' => 'Vaccination Certificate',
                'name_bn' => 'টিকা সনদ',
                'slug' => 'vaccination-certificate',
                'description' => 'Vaccination records (COVID-19, Yellow Fever, etc.)',
                'category' => 'Medical',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 61,
            ],

            // Legal Documents
            [
                'name' => 'Police Clearance Certificate',
                'name_bn' => 'পুলিশ ক্লিয়ারেন্স সার্টিফিকেট',
                'slug' => 'police-clearance',
                'description' => 'Police verification certificate (PCC)',
                'category' => 'Legal',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 70,
            ],
            [
                'name' => 'Character Certificate',
                'name_bn' => 'চারিত্রিক সনদপত্র',
                'slug' => 'character-certificate',
                'description' => 'Character/good conduct certificate',
                'category' => 'Legal',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 71,
            ],

            // Other Documents
            [
                'name' => 'Invitation Letter',
                'name_bn' => 'আমন্ত্রণ পত্র',
                'slug' => 'invitation-letter',
                'description' => 'Invitation letter from host/organization',
                'category' => 'Other',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 80,
            ],
            [
                'name' => 'Cover Letter',
                'name_bn' => 'কভার লেটার',
                'slug' => 'cover-letter',
                'description' => 'Cover letter explaining purpose of visit',
                'category' => 'Other',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 81,
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::create($type);
        }

        $this->command->info('Document types seeded successfully: ' . count($documentTypes) . ' entries');
    }
}
