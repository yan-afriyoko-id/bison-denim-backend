<?php

namespace App\Http\Requests\ConfigRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $key = $this->route('key');

        // Different validation rules for image configs
        $imageConfigs = ['store_logo_website', 'store_favicon'];

        if (in_array($key, $imageConfigs)) {
            return [
                'value' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,ico|max:2048',
                'description' => 'nullable|string|max:500',
                'type' => 'nullable|in:string,integer,boolean,json,text',
            ];
        }

        return [
            'value' => 'required|string',
            'description' => 'nullable|string|max:500',
            'type' => 'nullable|in:string,integer,boolean,json,text',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'value.required' => 'Configuration value is required.',
            'value.image' => 'The value must be a valid image file.',
            'value.mimes' => 'The image must be of type: jpg, jpeg, png, gif, webp, ico.',
            'value.max' => 'The image size must not exceed 2MB.',
            'type.in' => 'Configuration type must be one of: string, integer, boolean, json, text.',
        ];
    }
}

