<?php

namespace App\Http\Requests\RajaOngkirRequest;

use Illuminate\Foundation\Http\FormRequest;

class GetDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => 'required|integer|min:1',
            'id' => 'nullable|integer|min:1',
        ];
    }
}
