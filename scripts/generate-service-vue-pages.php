<?php
/**
 * Generate Vue pages for new services
 * Usage: php scripts/generate-service-vue-pages.php
 */

$services = [
    'WorkVisa' => [
        'route_name' => 'work-visa',
        'title' => 'Work Visa',
        'description' => 'working abroad',
        'icon_path' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'form_fields' => [
            ['name' => 'job_title', 'label' => 'Job Title', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g., Software Engineer'],
            ['name' => 'job_category', 'label' => 'Job Category', 'type' => 'select', 'required' => false, 'options' => ['IT', 'Healthcare', 'Construction', 'Hospitality', 'Education', 'Finance', 'Other']],
            ['name' => 'employer_name', 'label' => 'Employer Name', 'type' => 'text', 'required' => false, 'placeholder' => 'e.g., Tech Corp Inc.'],
            ['name' => 'employment_type', 'label' => 'Employment Type', 'type' => 'select', 'required' => false, 'options' => ['Full-time', 'Part-time', 'Contract', 'Temporary']],
            ['name' => 'offered_salary', 'label' => 'Offered Salary', 'type' => 'number', 'required' => false, 'placeholder' => 'Annual salary'],
            ['name' => 'years_of_experience', 'label' => 'Years of Experience', 'type' => 'number', 'required' => false, 'min' => 0, 'max' => 50],
            ['name' => 'intended_start_date', 'label' => 'Intended Start Date', 'type' => 'date', 'required' => false],
        ],
        'list_fields' => [
            ['name' => 'job_title', 'label' => 'Job Title'],
            ['name' => 'employer_name', 'label' => 'Employer'],
            ['name' => 'employment_type', 'label' => 'Type'],
            ['name' => 'intended_start_date', 'label' => 'Start Date'],
        ]
    ],
    'Translation' => [
        'route_name' => 'translation',
        'title' => 'Translation',
        'description' => 'document translation services',
        'icon_path' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
        'form_fields' => [
            ['name' => 'document_type', 'label' => 'Document Type', 'type' => 'select', 'required' => true, 'options' => ['Passport', 'Birth Certificate', 'Marriage Certificate', 'Academic Transcript', 'Degree Certificate', 'Experience Letter', 'Legal Document', 'Medical Report', 'Other']],
            ['name' => 'source_language', 'label' => 'Source Language', 'type' => 'select', 'required' => true, 'options' => ['English', 'Arabic', 'Bengali', 'Hindi', 'Urdu', 'Chinese', 'French', 'Spanish', 'German', 'Other']],
            ['name' => 'target_language', 'label' => 'Target Language', 'type' => 'select', 'required' => true, 'options' => ['English', 'Arabic', 'Bengali', 'Hindi', 'Urdu', 'Chinese', 'French', 'Spanish', 'German', 'Other']],
            ['name' => 'page_count', 'label' => 'Number of Pages', 'type' => 'number', 'required' => false, 'min' => 1, 'placeholder' => 'e.g., 5'],
            ['name' => 'certification_type', 'label' => 'Certification Type', 'type' => 'select', 'required' => false, 'options' => ['Standard', 'Certified', 'Notarized', 'Apostille']],
            ['name' => 'is_urgent', 'label' => 'Urgent Service', 'type' => 'checkbox', 'required' => false],
            ['name' => 'required_by_date', 'label' => 'Required By Date', 'type' => 'date', 'required' => false],
        ],
        'list_fields' => [
            ['name' => 'document_type', 'label' => 'Document'],
            ['name' => 'source_language', 'label' => 'From'],
            ['name' => 'target_language', 'label' => 'To'],
            ['name' => 'page_count', 'label' => 'Pages'],
        ]
    ],
    'Attestation' => [
        'route_name' => 'attestation',
        'title' => 'Attestation',
        'description' => 'document attestation services',
        'icon_path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'form_fields' => [
            ['name' => 'target_country_id', 'label' => 'Target Country', 'type' => 'country_select', 'required' => true],
            ['name' => 'document_type', 'label' => 'Document Type', 'type' => 'select', 'required' => true, 'options' => ['Educational Certificate', 'Degree', 'Birth Certificate', 'Marriage Certificate', 'Police Clearance', 'Medical Certificate', 'Experience Certificate', 'Power of Attorney', 'Commercial Document', 'Other']],
            ['name' => 'attestation_type', 'label' => 'Attestation Type', 'type' => 'select', 'required' => true, 'options' => ['MOFA', 'Embassy', 'HRD', 'MEA', 'Chamber of Commerce', 'Notary', 'Apostille']],
            ['name' => 'purpose', 'label' => 'Purpose', 'type' => 'select', 'required' => false, 'options' => ['Employment Visa', 'Student Visa', 'Business Visa', 'Family Visa', 'Work Permit', 'Immigration', 'Other']],
            ['name' => 'document_count', 'label' => 'Number of Documents', 'type' => 'number', 'required' => false, 'min' => 1, 'placeholder' => 'e.g., 3'],
            ['name' => 'is_urgent', 'label' => 'Urgent Service', 'type' => 'checkbox', 'required' => false],
            ['name' => 'required_by_date', 'label' => 'Required By Date', 'type' => 'date', 'required' => false],
        ],
        'list_fields' => [
            ['name' => 'document_type', 'label' => 'Document'],
            ['name' => 'attestation_type', 'label' => 'Type'],
            ['name' => 'purpose', 'label' => 'Purpose'],
            ['name' => 'document_count', 'label' => 'Count'],
        ]
    ],
    'HajjUmrah' => [
        'route_name' => 'hajj-umrah',
        'title' => 'Hajj & Umrah',
        'description' => 'pilgrimage packages',
        'icon_path' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'form_fields' => [
            ['name' => 'package_type', 'label' => 'Package Type', 'type' => 'select', 'required' => true, 'options' => ['Hajj Package', 'Umrah Package', 'Hajj + Umrah Combined']],
            ['name' => 'number_of_travelers', 'label' => 'Number of Travelers', 'type' => 'number', 'required' => true, 'min' => 1, 'placeholder' => 'e.g., 2'],
            ['name' => 'preferred_travel_date', 'label' => 'Preferred Travel Date', 'type' => 'date', 'required' => false],
            ['name' => 'duration', 'label' => 'Duration', 'type' => 'select', 'required' => false, 'options' => ['7 Days', '10 Days', '14 Days', '21 Days', '30 Days', '40 Days', 'Custom']],
            ['name' => 'accommodation_type', 'label' => 'Accommodation', 'type' => 'select', 'required' => false, 'options' => ['Economy (Shared)', 'Standard (4-6 persons)', 'Premium (2-3 persons)', 'VIP (Private)']],
            ['name' => 'meal_plan', 'label' => 'Meal Plan', 'type' => 'select', 'required' => false, 'options' => ['Without Meals', 'Breakfast Only', 'Half Board', 'Full Board']],
            ['name' => 'transport_type', 'label' => 'Transport', 'type' => 'select', 'required' => false, 'options' => ['Standard Bus', 'AC Coach', 'Private Vehicle']],
            ['name' => 'requires_visa_assistance', 'label' => 'Visa Assistance Required', 'type' => 'checkbox', 'required' => false],
            ['name' => 'requires_training', 'label' => 'Spiritual Training Required', 'type' => 'checkbox', 'required' => false],
        ],
        'list_fields' => [
            ['name' => 'package_type', 'label' => 'Package'],
            ['name' => 'number_of_travelers', 'label' => 'Travelers'],
            ['name' => 'duration', 'label' => 'Duration'],
            ['name' => 'preferred_travel_date', 'label' => 'Travel Date'],
        ]
    ],
];

