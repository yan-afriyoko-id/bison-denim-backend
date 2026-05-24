<?php

namespace App\Http\Resources\MainBannerResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainBannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'link_url' => $this->link_url,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}