<?php
// app/Services/Admin/JobPostingService.php

namespace App\Services\Admin;

use App\Models\JobPosting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class JobPostingService
{
    /**
     * Get validation rules for storing a job posting.
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'job_category_id' => 'required|exists:job_categories,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'company_name' => 'required|string|max:255',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'salary_range' => 'nullable|string',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'experience_level' => 'required|in:entry,mid,senior,manager',
            'education_level' => 'nullable|string',
            'application_deadline' => 'required|date',
            'status' => 'required|in:draft,published,expired,filled',
        ];
    }

    /**
     * Validate and create a new job posting.
     *
     * @param array $data
     * @return JobPosting
     * @throws ValidationException
     */
    public function createJobPosting(array $data): JobPosting
    {
        // Validate the incoming data
        $validator = Validator::make($data, $this->getValidationRules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Create the job posting
        return JobPosting::create($validator->validated());
    }
}