<?php

namespace App\Http\Resources;

use App\Interfaces\ConfigRepositoryInterface;
use App\Models\UserPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class UserPointResource extends JsonResource
{
    /**
     * Get minimum usable points from config
     * 
     * @return int
     */
    protected function getMinimumUsablePoints(): int
    {
        try {
            $configRepository = app(ConfigRepositoryInterface::class);
            $config = $configRepository->getByKey('point_minimum_usable_points');
            if ($config && $config->value) {
                return (int) $config->value;
            }
        } catch (\Exception $e) {
            // Fallback to default
        }
        return 250000; // Default: 250.000 poin
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $minimumUsablePoints = $this->getMinimumUsablePoints();
        $millionThreshold = app(ConfigRepositoryInterface::class)
            ->getByKey('point_million_threshold')?->value ?? 1000000;

        $pointsPerMillion = app(ConfigRepositoryInterface::class)
            ->getByKey('point_points_per_million')?->value ?? 25000;
        $hasPointTransaction = UserPointTransaction::where('user_id', $this->user_id)
            ->exists();

        $canUsePoints = $hasPointTransaction
            ? $this->points > 0
            : $this->points >= $minimumUsablePoints;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'points' => $this->points,
            'cumulative_total' => $this->cumulative_total,
            'minimum_usable_points' => $minimumUsablePoints,
            'is_active' => $this->is_active,
            'can_use_points' => $canUsePoints,
            'million_threshold' => (int) $millionThreshold,
            'points_per_million' => (int) $pointsPerMillion,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
