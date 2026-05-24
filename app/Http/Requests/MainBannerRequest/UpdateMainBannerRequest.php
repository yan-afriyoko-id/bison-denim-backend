<?php

namespace App\Http\Requests\MainBannerRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMainBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:250'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'link_url' => ['nullable', 'url', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:ACTIVE,INACTIVE'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
            'link_url.url' => 'Link tujuan harus berupa URL yang valid.',
        ];
    }
}