<?php

namespace App\Http\Resources\ProductVariantResource;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'variant_id' => $this->variant_id,
            'store_id' => $this->store_id,
            'qty' => $this->qty,
            'reserved_qty' => $this->reserved_qty,
            'store' => $this->whenLoaded('store', fn() => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'code' => $this->store->code ?? null,
            ]),
        ];
    }
}