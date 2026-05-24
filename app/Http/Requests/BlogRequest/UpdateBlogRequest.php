<?php

namespace App\Http\Requests\BlogRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
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
        $blogId = $this->route('id');

        // Build unique rule for slug - only ignore current blog ID if it exists
        $slugRule = ['required', 'string', 'max:255'];
        if ($blogId && is_numeric($blogId)) {
            $slugRule[] = 'unique:blogs,slug,' . $blogId . ',id';
        } else {
            $slugRule[] = 'unique:blogs,slug';
        }

        return [
            'cover' => ['nullable', 'image', 'max:2048'], // Image file, max 2MB
            'title' => ['nullable', 'string', 'max:255'],
            'short_desc' => ['nullable', 'string', 'max:500'],
            'long_desc' => ['nullable', 'string'],
            'fk_category' => ['required', 'exists:category_blogs,id'],
            'slug' => $slugRule,
            'status' => ['boolean'],
            'hot_news' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
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
            'title.required' => 'Title is required.',
            'fk_category.required' => 'Category is required.',
            'fk_category.exists' => 'Selected category is invalid.',
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'Slug already exists.',
            'status.boolean' => 'Status must be true or false.',
            'hot_news.boolean' => 'Hot news must be true or false.',
            'cover.image' => 'Cover must be an image file (JPEG, PNG, GIF, WebP).',
            'cover.max' => 'Cover image size must not exceed 2MB.',
        ];
    }
}
