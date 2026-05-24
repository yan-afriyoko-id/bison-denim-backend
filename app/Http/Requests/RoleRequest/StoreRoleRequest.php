<?php

namespace App\Http\Requests\RoleRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->check() && auth('sanctum')->user()->can('roles.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.unique' => 'This role name already exists.',
            'name.max' => 'Role name must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 500 characters.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
        ];
    }
}

