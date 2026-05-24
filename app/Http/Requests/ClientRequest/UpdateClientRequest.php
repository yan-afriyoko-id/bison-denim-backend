<?php

namespace App\Http\Requests\ClientRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name must not exceed 255 characters.',
            'phone.string' => 'Phone must be a string.',
            'phone.max' => 'Phone must not exceed 20 characters.',
            'address.string' => 'Address must be a string.',
        ];
    }
}

