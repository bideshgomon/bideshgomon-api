<?php

// app/Http/Controllers/Api/Admin/JobPostingController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Services\Admin\JobPostingService; // <-- Import the service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class JobPostingController extends Controller
{
    protected $jobPostingService;

    // Inject the service
    public function __construct(JobPostingService $jobPostingService)
    {
        $this->jobPostingService = $jobPostingService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Use the service to create the job
            $jobPosting = $this->jobPostingService->createJobPosting($request->all());

            return response()->json([
                'message' => 'Job posting created successfully.',
                'jobPosting' => $jobPosting,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error creating job posting via API', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     * Note: Other methods (index, show, update, destroy) would go here.
     */
    public function index()
    {
        // Placeholder for API index
        return JobPosting::paginate(10);
    }

    // ... other methods (show, update, destroy) ...
}
