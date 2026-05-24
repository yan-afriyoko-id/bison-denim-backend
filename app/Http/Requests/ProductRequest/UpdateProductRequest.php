<?php

namespace App\Http\Requests\ProductRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $productId = $this->route('id');

        // Build unique rule for slug - only ignore current product ID if it exists
        $slugRule = ['sometimes', 'string', 'max:250'];
        if ($productId && is_numeric($productId)) {
            $slugRule[] = 'unique:products,slug,' . $productId . ',id';
        } else {
            $slugRule[] = 'unique:products,slug';
        }

        return [
            'name' => ['sometimes', 'string', 'max:250'],
            'slug' => $slugRule,
            'is_freeshiping' => ['nullable', 'in:ACTIVE,INACTIVE'],
            'product_information' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string'],
            'material' => ['nullable', 'string', 'max:255'],
            'finishing' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'type_weight' => ['nullable', 'in:GRAM,KG'],
            'size_long' => ['nullable', 'numeric', 'min:0'],
            'size_tall' => ['nullable', 'numeric', 'min:0'],
            'size_wide' => ['nullable', 'numeric', 'min:0'],
            'type_size' => ['nullable', 'in:CM,M'],
            'package_long' => ['nullable', 'numeric', 'min:0'],
            'package_wide' => ['nullable', 'numeric', 'min:0'],
            'package_tall' => ['nullable', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:255'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'base_strike_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'integer', 'min:0'],
            'tags' => ['nullable', 'string'],
            'product_protection_percent' => [
                'sometimes',
                'integer',
                'min:0',
                'max:100',
            ],
            'status' => ['nullable', 'in:PUBLISH,INACTIVE,DRAFT'],
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
            'name.string' => 'Product name must be a string.',
            'name.max' => 'Product name must not exceed 250 characters.',
            'slug.string' => 'Product slug must be a string.',
            'slug.unique' => 'Product slug already exists.',
            'is_freeshiping.in' => 'Free shipping value must be ACTIVE or INACTIVE.',
            'material.max' => 'Material must not exceed 255 characters.',
            'finishing.max' => 'Finishing must not exceed 255 characters.',
            'color.max' => 'Color must not exceed 255 characters.',
            'type_weight.in' => 'Weight type must be GRAM or KG.',
            'type_size.in' => 'Size type must be CM or M.',
            'sku.max' => 'SKU must not exceed 255 characters.',
            'status.in' => 'Status must be PUBLISH, INACTIVE, or DRAFT.',
        ];
    }
}

