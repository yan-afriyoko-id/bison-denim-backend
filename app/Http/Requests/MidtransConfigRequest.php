<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MidtransConfigRequest extends FormRequest
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
            'environment' => ['required', 'string', 'in:sandbox,production'],
            'server_key' => ['required', 'string', 'max:255'],
            'client_key' => ['required', 'string', 'max:255'],
            'merchant_id' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'environment.required' => 'Environment is required',
            'environment.in' => 'Environment must be either sandbox or production',
            'server_key.required' => 'Server key is required',
            'server_key.max' => 'Server key must not exceed 255 characters',
            'client_key.required' => 'Client key is required',
            'client_key.max' => 'Client key must not exceed 255 characters',
            'merchant_id.required' => 'Merchant ID is required',
            'merchant_id.max' => 'Merchant ID must not exceed 255 characters',
            'is_active.boolean' => 'Is active field must be true or false',
        ];
    }
}

