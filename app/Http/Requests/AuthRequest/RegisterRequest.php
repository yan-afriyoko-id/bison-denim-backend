<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'dob' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'First name is required.',
            'name.max' => 'First name must not exceed 100 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.max' => 'Last name must not exceed 100 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'email.max' => 'Email must not exceed 100 characters.',
            'phone.unique' => 'This phone number is already registered.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'dob.required' => 'Date of birth is required.',
            'dob.date' => 'Please enter a valid date.',
            'dob.before' => 'Date of birth must be in the past.',
            'dob.after' => 'Please enter a valid date of birth.',
            'gender.required' => 'Gender is required.',
            'gender.in' => 'Gender must be either MALE or FEMALE.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
