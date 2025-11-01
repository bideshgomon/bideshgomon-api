<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Import User model

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins can update users
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the user ID from the route parameter
        $userId = $this->route('user')->id;

        return [
            'name' => 'required|string|max:255',
            // Ensure email is unique, ignoring the current user
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'role_id' => 'required|exists:roles,id', // Ensure role exists
            'is_active' => 'required|boolean',
            // Optional: Add password validation if you allow admins to change passwords
            // 'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
