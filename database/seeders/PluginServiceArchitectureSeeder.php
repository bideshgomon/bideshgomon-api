<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceModule;
use App\Models\ServiceFormField;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PluginServiceArchitectureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates sample services with dynamic form fields
     * demonstrating the plugin architecture capabilities.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Get or create visa category
            $visaCategory = ServiceCategory::firstOrCreate(
                ['slug' => 'visa-services'],
                [
                    'name' => 'Visa Services',
                    'description' => 'Various visa application services',
                    'icon' => 'DocumentTextIcon',
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            );

            $studyCategory = ServiceCategory::firstOrCreate(
                ['slug' => 'study-abroad'],
                [
                    'name' => 'Study Abroad',
                    'description' => 'Education consultancy services',
                    'icon' => 'AcademicCapIcon',
                    'is_active' => true,
                    'sort_order' => 2,
                ]
            );

            $jobCategory = ServiceCategory::firstOrCreate(
                ['slug' => 'job-placement'],
                [
                    'name' => 'Job Placement',
                    'description' => 'International job placement services',
                    'icon' => 'BriefcaseIcon',
                    'is_active' => true,
                    'sort_order' => 3,
                ]
            );

            // Create Tourist Visa Service
            $this->createTouristVisaService($visaCategory);

            // Create Student Visa Service
            $this->createStudentVisaService($visaCategory);

            // Create Work Visa Service
            $this->createWorkVisaService($visaCategory);

            // Create Study Abroad Consultancy Service
            $this->createStudyAbroadService($studyCategory);

            // Create Job Placement Service
            $this->createJobPlacementService($jobCategory);

            DB::commit();

            $this->command->info('✅ Plugin Service Architecture seeded successfully!');
            $this->command->info('Created 5 services with dynamic form fields and profile mapping.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error seeding plugin services: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create Tourist Visa Service with form fields
     */
    protected function createTouristVisaService($category)
    {
        $service = ServiceModule::updateOrCreate(
            ['slug' => 'tourist-visa-application'],
            [
                'service_category_id' => $category->id,
                'name' => 'Tourist Visa Application',
                'short_description' => 'Apply for tourist visa with expert guidance and profile auto-fill',
                'full_description' => 'Complete tourist visa application service with document preparation, profile verification, and application tracking. Your profile information will be automatically filled to save time.',
                'service_type' => 'revenue_service',
                'icon' => 'GlobeAltIcon',
                'is_active' => true,
                'is_featured' => true,
                'coming_soon' => false,
                'requires_approval' => true,
                'processing_days' => 15,
                'sort_order' => 1,
                'config' => [
                    'allow_draft' => true,
                    'require_payment' => true,
                    'auto_approval' => false,
                    'base_price' => 5000.00,
                    'purpose' => 'tourism',
                ],
                'settings' => [
                    'min_processing_days' => 10,
                    'max_processing_days' => 30,
                    'required_documents' => ['passport', 'photo', 'bank_statement'],
                ],
            ]
        );

        // Create form fields for Tourist Visa
        $this->createFormFields($service, [
            // Personal Information Group
            [
                'label' => 'Destination Country',
                'field_name' => 'destination_country',
                'field_type' => 'select',
                'group_name' => 'Personal Information',
                'placeholder' => 'Select country you want to visit',
                'help_text' => 'Choose the country you are applying visa for',
                'options' => ['USA', 'UK', 'Canada', 'Australia', 'UAE', 'Malaysia', 'Singapore', 'Thailand'],
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Full Name (as per passport)',
                'field_name' => 'full_name',
                'field_type' => 'text',
                'group_name' => 'Personal Information',
                'placeholder' => 'Enter your full name',
                'profile_map_key' => 'user_profiles.name_as_per_passport',
                'validation_rules' => 'required|string|max:255',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'label' => 'Date of Birth',
                'field_name' => 'date_of_birth',
                'field_type' => 'date',
                'group_name' => 'Personal Information',
                'profile_map_key' => 'user_profiles.date_of_birth',
                'validation_rules' => 'required|date|before:today',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'label' => 'Gender',
                'field_name' => 'gender',
                'field_type' => 'radio',
                'group_name' => 'Personal Information',
                'profile_map_key' => 'user_profiles.gender',
                'options' => ['Male', 'Female', 'Other'],
                'validation_rules' => 'required|in:Male,Female,Other',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'label' => 'Nationality',
                'field_name' => 'nationality',
                'field_type' => 'text',
                'group_name' => 'Personal Information',
                'profile_map_key' => 'user_profiles.nationality',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
            ],

            // Contact Information Group
            [
                'label' => 'Email Address',
                'field_name' => 'email',
                'field_type' => 'email',
                'group_name' => 'Contact Information',
                'profile_map_key' => 'email',
                'validation_rules' => 'required|email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'label' => 'Phone Number',
                'field_name' => 'phone',
                'field_type' => 'tel',
                'group_name' => 'Contact Information',
                'profile_map_key' => 'user_phone_numbers.phone_number',
                'placeholder' => '+880 1712-345678',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'label' => 'Current Address',
                'field_name' => 'current_address',
                'field_type' => 'textarea',
                'group_name' => 'Contact Information',
                'profile_map_key' => 'user_profiles.current_address',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
            ],

            // Passport Information Group
            [
                'label' => 'Passport Number',
                'field_name' => 'passport_number',
                'field_type' => 'text',
                'group_name' => 'Passport Information',
                'profile_map_key' => 'user_passports.passport_number',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'label' => 'Passport Issue Date',
                'field_name' => 'passport_issue_date',
                'field_type' => 'date',
                'group_name' => 'Passport Information',
                'profile_map_key' => 'user_passports.issue_date',
                'validation_rules' => 'required|date|before:today',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'label' => 'Passport Expiry Date',
                'field_name' => 'passport_expiry_date',
                'field_type' => 'date',
                'group_name' => 'Passport Information',
                'profile_map_key' => 'user_passports.expiry_date',
                'validation_rules' => 'required|date|after:today',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 11,
            ],

            // Travel Details Group
            [
                'label' => 'Purpose of Visit',
                'field_name' => 'visit_purpose',
                'field_type' => 'select',
                'group_name' => 'Travel Details',
                'options' => ['Tourism', 'Visiting Family', 'Business Meeting', 'Medical Treatment', 'Other'],
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'label' => 'Intended Travel Date',
                'field_name' => 'travel_date',
                'field_type' => 'date',
                'group_name' => 'Travel Details',
                'validation_rules' => 'required|date|after:today',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'label' => 'Duration of Stay (days)',
                'field_name' => 'stay_duration',
                'field_type' => 'number',
                'group_name' => 'Travel Details',
                'validation_rules' => 'required|integer|min:1|max:90',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 14,
            ],

            // Financial Information Group
            [
                'label' => 'Occupation',
                'field_name' => 'occupation',
                'field_type' => 'text',
                'group_name' => 'Financial Information',
                'profile_map_key' => 'user_work_experiences.position',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'label' => 'Monthly Income (BDT)',
                'field_name' => 'monthly_income',
                'field_type' => 'number',
                'group_name' => 'Financial Information',
                'profile_map_key' => 'user_financial_information.monthly_income',
                'validation_rules' => 'required|numeric|min:0',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 16,
            ],

            // Document Uploads Group
            [
                'label' => 'Passport Photo Page',
                'field_name' => 'passport_scan',
                'field_type' => 'file',
                'group_name' => 'Document Uploads',
                'help_text' => 'Upload clear scan of passport photo page (PDF or Image)',
                'validation_rules' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 17,
            ],
            [
                'label' => 'Passport Size Photo',
                'field_name' => 'photo',
                'field_type' => 'file',
                'group_name' => 'Document Uploads',
                'help_text' => 'Recent passport size photo (white background)',
                'validation_rules' => 'required|image|mimes:jpg,jpeg,png|max:1024',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 18,
            ],
            [
                'label' => 'Bank Statement (Last 6 months)',
                'field_name' => 'bank_statement',
                'field_type' => 'file',
                'group_name' => 'Document Uploads',
                'help_text' => 'Upload bank statement showing sufficient funds',
                'validation_rules' => 'required|file|mimes:pdf|max:5120',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 19,
            ],

            // Additional Information Group
            [
                'label' => 'Have you been refused visa before?',
                'field_name' => 'visa_refused',
                'field_type' => 'radio',
                'group_name' => 'Additional Information',
                'options' => ['Yes', 'No'],
                'validation_rules' => 'required|in:Yes,No',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'label' => 'If yes, please provide details',
                'field_name' => 'visa_refused_details',
                'field_type' => 'textarea',
                'group_name' => 'Additional Information',
                'conditional_logic' => json_encode(['field' => 'visa_refused', 'operator' => '==', 'value' => 'Yes']),
                'validation_rules' => 'nullable|string',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 21,
            ],
            [
                'label' => 'Additional Comments',
                'field_name' => 'additional_comments',
                'field_type' => 'textarea',
                'group_name' => 'Additional Information',
                'placeholder' => 'Any additional information you want to share',
                'validation_rules' => 'nullable|string|max:1000',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 22,
            ],
        ]);

        $this->command->info('✅ Tourist Visa Service created with 22 form fields');
    }

    /**
     * Create Student Visa Service
     */
    protected function createStudentVisaService($category)
    {
        $service = ServiceModule::updateOrCreate(
            ['slug' => 'student-visa-application'],
            [
                'service_category_id' => $category->id,
                'name' => 'Student Visa Application',
                'short_description' => 'Student visa application with education profile mapping',
                'full_description' => 'Complete student visa application service with automatic education history integration.',
                'service_type' => 'revenue_service',
                'icon' => 'AcademicCapIcon',
                'is_active' => true,
                'is_featured' => true,
                'requires_approval' => true,
                'processing_days' => 30,
                'sort_order' => 2,
                'config' => ['allow_draft' => true, 'base_price' => 8000.00, 'purpose' => 'study'],
            ]
        );

        $this->createFormFields($service, [
            [
                'label' => 'Destination Country',
                'field_name' => 'destination_country',
                'field_type' => 'select',
                'group_name' => 'Basic Information',
                'options' => ['USA', 'UK', 'Canada', 'Australia', 'Germany', 'France'],
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Full Name',
                'field_name' => 'full_name',
                'field_type' => 'text',
                'group_name' => 'Basic Information',
                'profile_map_key' => 'user_profiles.name_as_per_passport',
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'label' => 'Email',
                'field_name' => 'email',
                'field_type' => 'email',
                'group_name' => 'Basic Information',
                'profile_map_key' => 'email',
                'validation_rules' => 'required|email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'label' => 'University Name',
                'field_name' => 'university_name',
                'field_type' => 'text',
                'group_name' => 'Education Details',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'label' => 'Course Name',
                'field_name' => 'course_name',
                'field_type' => 'text',
                'group_name' => 'Education Details',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'label' => 'Last Degree',
                'field_name' => 'last_degree',
                'field_type' => 'text',
                'group_name' => 'Education Details',
                'profile_map_key' => 'user_educations.degree_title',
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'label' => 'IELTS/TOEFL Score',
                'field_name' => 'language_test_score',
                'field_type' => 'text',
                'group_name' => 'Education Details',
                'profile_map_key' => 'user_languages.overall_score',
                'validation_rules' => 'nullable|string',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'label' => 'Acceptance Letter',
                'field_name' => 'acceptance_letter',
                'field_type' => 'file',
                'group_name' => 'Documents',
                'validation_rules' => 'required|file|mimes:pdf|max:5120',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
            ],
        ]);

        $this->command->info('✅ Student Visa Service created with 8 form fields');
    }

    /**
     * Create Work Visa Service
     */
    protected function createWorkVisaService($category)
    {
        $service = ServiceModule::updateOrCreate(
            ['slug' => 'work-visa-application'],
            [
                'service_category_id' => $category->id,
                'name' => 'Work Visa Application',
                'short_description' => 'Professional work visa with employment history mapping',
                'full_description' => 'Work visa application with automatic work experience integration from your profile.',
                'service_type' => 'revenue_service',
                'icon' => 'BriefcaseIcon',
                'is_active' => true,
                'is_featured' => false,
                'requires_approval' => true,
                'processing_days' => 45,
                'sort_order' => 3,
                'config' => ['allow_draft' => true, 'base_price' => 10000.00, 'purpose' => 'work'],
            ]
        );

        $this->createFormFields($service, [
            [
                'label' => 'Destination Country',
                'field_name' => 'destination_country',
                'field_type' => 'select',
                'group_name' => 'Basic Information',
                'options' => ['UAE', 'Saudi Arabia', 'Malaysia', 'Singapore', 'Qatar'],
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Job Title',
                'field_name' => 'job_title',
                'field_type' => 'text',
                'group_name' => 'Employment Details',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'label' => 'Employer Name',
                'field_name' => 'employer_name',
                'field_type' => 'text',
                'group_name' => 'Employment Details',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'label' => 'Current Experience (years)',
                'field_name' => 'experience_years',
                'field_type' => 'number',
                'group_name' => 'Employment Details',
                'profile_map_key' => 'user_work_experiences.years_of_experience',
                'validation_rules' => 'required|integer|min:0',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'label' => 'Job Offer Letter',
                'field_name' => 'job_offer_letter',
                'field_type' => 'file',
                'group_name' => 'Documents',
                'validation_rules' => 'required|file|mimes:pdf|max:5120',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ]);

        $this->command->info('✅ Work Visa Service created with 5 form fields');
    }

    /**
     * Create Study Abroad Consultancy Service
     */
    protected function createStudyAbroadService($category)
    {
        $service = ServiceModule::updateOrCreate(
            ['slug' => 'study-abroad-consultancy'],
            [
                'service_category_id' => $category->id,
                'name' => 'Study Abroad Consultancy',
                'short_description' => 'Expert guidance for studying abroad',
                'full_description' => 'Comprehensive consultancy for university selection, application, and visa processing.',
                'service_type' => 'revenue_service',
                'icon' => 'LightBulbIcon',
                'is_active' => true,
                'is_featured' => true,
                'requires_approval' => false,
                'processing_days' => 7,
                'sort_order' => 4,
                'config' => ['allow_draft' => true, 'base_price' => 15000.00, 'purpose' => 'consultation'],
            ]
        );

        $this->createFormFields($service, [
            [
                'label' => 'Preferred Country',
                'field_name' => 'preferred_country',
                'field_type' => 'select',
                'group_name' => 'Preferences',
                'options' => ['USA', 'UK', 'Canada', 'Australia', 'Germany'],
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Field of Study',
                'field_name' => 'field_of_study',
                'field_type' => 'text',
                'group_name' => 'Preferences',
                'validation_rules' => 'required|string',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'label' => 'Budget (USD)',
                'field_name' => 'budget',
                'field_type' => 'number',
                'group_name' => 'Preferences',
                'validation_rules' => 'required|numeric|min:0',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ]);

        $this->command->info('✅ Study Abroad Service created with 3 form fields');
    }

    /**
     * Create Job Placement Service
     */
    protected function createJobPlacementService($category)
    {
        $service = ServiceModule::updateOrCreate(
            ['slug' => 'international-job-placement'],
            [
                'service_category_id' => $category->id,
                'name' => 'International Job Placement',
                'short_description' => 'Find jobs abroad with our placement service',
                'full_description' => 'Professional job placement service connecting you with international employers.',
                'service_type' => 'revenue_service',
                'icon' => 'UserGroupIcon',
                'is_active' => true,
                'is_featured' => false,
                'requires_approval' => true,
                'processing_days' => 60,
                'sort_order' => 5,
                'config' => ['allow_draft' => true, 'base_price' => 20000.00, 'purpose' => 'job'],
            ]
        );

        $this->createFormFields($service, [
            [
                'label' => 'Preferred Country',
                'field_name' => 'preferred_country',
                'field_type' => 'select',
                'group_name' => 'Job Preferences',
                'options' => ['UAE', 'Saudi Arabia', 'Malaysia', 'Singapore', 'Qatar', 'Kuwait'],
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Job Category',
                'field_name' => 'job_category',
                'field_type' => 'select',
                'group_name' => 'Job Preferences',
                'options' => ['IT', 'Engineering', 'Healthcare', 'Hospitality', 'Construction', 'Retail'],
                'validation_rules' => 'required',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'label' => 'Years of Experience',
                'field_name' => 'experience_years',
                'field_type' => 'number',
                'group_name' => 'Job Preferences',
                'profile_map_key' => 'user_work_experiences.years_of_experience',
                'validation_rules' => 'required|integer|min:0',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'label' => 'CV/Resume',
                'field_name' => 'cv',
                'field_type' => 'file',
                'group_name' => 'Documents',
                'validation_rules' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ]);

        $this->command->info('✅ Job Placement Service created with 4 form fields');
    }

    /**
     * Helper to create form fields for a service
     */
    protected function createFormFields($service, $fields)
    {
        foreach ($fields as $fieldData) {
            $fieldData['service_module_id'] = $service->id;
            
            // Map 'label' to 'field_label' (correct column name)
            if (isset($fieldData['label'])) {
                $fieldData['field_label'] = $fieldData['label'];
                unset($fieldData['label']);
            }
            
            // Convert options array to JSON if exists
            if (isset($fieldData['options']) && is_array($fieldData['options'])) {
                $fieldData['options'] = json_encode($fieldData['options']);
            }
            
            // Convert conditional_logic to conditional_rules
            if (isset($fieldData['conditional_logic'])) {
                $fieldData['conditional_rules'] = $fieldData['conditional_logic'];
                unset($fieldData['conditional_logic']);
            }

            ServiceFormField::updateOrCreate(
                [
                    'service_module_id' => $service->id,
                    'field_name' => $fieldData['field_name'],
                ],
                $fieldData
            );
        }
    }
}
