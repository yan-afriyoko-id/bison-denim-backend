<?php

namespace App\Http\Requests\SettingRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email:rfc,dns|max:255',
            'instagram' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'pinterest' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:500',
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
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'instagram.url' => 'Instagram must be a valid URL.',
            'tiktok.url' => 'TikTok must be a valid URL.',
            'facebook.url' => 'Facebook must be a valid URL.',
            'youtube.url' => 'YouTube must be a valid URL.',
            'pinterest.url' => 'Pinterest must be a valid URL.',
            'location.max' => 'Location must not exceed 500 characters.',
        ];
    }
}

