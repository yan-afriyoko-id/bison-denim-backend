<?php

namespace App\Http\Requests\BlogRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryBlogRequest extends FormRequest
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
        $categoryId = $this->route('id');

        // Build unique rule for slug - only ignore current category ID if it exists
        $slugRule = ['required', 'string', 'max:255'];
        if ($categoryId && is_numeric($categoryId)) {
            $slugRule[] = 'unique:category_blogs,slug,' . $categoryId . ',id';
        } else {
            $slugRule[] = 'unique:category_blogs,slug';
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
            'description' => ['nullable', 'string'],
            'status' => ['boolean'],
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
            'name.required' => 'Category name is required.',
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'Slug already exists.',
            'status.boolean' => 'Status must be true or false.',
        ];
    }
}
