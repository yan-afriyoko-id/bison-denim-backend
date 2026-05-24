<?php

namespace App\Http\Requests\PopupBannerRequest;

use Illuminate\Foundation\Http\FormRequest;

class StorePopupBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sesuaikan jika pakai permission
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:2048'],
            'url' => ['nullable', 'url'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'image.required' => 'Image is required.',
            'url.url' => 'URL must be a valid link.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }
}
