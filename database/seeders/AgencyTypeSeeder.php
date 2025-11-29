<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgencyType;
use Illuminate\Support\Str;

class AgencyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencyTypes = [
            // Type 1: Visa & Immigration Specialist
            [
                'name' => 'Visa & Immigration Specialist',
                'slug' => 'visa-immigration-specialist',
                'icon' => 'DocumentCheckIcon',
                'color' => 'blue',
                'description' => 'Full-service visa processing and immigration consultancy. Handles tourist, student, work, and permanent residence visas across multiple countries.',
                'allowed_service_modules' => [
                    'tourist-visa',
                    'student-visa',
                    'work-visa',
                    'business-visa',
                    'medical-visa',
                    'family-visa',
                    'permanent-residence',
                ],
                'required_certifications' => [
                    'Immigration Consultant License',
                    'ICCRC Registration (Canada)',
                    'OISC Registration (UK)',
                    'MARA Registration (Australia)',
                ],
                'capabilities' => [
                    'process_visa_applications',
                    'document_verification',
                    'embassy_representation',
                    'appeal_services',
                    'status_tracking',
                    'country_expertise',
                ],
                'default_commission_rate' => 18.00,
                'min_commission_rate' => 15.00,
                'max_commission_rate' => 25.00,
                'can_set_own_pricing' => true,
                'requires_verification' => true,
                'requires_insurance' => true,
                'can_manage_team' => true,
                'can_create_resources' => false,
                'needs_country_assignment' => true,
                'is_active' => true,
                'display_order' => 1,
            ],

            // Type 2: Education Consultant
            [
                'name' => 'Education Consultant',
                'slug' => 'education-consultant',
                'icon' => 'AcademicCapIcon',
                'color' => 'purple',
                'description' => 'University admissions, course selection, and student visa services. Exclusive partnerships with universities and language schools worldwide.',
                'allowed_service_modules' => [
                    'student-visa',
                    'university-admission',
                    'language-course',
                    'scholarship-application',
                    'course-counseling',
                ],
                'required_certifications' => [
                    'Education Consultant License',
                    'British Council Certification',
                    'AIRC Certification',
                    'University Partnership Agreements',
                ],
                'capabilities' => [
                    'university_partnerships',
                    'course_counseling',
                    'admission_processing',
                    'scholarship_applications',
                    'create_university_resources',
                    'create_language_center_resources',
                    'student_visa_support',
                    'accommodation_services',
                ],
                'default_commission_rate' => 20.00,
                'min_commission_rate' => 15.00,
                'max_commission_rate' => 30.00,
                'can_set_own_pricing' => true,
                'requires_verification' => true,
                'requires_insurance' => false,
                'can_manage_team' => true,
                'can_create_resources' => true, // Can add universities, schools
                'needs_country_assignment' => false,
                'is_active' => true,
                'display_order' => 2,
            ],

            // Type 3: Recruitment & Job Placement Agency
            [
                'name' => 'Recruitment & Job Placement',
                'slug' => 'recruitment-job-placement',
                'icon' => 'BriefcaseIcon',
                'color' => 'green',
                'description' => 'International job placement and work visa processing. Direct employer connections and skill assessment services for foreign workers.',
                'allowed_service_modules' => [
                    'work-visa',
                    'job-placement',
                    'skill-assessment',
                    'employer-nomination',
                    'cv-building',
                    'job-matching',
                ],
                'required_certifications' => [
                    'Recruitment Agency License',
                    'Ministry of Manpower Registration',
                    'POEA License (Philippines)',
                    'Employment Agency License',
                ],
                'capabilities' => [
                    'employer_network',
                    'job_matching',
                    'skill_assessment',
                    'cv_creation',
                    'work_visa_processing',
                    'create_job_portal_resources',
                    'pre_departure_training',
                    'contract_negotiation',
                ],
                'default_commission_rate' => 22.00,
                'min_commission_rate' => 18.00,
                'max_commission_rate' => 30.00,
                'can_set_own_pricing' => true,
                'requires_verification' => true,
                'requires_insurance' => true,
                'can_manage_team' => true,
                'can_create_resources' => true, // Can add job portals, employers
                'needs_country_assignment' => true,
                'is_active' => true,
                'display_order' => 3,
            ],

            // Type 4: Travel & Tourism Agency
            [
                'name' => 'Travel & Tourism Agency',
                'slug' => 'travel-tourism-agency',
                'icon' => 'GlobeAltIcon',
                'color' => 'orange',
                'description' => 'Complete travel packages including flight booking, hotel reservations, tourist visa, and travel insurance for domestic and international destinations.',
                'allowed_service_modules' => [
                    'tourist-visa',
                    'flight-booking',
                    'hotel-booking',
                    'travel-insurance',
                    'tour-packages',
                    'transport-rental',
                ],
                'required_certifications' => [
                    'Travel Agency License',
                    'IATA Certification',
                    'Tour Operator License',
                    'Tourism Ministry Registration',
                ],
                'capabilities' => [
                    'flight_booking_system',
                    'hotel_reservation',
                    'tour_package_creation',
                    'visa_support',
                    'travel_insurance_sales',
                    'group_bookings',
                    'itinerary_planning',
                    'transport_arrangement',
                ],
                'default_commission_rate' => 12.00,
                'min_commission_rate' => 8.00,
                'max_commission_rate' => 18.00,
                'can_set_own_pricing' => true,
                'requires_verification' => true,
                'requires_insurance' => false,
                'can_manage_team' => true,
                'can_create_resources' => false,
                'needs_country_assignment' => false,
                'is_active' => true,
                'display_order' => 4,
            ],

            // Type 5: Multi-Service Consultancy
            [
                'name' => 'Multi-Service Consultancy',
                'slug' => 'multi-service-consultancy',
                'icon' => 'BuildingOfficeIcon',
                'color' => 'indigo',
                'description' => 'Full-spectrum migration and settlement services. Combines visa processing, education counseling, job placement, and post-arrival support.',
                'allowed_service_modules' => [
                    'tourist-visa',
                    'student-visa',
                    'work-visa',
                    'business-visa',
                    'permanent-residence',
                    'university-admission',
                    'job-placement',
                    'hotel-booking',
                    'language-course',
                    'skill-assessment',
                    'document-translation',
                    'settlement-services',
                ],
                'required_certifications' => [
                    'General Consultancy License',
                    'Immigration License',
                    'Education Consultant License',
                    'Recruitment License',
                    'Business Registration',
                ],
                'capabilities' => [
                    'all_visa_types',
                    'education_counseling',
                    'job_placement',
                    'settlement_services',
                    'document_services',
                    'translation_services',
                    'accommodation_support',
                    'create_all_resources',
                    'country_expertise',
                    'end_to_end_support',
                ],
                'default_commission_rate' => 20.00,
                'min_commission_rate' => 15.00,
                'max_commission_rate' => 28.00,
                'can_set_own_pricing' => true,
                'requires_verification' => true,
                'requires_insurance' => true,
                'can_manage_team' => true,
                'can_create_resources' => true, // Can add all resource types
                'needs_country_assignment' => true,
                'is_active' => true,
                'display_order' => 5,
            ],
        ];

        foreach ($agencyTypes as $type) {
            AgencyType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }

        $this->command->info('✅ Created 5 agency types successfully!');
        $this->command->table(
            ['Name', 'Slug', 'Commission Rate', 'Services'],
            collect($agencyTypes)->map(fn($t) => [
                $t['name'],
                $t['slug'],
                $t['default_commission_rate'].'%',
                count($t['allowed_service_modules']).' services'
            ])
        );
    }
}
