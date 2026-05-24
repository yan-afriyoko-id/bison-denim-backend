<?php

namespace App\Http\Resources\StoreResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'status' => $this->status,
            'description' => $this->description,
            'city_id' => $this->city_id,
            
            // Relationships
            'products' => $this->whenLoaded('products', function () {
                return $this->products->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'status' => $product->status,
                ]);
            }),
            
            'timestamps' => [
                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            ],
        ];
    }
}

