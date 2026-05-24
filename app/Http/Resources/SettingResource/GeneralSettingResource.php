<?php

namespace App\Http\Resources\SettingResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingResource extends JsonResource
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
            'phone' => $this->phone,
            'email' => $this->email,
            'location' => $this->location,
            'social_media' => [
                'instagram' => $this->instagram,
                'tiktok' => $this->tiktok,
                'facebook' => $this->facebook,
                'youtube' => $this->youtube,
                'pinterest' => $this->pinterest,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

