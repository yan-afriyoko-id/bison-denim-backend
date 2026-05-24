<?php

namespace App\Http\Requests\CartRequest;

use Illuminate\Foundation\Http\FormRequest;

class CreateCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow both authenticated and guest users
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data' => 'required|array|min:1',
            'data.*.variant_id' => 'required|integer|exists:product_variants,id',
            'data.*.qty' => 'required|integer|min:1',
            'data.*.note' => 'nullable|string|max:500',
            'data.*.store_id' => 'nullable|integer|exists:stores,id',
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
            'data.required' => 'Cart data is required.',
            'data.array' => 'Cart data must be an array.',
            'data.min' => 'Cart must contain at least one item.',
            'data.*.variant_id.required' => 'Variant ID is required for each item.',
            'data.*.variant_id.exists' => 'Selected variant does not exist.',
            'data.*.qty.required' => 'Quantity is required for each item.',
            'data.*.qty.min' => 'Quantity must be at least 1.',
            'data.*.note.max' => 'Note cannot exceed 500 characters.',
        ];
    }
}


