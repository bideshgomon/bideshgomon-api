<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // To get user ID if needed for rules

class UserProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated users can update their own profile
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Copy rules ONLY for UserProfile fields from ProfileUpdateRequest
        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'dob' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'passport_number' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'current_occupation' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'present_address_line' => ['nullable', 'string', 'max:255'],
            'present_city' => ['nullable', 'string', 'max:100'],
            'present_country' => ['nullable', 'string', 'max:100'],
            'present_postal_code' => ['nullable', 'string', 'max:20'],
            'is_permanent_same_as_present' => ['nullable', 'boolean'],
            'permanent_address_line' => ['nullable', 'string', 'max:255'],
            'permanent_city' => ['nullable', 'string', 'max:100'],
            'permanent_country' => ['nullable', 'string', 'max:100'],
            'permanent_postal_code' => ['nullable', 'string', 'max:20'],
            'social_linkedin' => ['nullable', 'string', 'max:255', 'url'],
            'social_github' => ['nullable', 'string', 'max:255', 'url'],
            'social_website' => ['nullable', 'string', 'max:255', 'url'],
            'portfolio_link' => ['nullable', 'string', 'max:255', 'url'],
            'travel_purpose' => ['nullable', 'string', 'max:255'],
            'funding_source' => ['nullable', 'string', 'max:255'],
            'estimated_funds' => ['nullable', 'numeric', 'min:0'],
            'preferred_countries' => ['nullable', 'array'],
            'intended_intake' => ['nullable', 'string', 'max:100'],
            'has_dependents' => ['nullable', 'boolean'],
            'dependents_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
