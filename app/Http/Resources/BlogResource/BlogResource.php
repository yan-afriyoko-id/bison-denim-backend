<?php

namespace App\Http\Resources\BlogResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'cover' => $this->cover,
            'cover_url' => $this->cover ? asset('storage/' . $this->cover) : null,
            'title' => $this->title,
            'short_desc' => $this->short_desc,
            'long_desc' => $this->long_desc,
            'fk_category' => $this->fk_category,
            'slug' => $this->slug,
            'status' => $this->status,
            'hot_news' => $this->hot_news,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
                'description' => $this->category->description,
                'status' => $this->category->status,
            ] ?? null,
            'created_date' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
