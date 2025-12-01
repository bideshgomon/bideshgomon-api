<?php

namespace App\Services;

use App\Models\User;
use App\Models\ServiceModule;
use App\Models\ServiceFormField;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

/**
 * DataMapperService - The "Smart Bridge"
 * 
 * This service auto-fills application forms with user profile data.
 * It's the core of the "Single Source of Truth" architecture.
 */
class DataMapperService
{
    /**
     * Get form structure with pre-filled data from user profile
     * 
     * @param ServiceModule $service
     * @param User $user
     * @return array
     */
    public function getFormWithData(ServiceModule $service, User $user): array
    {
        $fields = $service->formFields()
            ->active()
            ->ordered()
            ->get();

        $userData = $this->getUserProfileData($user);
        
        $formStructure = [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->short_description,
            ],
            'fields' => [],
            'groups' => [],
        ];

        foreach ($fields as $field) {
            $fieldData = $this->mapFieldData($field, $userData);
            
            // Add to fields array
            $formStructure['fields'][] = $fieldData;
            
            // Group fields by group_name
            if ($field->group_name) {
                if (!isset($formStructure['groups'][$field->group_name])) {
                    $formStructure['groups'][$field->group_name] = [];
                }
                $formStructure['groups'][$field->group_name][] = $fieldData;
            }
        }

