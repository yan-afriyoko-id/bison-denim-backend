<?php

namespace App\Http\Resources\ConfigResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Determine if this is an image config
        $imageConfigs = ['store_logo_website', 'store_favicon'];
        $isImageConfig = in_array($this->key, $imageConfigs);

        // Generate image URL - same pattern as BlogResource
        // BlogResource: 'cover_url' => $this->cover ? asset('storage/' . $this->cover) : null
        $valueImage = null;
        if ($isImageConfig && $this->value) {
            // Check if value is already a full URL
            if (filter_var($this->value, FILTER_VALIDATE_URL)) {
                // If it's already a full URL (e.g., placeholder URL), use it as is
                $valueImage = $this->value;
            } else {
                // Handle both old format (/storage/path) and new format (path)
                $path = $this->value;

                // Remove /storage/ prefix if exists (for backward compatibility with old data)
                if (str_starts_with($path, '/storage/')) {
                    $path = substr($path, 9); // Remove '/storage/'
                } elseif (str_starts_with($path, 'storage/')) {
                    $path = substr($path, 8); // Remove 'storage/'
                }

                // Generate full URL using asset() - same as BlogResource
                // BlogResource: asset('storage/' . $this->cover)
                $valueImage = asset('storage/' . $path);
            }
        }

        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->value,
            'value_image' => $valueImage,
            'description' => $this->description,
            'type' => $this->type,
            'casted_value' => $this->getCastedValue(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
