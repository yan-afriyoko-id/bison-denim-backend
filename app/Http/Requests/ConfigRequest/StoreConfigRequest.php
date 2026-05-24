<?php

namespace App\Http\Requests\ConfigRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfigRequest extends FormRequest
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
        return [
            'key' => 'required|string|unique:configs,key|max:255',
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
            'key.required' => 'Configuration key is required.',
            'key.unique' => 'Configuration key already exists.',
            'value.required' => 'Configuration value is required.',
            'type.in' => 'Configuration type must be one of: string, integer, boolean, json, text.',
        ];
    }
}

