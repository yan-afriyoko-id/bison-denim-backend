<?php

namespace App\Http\Requests\CheckoutRequest;

use Illuminate\Foundation\Http\FormRequest;

class CreateCheckoutRequest extends FormRequest
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
            'data' => 'required|array',
            
            // Shipping Address
            'data.shipping' => 'required|array',
            'data.shipping.first_name' => 'required|string|max:250',
            'data.shipping.last_name' => 'nullable|string|max:250',
            'data.shipping.email' => 'required|email|max:250',
            'data.shipping.phone' => [
                'required',
                'string',
                'max:250',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }
                    
                    $cleaned = preg_replace('/[^0-9+]/', '', trim($value));
                    
                    if (!preg_match('/^\+?[0-9]{2,15}$/', $cleaned)) {
                        $fail('Shipping phone number must contain only digits and may start with +');
                    }
                },
            ],
            'data.shipping.address' => 'required|string',
            'data.shipping.city' => 'required|string|max:250',
            'data.shipping.province' => 'required|string|max:250',
            'data.shipping.postal_code' => 'required|string|max:250',
            'data.shipping.label_place' => 'nullable|string|max:250',
            'data.shipping.note_address' => 'nullable|string|max:250',
            
            // Billing Address (optional, can be same as shipping)
            'data.billing' => 'nullable|array',
            'data.billing.same_as_shipping' => 'nullable|boolean',
            'data.billing.first_name' => 'required_with:data.billing|string|max:250',
            'data.billing.last_name' => 'nullable|string|max:250',
            'data.billing.phone' => [
                'nullable',
                'string',
                'max:250',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }
                    
                    $cleaned = preg_replace('/[^0-9+]/', '', trim($value));
                    
                    if (!preg_match('/^\+?[0-9]{2,15}$/', $cleaned)) {
                        $fail('Billing phone number must contain only digits and may start with +');
                    }
                },
            ],
            'data.billing.address' => 'required_with:data.billing|string',
            'data.billing.city' => 'required_with:data.billing|string|max:250',
            'data.billing.province' => 'required_with:data.billing|string|max:250',
            'data.billing.postal_code' => 'required_with:data.billing|string|max:250',
            'data.billing.label_place' => 'nullable|string|max:250',
            'data.billing.note_address' => 'nullable|string|max:250',
            
            // Courier Information
            'data.courier' => 'required|array',
            'data.courier.agent' => 'required|string|max:250',
            'data.courier.service' => 'required|string|max:250',
            'data.courier.service_desc' => 'nullable|string|max:250',
            'data.courier.etd' => 'nullable|string|max:250',
            'data.courier.cost' => 'required|integer|min:0',
            
            // Products
            'data.products' => 'required|array|min:1',
            'data.products.*.variant_id' => 'required|integer|exists:product_variants,id',
            'data.products.*.qty' => 'required|integer|min:1',
            'data.products.*.note' => 'nullable|string|max:500',
            
            // Payment
            'data.payment_method' => 'nullable|string|max:250',
            
            // Voucher (optional)
            'data.voucher_id' => 'nullable|integer',
            
            // Notes
            'data.invoice_note' => 'nullable|string',
            'data.delivery_order_note' => 'nullable|string',
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
            'data.required' => 'Checkout data is required.',
            'data.shipping.required' => 'Shipping address is required.',
            'data.shipping.first_name.required' => 'Shipping first name is required.',
            'data.shipping.email.required' => 'Shipping email is required.',
            'data.shipping.phone.required' => 'Shipping phone is required.',
            'data.shipping.address.required' => 'Shipping address is required.',
            'data.shipping.city.required' => 'Shipping city is required.',
            'data.shipping.province.required' => 'Shipping province is required.',
            'data.shipping.postal_code.required' => 'Shipping postal code is required.',
            'data.courier.required' => 'Courier information is required.',
            'data.courier.agent.required' => 'Courier agent is required.',
            'data.courier.service.required' => 'Courier service is required.',
            'data.courier.cost.required' => 'Courier cost is required.',
            'data.products.required' => 'Products are required.',
            'data.products.min' => 'At least one product is required.',
            'data.products.*.variant_id.required' => 'Variant ID is required for each product.',
            'data.products.*.variant_id.exists' => 'Selected variant does not exist.',
            'data.products.*.qty.required' => 'Quantity is required for each product.',
            'data.products.*.qty.min' => 'Quantity must be at least 1.',
        ];
    }
}


