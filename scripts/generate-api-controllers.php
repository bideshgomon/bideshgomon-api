<?php
/**
 * Generate API controllers for new services
 * Usage: php scripts/generate-api-controllers.php
 */

$services = [
    'StudentVisa' => [
        'model' => 'StudentVisa',
        'table' => 'student_visas',
        'slug' => 'student-visa',
        'relationship' => 'studentVisas',
        'foreign_key' => 'student_visa_id',
        'validation' => [
            'destination_country_id' => 'required|exists:countries,id',
            'education_level' => 'required|string',
            'study_field' => 'nullable|string',
            'institution_name' => 'nullable|string|max:255',
            'course_name' => 'nullable|string|max:255',
            'intended_start_date' => 'nullable|date',
            'course_duration_months' => 'nullable|integer|min:1',
            'has_admission_letter' => 'boolean',
            'has_ielts_toefl' => 'boolean',
            'english_test_type' => 'nullable|string',
            'english_test_score' => 'nullable|string',
            'previous_education_gpa' => 'nullable|string',
            'user_notes' => 'nullable|string|max:5000',
        ],
    ],
    'WorkVisa' => [
        'model' => 'WorkVisa',
        'table' => 'work_visas',
        'slug' => 'work-visa',
        'relationship' => 'workVisas',
        'foreign_key' => 'work_visa_id',
        'validation' => [
            'destination_country_id' => 'required|exists:countries,id',
            'job_title' => 'required|string|max:255',
            'job_category' => 'nullable|string',
            'employer_name' => 'nullable|string|max:255',
            'employment_type' => 'nullable|string',
            'offered_salary' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string',
            'years_of_experience' => 'nullable|integer|min:0',
            'intended_start_date' => 'nullable|date',
            'user_notes' => 'nullable|string|max:5000',
        ],
    ],
    'Translation' => [
        'model' => 'Translation',
        'table' => 'translations',
        'slug' => 'translation',
        'relationship' => 'translations',
        'foreign_key' => 'translation_id',
        'validation' => [
            'document_type' => 'required|string',
            'source_language' => 'required|string',
            'target_language' => 'required|string',
            'page_count' => 'nullable|integer|min:1',
            'certification_type' => 'nullable|string',
            'is_urgent' => 'boolean',
            'required_by_date' => 'nullable|date',
            'user_notes' => 'nullable|string|max:5000',
        ],
    ],
    'Attestation' => [
        'model' => 'Attestation',
        'table' => 'attestations',
        'slug' => 'attestation',
        'relationship' => 'attestations',
        'foreign_key' => 'attestation_id',
        'validation' => [
            'target_country_id' => 'required|exists:countries,id',
            'document_type' => 'required|string',
            'attestation_type' => 'required|string',
            'purpose' => 'nullable|string',
            'document_count' => 'nullable|integer|min:1',
            'is_urgent' => 'boolean',
            'required_by_date' => 'nullable|date',
            'user_notes' => 'nullable|string|max:5000',
        ],
    ],
    'HajjUmrah' => [
        'model' => 'HajjUmrah',
        'table' => 'hajj_umrahs',
        'slug' => 'hajj-umrah',
        'relationship' => 'hajjUmrahs',
        'foreign_key' => 'hajj_umrah_id',
        'validation' => [
            'package_type' => 'required|string',
            'number_of_travelers' => 'required|integer|min:1',
            'preferred_travel_date' => 'nullable|date',
            'duration' => 'nullable|string',
            'accommodation_type' => 'nullable|string',
            'meal_plan' => 'nullable|string',
            'transport_type' => 'nullable|string',
            'requires_visa_assistance' => 'boolean',
            'requires_training' => 'boolean',
            'special_requirements' => 'nullable|string|max:5000',
        ],
    ],
];

function generateApiController($service, $config) {
    $validationRules = var_export($config['validation'], true);
    
    $template = <<<PHP
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\\{$config['model']};
use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class {$service}ApplicationController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index(Request \$request)
    {
        \$applications = \$request->user()->{$config['relationship']}()
            ->with(['destinationCountry'])
            ->latest()
            ->paginate(15);

        return response()->json(\$applications);
    }

    /**
     * Store a newly created resource in storage for the authenticated user.
     */
    public function store(Request \$request)
    {
        \$validated = \$request->validate($validationRules);

        \$validated['user_id'] = \$request->user()->id;
        \$validated['status'] = 'pending';

        DB::beginTransaction();
        try {
            // Create the {$config['model']} record
            \$application = {$config['model']}::create(\$validated);

            // Get the service module (slug: '{$config['slug']}')
            \$serviceModule = ServiceModule::where('slug', '{$config['slug']}')->first();

            if (\$serviceModule) {
                // Create ServiceApplication for agency processing
                \$serviceApplication = ServiceApplication::create([
                    'user_id' => \$request->user()->id,
                    'service_module_id' => \$serviceModule->id,
                    '{$config['foreign_key']}' => \$application->id,
                    'status' => 'pending',
                    'application_data' => \$validated,
                ]);

                Log::info('ServiceApplication created for {$config['slug']}', [
                    'service_application_id' => \$serviceApplication->id,
                    'application_number' => \$serviceApplication->application_number,
                ]);
            }

            DB::commit();

            Log::info('{$config['slug']} application created', [
                'application_id' => \$application->id,
                'user_id' => \$request->user()->id,
            ]);

            \$application->load(['destinationCountry']);

            return response()->json([
                'message' => 'Application created successfully',
                'application' => \$application,
                'redirect' => route('profile.{$config['slug']}.show', \$application),
            ], 201);

        } catch (\Exception \$e) {
            DB::rollBack();
            Log::error('Failed to create {$config['slug']} application', [
                'error' => \$e->getMessage(),
                'user_id' => \$request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to create application',
                'error' => \$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request \$request, {$config['model']} \$application)
    {
        // Ensure user can only view their own applications
        if (\$application->user_id !== \$request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized access to this application.'
            ], 403);
        }

        \$application->load(['destinationCountry']);

        return response()->json(\$application);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request \$request, {$config['model']} \$application)
    {
        // Ensure user can only update their own applications
        if (\$application->user_id !== \$request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized access to this application.'
            ], 403);
        }

        // Only allow updates if status is 'pending'
        if (\$application->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot update application after submission.'
            ], 422);
        }

        \$validated = \$request->validate($validationRules);

        \$application->update(\$validated);

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => \$application->load(['destinationCountry']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request \$request, {$config['model']} \$application)
    {
        // Ensure user can only delete their own applications
        if (\$application->user_id !== \$request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized access to this application.'
            ], 403);
        }

        // Only allow deletion if status is 'pending'
        if (\$application->status !== 'pending') {
            return response()->json([
                'message' => 'Cannot delete application after submission.'
            ], 422);
        }

        \$application->delete();

        return response()->json([
            'message' => 'Application deleted successfully'
        ]);
    }
}
PHP;

    return $template;
}

// Generate controllers
foreach ($services as $service => $config) {
    $dir = __DIR__ . "/../app/Http/Controllers/Api";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $path = "$dir/{$service}ApplicationController.php";
    file_put_contents($path, generateApiController($service, $config));
    echo "✅ Generated {$service}ApplicationController.php\n";
}

echo "\n🎉 All API controllers generated successfully!\n";
echo "📝 Next: Add routes to routes/api.php\n";
