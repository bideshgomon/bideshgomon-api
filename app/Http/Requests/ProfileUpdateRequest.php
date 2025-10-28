<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            // User Model
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            
            // UserProfile Model
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

            // Present Address
            'present_address_line' => ['nullable', 'string', 'max:255'],
            'present_city' => ['nullable', 'string', 'max:100'],
            'present_country' => ['nullable', 'string', 'max:100'],
            'present_postal_code' => ['nullable', 'string', 'max:20'],

            // Permanent Address
            'is_permanent_same_as_present' => ['nullable', 'boolean'],
            'permanent_address_line' => ['nullable', 'string', 'max:255'],
            'permanent_city' => ['nullable', 'string', 'max:100'],
            'permanent_country' => ['nullable', 'string', 'max:100'],
            'permanent_postal_code' => ['nullable', 'string', 'max:20'],

            // Links
            'social_linkedin' => ['nullable', 'string', 'max:255', 'url'],
            'social_github' => ['nullable', 'string', 'max:255', 'url'],
            'social_website' => ['nullable', 'string', 'max:255', 'url'],
            'portfolio_link' => ['nullable', 'string', 'max:255', 'url'],

            // Analysis Fields
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