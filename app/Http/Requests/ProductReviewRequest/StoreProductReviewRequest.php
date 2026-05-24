<?php

namespace App\Http\Requests\ProductReviewRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'rating'        => 'required|integer|between:1,5',
            'comment'       => 'nullable|string|max:2000',
            'order_item_id' => 'required|integer|exists:order_items,id',
        ];
    }
}