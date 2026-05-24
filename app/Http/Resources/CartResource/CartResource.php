<?php

namespace App\Http\Resources\CartResource;

use App\Http\Resources\CartItemResource\CartItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->items),
        ];
    }
}