        return $formStructure;
    }

    /**
     * Map field data with user profile value
     * 
     * @param ServiceFormField $field
     * @param array $userData
     * @return array
     */
    private function mapFieldData(ServiceFormField $field, array $userData): array
    {
        $value = $this->getProfileValue($field, $userData);
        
        return [
            'id' => $field->id,
            'name' => $field->field_name,
            'label' => $field->field_label,
            'type' => $field->field_type,
            'placeholder' => $field->placeholder,
            'help_text' => $field->help_text,
            'default_value' => $field->default_value,
            'value' => $value, // Pre-filled from profile
            'is_required' => $field->is_required,
            'is_readonly' => $field->is_readonly,
            'validation_rules' => $field->getValidationRulesArray(),
            'options' => $field->getOptionsArray(),
            'allow_multiple' => $field->allow_multiple,
            'accepted_file_types' => $field->accepted_file_types,
            'max_file_size' => $field->max_file_size,
            'conditional_rules' => $field->conditional_rules,
            'sort_order' => $field->sort_order,
            'group_name' => $field->group_name,
            'section_name' => $field->section_name,
            'column_width' => $field->column_width,
            'css_class' => $field->css_class,
            'has_profile_mapping' => $field->hasProfileMapping(),
            'profile_map_key' => $field->getProfileMapPath(),
            'is_prefilled' => !empty($value),
        ];
    }

    /**
     * Get value from user profile using field mapping
     * 
     * @param ServiceFormField $field
     * @param array $userData
     * @return mixed
     */
    private function getProfileValue(ServiceFormField $field, array $userData)
    {
        if (!$field->hasProfileMapping()) {
            return $field->default_value;
        }

        $mapPath = $field->getProfileMapPath();
        
        // Use dot notation to access nested data
        $value = Arr::get($userData, $mapPath);
        
        // Fallback to default value if profile value is empty
        return $value ?? $field->default_value;
    }

    /**
     * Get comprehensive user profile data
     * 
     * @param User $user
     * @return array
     */
    private function getUserProfileData(User $user): array
    {
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        // Load user_profiles
        if ($user->profile) {
            $data['user_profiles'] = $user->profile->toArray();
        }

        // Load primary passport
        $primaryPassport = $user->passports()->where('is_primary', true)->first();
        if ($primaryPassport) {
            $data['user_passports'] = $primaryPassport->toArray();
        }

        // Load primary phone number
        $primaryPhone = $user->phoneNumbers()->where('is_primary', true)->first();
        if ($primaryPhone) {
            $data['user_phone_numbers'] = $primaryPhone->toArray();
        }

        // Load financial information
        if ($user->financialInformation) {
            $data['user_financial_information'] = $user->financialInformation->toArray();
        }

        // Load education (latest)
        $latestEducation = $user->educations()->latest()->first();
        if ($latestEducation) {
            $data['user_educations'] = $latestEducation->toArray();
        }

        // Load work experience (latest)
        $latestWork = $user->workExperiences()->latest()->first();
        if ($latestWork) {
            $data['user_work_experiences'] = $latestWork->toArray();
        }

        // Load languages
        $data['user_languages'] = $user->languages()->get()->toArray();

        return $data;
    }

    /**
     * Validate form data against field rules
     * 
     * @param ServiceModule $service
     * @param array $formData
     * @return array [bool $isValid, array $errors]
     */
    public function validateFormData(ServiceModule $service, array $formData): array
    {
        $fields = $service->formFields()->active()->get();
        $rules = [];
        $messages = [];

        foreach ($fields as $field) {
            // Skip fields that shouldn't be shown based on conditional rules
            if (!$field->shouldShow($formData)) {
                continue;
            }

            $rules[$field->field_name] = $field->getValidationRulesArray();
            
            // Custom error messages
            $messages["{$field->field_name}.required"] = "{$field->field_label} is required.";
            $messages["{$field->field_name}.email"] = "{$field->field_label} must be a valid email address.";
            $messages["{$field->field_name}.numeric"] = "{$field->field_label} must be a number.";
            $messages["{$field->field_name}.date"] = "{$field->field_label} must be a valid date.";
            
            if ($field->min_length) {
                $messages["{$field->field_name}.min"] = "{$field->field_label} must be at least {$field->min_length} characters.";
            }
            if ($field->max_length) {
                $messages["{$field->field_name}.max"] = "{$field->field_label} must not exceed {$field->max_length} characters.";
            }
        }

        $validator = validator($formData, $rules, $messages);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors()->toArray(),
            ];
        }

        return [
            'valid' => true,
            'errors' => [],
        ];
    }

    /**
     * Update user profile with form data (when user checks "Save to profile")
     * 
     * @param User $user
     * @param ServiceModule $service
     * @param array $formData
     * @param array $fieldsToUpdate Array of field names user wants to update in profile
     * @return bool
     */
    public function updateProfileFromFormData(User $user, ServiceModule $service, array $formData, array $fieldsToUpdate = []): bool
    {
        $fields = $service->formFields()
            ->active()
            ->whereIn('field_name', $fieldsToUpdate)
            ->whereNotNull('profile_map_key')
            ->get();

        DB::beginTransaction();
        try {
            foreach ($fields as $field) {
                $value = $formData[$field->field_name] ?? null;
                
                if ($value === null) {
                    continue;
                }

                $this->updateProfileField($user, $field, $value);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update profile from form data', [
                'user_id' => $user->id,
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Update specific profile field
     * 
     * @param User $user
     * @param ServiceFormField $field
     * @param mixed $value
     * @return void
     */
    private function updateProfileField(User $user, ServiceFormField $field, $value): void
    {
        [$table, $column] = explode('.', $field->getProfileMapPath());

        switch ($table) {
            case 'user_profiles':
                if (!$user->profile) {
                    $user->profile()->create([$column => $value]);
                } else {
                    $user->profile->update([$column => $value]);
                }
                break;

            case 'user_passports':
                $passport = $user->passports()->where('is_primary', true)->first();
                if ($passport) {
                    $passport->update([$column => $value]);
                }
                break;

            case 'user_phone_numbers':
                $phone = $user->phoneNumbers()->where('is_primary', true)->first();
                if ($phone) {
                    $phone->update([$column => $value]);
                }
                break;

            case 'user_financial_information':
                if (!$user->financialInformation) {
                    $user->financialInformation()->create([$column => $value]);
                } else {
                    $user->financialInformation->update([$column => $value]);
                }
                break;

            // Add more cases as needed
        }
    }

    /**
     * Get available profile fields for mapping (Admin tool)
     * 
     * @return array
     */
    public function getAvailableProfileFields(): array
    {
        return [
            'Personal Information' => [
                ['key' => 'user_profiles.first_name', 'label' => 'First Name', 'type' => 'text'],
                ['key' => 'user_profiles.middle_name', 'label' => 'Middle Name', 'type' => 'text'],
                ['key' => 'user_profiles.last_name', 'label' => 'Last Name', 'type' => 'text'],
                ['key' => 'user_profiles.date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'],
                ['key' => 'user_profiles.gender', 'label' => 'Gender', 'type' => 'select'],
                ['key' => 'user_profiles.nationality', 'label' => 'Nationality', 'type' => 'text'],
                ['key' => 'user_profiles.national_id', 'label' => 'National ID (NID)', 'type' => 'text'],
                ['key' => 'user_profiles.birth_certificate_number', 'label' => 'Birth Certificate Number', 'type' => 'text'],
            ],
            'Contact Information' => [
                ['key' => 'user.email', 'label' => 'Email Address', 'type' => 'email'],
                ['key' => 'user_phone_numbers.phone_number', 'label' => 'Phone Number', 'type' => 'tel'],
                ['key' => 'user_profiles.address', 'label' => 'Address', 'type' => 'textarea'],
                ['key' => 'user_profiles.city', 'label' => 'City', 'type' => 'text'],
                ['key' => 'user_profiles.state', 'label' => 'State/Province', 'type' => 'text'],
                ['key' => 'user_profiles.postal_code', 'label' => 'Postal Code', 'type' => 'text'],
                ['key' => 'user_profiles.country', 'label' => 'Country', 'type' => 'select'],
            ],
            'Passport Information' => [
                ['key' => 'user_passports.passport_number', 'label' => 'Passport Number', 'type' => 'text'],
                ['key' => 'user_passports.issue_date', 'label' => 'Passport Issue Date', 'type' => 'date'],
                ['key' => 'user_passports.expiry_date', 'label' => 'Passport Expiry Date', 'type' => 'date'],
                ['key' => 'user_passports.issue_place', 'label' => 'Place of Issue', 'type' => 'text'],
                ['key' => 'user_passports.passport_type', 'label' => 'Passport Type', 'type' => 'select'],
            ],
            'Financial Information' => [
                ['key' => 'user_financial_information.annual_income', 'label' => 'Annual Income', 'type' => 'number'],
                ['key' => 'user_financial_information.bank_name', 'label' => 'Bank Name', 'type' => 'text'],
                ['key' => 'user_financial_information.account_number', 'label' => 'Account Number', 'type' => 'text'],
            ],
            'Education' => [
                ['key' => 'user_educations.institution_name', 'label' => 'Institution Name', 'type' => 'text'],
                ['key' => 'user_educations.degree', 'label' => 'Degree/Certificate', 'type' => 'text'],
                ['key' => 'user_educations.field_of_study', 'label' => 'Field of Study', 'type' => 'text'],
                ['key' => 'user_educations.graduation_year', 'label' => 'Graduation Year', 'type' => 'number'],
            ],
            'Work Experience' => [
                ['key' => 'user_work_experiences.company_name', 'label' => 'Company Name', 'type' => 'text'],
                ['key' => 'user_work_experiences.job_title', 'label' => 'Job Title', 'type' => 'text'],
                ['key' => 'user_work_experiences.start_date', 'label' => 'Start Date', 'type' => 'date'],
                ['key' => 'user_work_experiences.end_date', 'label' => 'End Date', 'type' => 'date'],
            ],
        ];
    }
}
