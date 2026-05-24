<?php

namespace App\Http\Resources\BlogResource;

use App\Models\CategoryBlog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryBlogResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'blogs' => BlogResource::collection($this->whenLoaded('blogs')),
            'created_date' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
