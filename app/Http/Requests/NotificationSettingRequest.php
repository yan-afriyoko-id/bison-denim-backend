<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationSettingRequest extends FormRequest
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
            'setting_key' => ['required', 'string', 'max:255'],
            'setting_name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'setting_key.required' => 'Setting key is required',
            'setting_key.max' => 'Setting key must not exceed 255 characters',
            'setting_name.required' => 'Setting name is required',
            'setting_name.max' => 'Setting name must not exceed 255 characters',
            'is_active.boolean' => 'Is active field must be true or false',
            'description.max' => 'Description must not exceed 1000 characters',
        ];
    }
}

