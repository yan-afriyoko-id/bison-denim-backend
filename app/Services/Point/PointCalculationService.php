<?php

namespace App\Services\Point;

use App\Models\UserPoint;
use App\Interfaces\ConfigRepositoryInterface;

class PointCalculationService
{
    protected ConfigRepositoryInterface $configRepository;
    
    public function __construct(ConfigRepositoryInterface $configRepository)
    {
        $this->configRepository = $configRepository;
    }
    
    /**
     * Get points per million from config
     * 
     * @return int Points per million (default: 25000)
     */
    protected function getPointsPerMillion(): int
    {
        try {
            $config = $this->configRepository->getByKey('point_points_per_million');
            if ($config && $config->value) {
                return (int) $config->value;
            }
        } catch (\Exception $e) {
            // Fallback to default
        }
        return 25000; // Default: 25.000 poin per 1 juta
    }
    
    /**
     * Get million threshold from config
     * 
     * @return int Million threshold (default: 1000000)
     */
    protected function getMillionThreshold(): int
    {
        try {
            $config = $this->configRepository->getByKey('point_million_threshold');
            if ($config && $config->value) {
                return (int) $config->value;
            }
        } catch (\Exception $e) {
            // Fallback to default
        }
        return 1000000; // Default: 1 juta
    }
    
    /**
     * Calculate new points earned from cumulative total
     * Formula: Setiap kelipatan X juta baru dari total kumulatif = Y poin
     * 
     * @param int $previousCumulativeTotal Total kumulatif sebelum order ini
     * @param int $newCumulativeTotal Total kumulatif setelah order ini
     * @return int Points earned
     */
    public function calculateNewPoints(int $previousCumulativeTotal, int $newCumulativeTotal): int
    {
        $millionThreshold = $this->getMillionThreshold();
        $pointsPerMillion = $this->getPointsPerMillion();
        
        // Hitung kelipatan sebelumnya
        $previousMilestones = (int) floor($previousCumulativeTotal / $millionThreshold);
        
        // Hitung kelipatan sekarang
        $newMilestones = (int) floor($newCumulativeTotal / $millionThreshold);
        
        // Poin baru = selisih kelipatan × points per million
        $newMilestonesEarned = $newMilestones - $previousMilestones;
        
        return $newMilestonesEarned * $pointsPerMillion;
    }
    
    /**
     * Get minimum usable points from config
     * 
     * @return int Minimum usable points (default: 250000)
     */
    public function getMinimumUsablePoints(): int
    {
        try {
            $config = $this->configRepository->getByKey('point_minimum_usable_points');
            if ($config && $config->value) {
                return (int) $config->value;
            }
        } catch (\Exception $e) {
            // Fallback to default
        }
        return 250000; // Default: 250.000 poin
    }
    
    /**
     * Check if user can use points
     * 
     * @param UserPoint $userPoint
     * @return bool
     */
    public function canUsePoints(UserPoint $userPoint): bool
    {
        $minimumUsablePoints = $this->getMinimumUsablePoints();
        return $userPoint->points >= $minimumUsablePoints;
    }
}