function generateCreateVue($service, $config) {
    $formFields = '';
    foreach ($config['form_fields'] as $field) {
        if ($field['type'] === 'select') {
            $formFields .= "    {$field['name']}: '',\n";
        } elseif ($field['type'] === 'checkbox') {
            $formFields .= "    {$field['name']}: false,\n";
        } else {
            $formFields .= "    {$field['name']}: '',\n";
        }
    }
    
    $template = <<<VUE
<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    countries: Array,
});

const form = useForm({
    destination_country_id: '',
$formFields    user_notes: '',
});

const submit = () => {
    form.post(route('api.{$config['route_name']}-applications.store'), {
        preserveScroll: true,
        onSuccess: () => {
            // Backend will handle redirect
        },
    });
};
</script>

<template>
    <Head title="Create {$config['title']} Application" />

    <AuthenticatedLayout>
        <div class="py-6 sm:py-12">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6 sm:mb-8">
                    <Link :href="route('profile.{$config['route_name']}.index')" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Applications
                    </Link>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">New {$config['title']} Application</h1>
                    <p class="mt-2 text-sm text-gray-600">Apply for {$config['description']}</p>
                </div>

                <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Form fields will be added here -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea v-model="form.user_notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Any additional information..."></textarea>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Your application will be saved as "Pending"</li>
                                            <li>Multiple agencies will provide quotes</li>
                                            <li>Compare and select the best option</li>
                                            <li>Your chosen agency will process your application</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                        <Link :href="route('profile.{$config['route_name']}.index')" class="inline-flex items-center justify-center px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ form.processing ? 'Creating...' : 'Create Application' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
VUE;

    return $template;
}

// Generate pages
foreach ($services as $service => $config) {
    $dir = __DIR__ . "/../resources/js/Pages/Profile/$service";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Generate Create.vue
    $createPath = "$dir/Create.vue";
    file_put_contents($createPath, generateCreateVue($service, $config));
    echo "✅ Generated $service/Create.vue\n";
    
    // Copy Index and Show from StudentVisa (we'll customize later)
    $sourceIndex = __DIR__ . "/../resources/js/Pages/Profile/StudentVisa/Index.vue";
    $sourceShow = __DIR__ . "/../resources/js/Pages/Profile/StudentVisa/Show.vue";
    
    if (file_exists($sourceIndex)) {
        $indexContent = file_get_contents($sourceIndex);
        $indexContent = str_replace('student-visa', $config['route_name'], $indexContent);
        $indexContent = str_replace('Student Visa', $config['title'], $indexContent);
        $indexContent = str_replace('StudentVisa', $service, $indexContent);
        file_put_contents("$dir/Index.vue", $indexContent);
        echo "✅ Generated $service/Index.vue\n";
    }
    
    if (file_exists($sourceShow)) {
        $showContent = file_get_contents($sourceShow);
        $showContent = str_replace('student-visa', $config['route_name'], $showContent);
        $showContent = str_replace('Student Visa', $config['title'], $showContent);
        $showContent = str_replace('StudentVisa', $service, $showContent);
        file_put_contents("$dir/Show.vue", $showContent);
        echo "✅ Generated $service/Show.vue\n";
    }
}

echo "\n🎉 All Vue pages generated successfully!\n";
echo "📝 Next: Customize the Create.vue forms with specific fields\n";
