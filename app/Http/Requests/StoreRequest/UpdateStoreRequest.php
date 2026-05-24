<?php

namespace App\Http\Requests\StoreRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
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
        $storeId = $this->route('id');

        // Build unique rule for code - only ignore current store ID if it exists
        $codeRule = ['sometimes', 'string', 'max:100'];
        if ($storeId && is_numeric($storeId)) {
            $codeRule[] = 'unique:stores,code,' . $storeId . ',id';
        } else {
            $codeRule[] = 'unique:stores,code';
        }

        return [
            'name' => ['sometimes', 'string', 'max:250'],
            'code' => $codeRule,
            'email' => ['nullable', 'email', 'max:250'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'in:ACTIVE,INACTIVE'],
            'description' => ['nullable', 'string'],
            'city_id' => ['nullable', 'integer', 'min:1'],
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
            'name.string' => 'Store name must be a string.',
            'name.max' => 'Store name must not exceed 250 characters.',
            'code.unique' => 'Store code already exists.',
            'email.email' => 'Store email must be a valid email address.',
            'status.in' => 'Status must be ACTIVE or INACTIVE.',
        ];
    }
}

