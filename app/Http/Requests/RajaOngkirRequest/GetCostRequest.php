<?php

namespace App\Http\Requests\RajaOngkirRequest;

use Illuminate\Foundation\Http\FormRequest;

class GetCostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination' => 'required|integer|min:1',
            'weight' => 'required|integer|min:1',
            'courier' => [
                'required',
                'string',
                'regex:/^[a-z]+(:[a-z]+)*$/i',
            ],
            'origin' => 'nullable|integer|min:1',
            'price' => 'nullable|string|in:lowest',
        ];
    }
}
