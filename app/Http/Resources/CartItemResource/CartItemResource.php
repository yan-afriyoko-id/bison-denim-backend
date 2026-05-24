<?php

namespace App\Http\Resources\CartItemResource;

use App\Http\Resources\ProductVariantResource\ProductVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'variant_id' => $this->variant_id,
            'qty' => $this->qty,
            'note' => $this->note,
            'store_id' => $this->store_id,
            'is_protected' => $this->is_protected,
            'variant' => new ProductVariantResource($this->variant),
        ];
    }
}