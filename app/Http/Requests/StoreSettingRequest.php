<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
            'logo_website' => ['nullable', 'string', 'url'],
            'favicon' => ['nullable', 'string', 'url'],
            'store_address' => ['nullable', 'string', 'max:500'],
            'store_name' => ['nullable', 'string', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:20'],
            'store_email' => ['nullable', 'email'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'logo_website.url' => 'Logo website must be a valid URL',
            'favicon.url' => 'Favicon must be a valid URL',
            'store_address.max' => 'Store address must not exceed 500 characters',
            'store_name.max' => 'Store name must not exceed 255 characters',
            'store_phone.max' => 'Store phone must not exceed 20 characters',
            'store_email.email' => 'Store email must be a valid email address',
        ];
    }
}

