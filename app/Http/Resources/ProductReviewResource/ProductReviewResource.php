<?php

namespace App\Http\Resources\ProductReviewResource;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'rating'      => $this->rating,
            'comment'     => $this->comment,
            'review_date' => $this->review_date->format('d M Y'),
            'user'        => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'is_approved' => $this->is_approved,
        ];
    }
}