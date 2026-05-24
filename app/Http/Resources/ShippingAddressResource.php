<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'label_place' => $this->label_place,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'note_address' => $this->note_address,
            'is_primary' => $this->is_primary,
            'province_id' => $this->province_id,
            'province_label' => $this->province_label,
            'city_id' => $this->city_id,
            'city_label' => $this->city_label,
            'district_id' => $this->district_id,
            'district_label' => $this->district_label,
            'sub_district_id' => $this->sub_district_id,
            'sub_district_label' => $this->sub_district_label,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
