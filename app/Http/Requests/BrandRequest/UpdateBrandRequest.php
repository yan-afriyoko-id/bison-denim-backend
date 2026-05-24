<?php

namespace App\Http\Requests\BrandRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:250'],
            'slug' => ['sometimes', 'string', 'max:250'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'], // 5MB max
            'status' => ['nullable', 'in:ACTIVE,INACTIVE'],
            'order' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
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
            'name.string' => 'Brand name must be a string.',
            'name.max' => 'Brand name must not exceed 250 characters.',
            'slug.string' => 'Brand slug must be a string.',
            'slug.max' => 'Brand slug must not exceed 250 characters.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a file of type: jpeg, jpg, png, gif, webp.',
            'logo.max' => 'Logo file size must not exceed 5MB.',
            'status.in' => 'Status must be ACTIVE or INACTIVE.',
            'order.integer' => 'Order must be an integer.',
            'order.min' => 'Order must be at least 0.',
        ];
    }
}
