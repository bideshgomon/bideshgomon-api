<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'job_category_id' => 'sometimes|required|exists:job_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'employment_type' => 'sometimes|required|string|max:50',
            'experience_level' => 'nullable|string|max:50',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'apply_url' => 'nullable|string|max:255|url',
            'is_active' => 'sometimes|required|boolean',
        ];
    }
}