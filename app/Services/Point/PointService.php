<?php

namespace App\Services\Point;

use App\Models\Order;
use App\Models\UserPoint;
use App\Models\UserPointTransaction;
use App\Interfaces\ConfigRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PointService
{
    protected PointCalculationService $pointCalculationService;
    protected ConfigRepositoryInterface $configRepository;

    public function __construct(
        PointCalculationService $pointCalculationService,
        ConfigRepositoryInterface $configRepository
    ) {
        $this->pointCalculationService = $pointCalculationService;
        $this->configRepository = $configRepository;
    }

    /**
     * Get minimum usable points from config
     * 
     * @return int Minimum usable points (default: 250000)
     */
    protected function getMinimumUsablePoints(): int
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
     * Add points to user after order is paid
     * Calculate points based on cumulative total
     * Note: Points are calculated from subtotal only (product price), not including shipping cost
     * 
     * @param int $userId
     * @param int $orderId
     * @param int $orderTotal Order subtotal (product price only, excluding shipping cost)
     * @return UserPointTransaction|null
     */
    public function addPointsFromOrder(int $userId, int $orderId, int $orderTotal): ?UserPointTransaction
    {
        // Check if points have already been added for this order to prevent duplicates
        $existingTransaction = UserPointTransaction::where('order_id', $orderId)
            ->where('transaction_type', 'EARNED')
            ->first();

        if ($existingTransaction) {
            return $existingTransaction;
        }

        DB::beginTransaction();
        try {
            // Get or create user point record
            $userPoint = UserPoint::firstOrCreate(
                ['user_id' => $userId],
                [
                    'points' => 0,
                    'earned_points' => 0,
                    'used_points' => 0,
                    'cumulative_total' => 0,
                ]
            );

            // Get previous cumulative total
            $previousCumulativeTotal = $userPoint->cumulative_total ?? 0;

            // Calculate new cumulative total
            $newCumulativeTotal = $previousCumulativeTotal + $orderTotal;

            // Calculate new points earned
            $pointsEarned = $this->pointCalculationService->calculateNewPoints(
                $previousCumulativeTotal,
                $newCumulativeTotal
            );

            // Update cumulative total
            $userPoint->cumulative_total = $newCumulativeTotal;

            // Add points if any
            if ($pointsEarned > 0) {
                $userPoint->points += $pointsEarned;
                $userPoint->earned_points += $pointsEarned;
            }

            $userPoint->save();

            // Create transaction record only if points earned
            $transaction = null;
            if ($pointsEarned > 0) {
                $order = Order::find($orderId);
                $orderNumber = $order ? $order->order_number : $orderId;

                $transaction = UserPointTransaction::create([
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'transaction_type' => 'EARNED',
                    'points' => $pointsEarned,
                    'description' => "Poin dari pembelian order #{$orderNumber}",
                    'reference_id' => (string) $orderId,
                ]);
            }

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deductPoints(int $userId, int $points, int $orderId, string $description = ''): UserPointTransaction
    {
        if ($points <= 0) {
            throw new \Exception('Jumlah point harus lebih dari 0');
        }

        return DB::transaction(function () use ($userId, $points, $orderId, $description) {
            $userPoint = UserPoint::where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($userPoint->points < $points) {
                throw new \Exception('Point tidak cukup. Tersedia: ' . $userPoint->points);
            }

            $finalDescription = $description;
            if (!$description) {
                $order = Order::find($orderId);
                $orderNumber = $order ? $order->order_number : $orderId;
                $finalDescription = "Digunakan untuk pembayaran order #{$orderNumber}";
            }

            $userPoint->decrement('points', $points);
            $userPoint->increment('used_points', $points);

            $transaction = UserPointTransaction::create([
                'user_id'          => $userId,
                'order_id'         => $orderId,
                'transaction_type' => 'USED',
                'points'           => -$points,
                'description'      => $finalDescription,
                'reference_id'     => (string) $orderId,
            ]);

            return $transaction;
        });
    }

    public function refundPoints(int $userId, int $points, int $orderId, string $description = ''): void
    {
        DB::transaction(function () use ($userId, $points, $orderId, $description) {
            $userPoint = UserPoint::where('user_id', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            $finalDescription = $description;
            if (!$description) {
                $order = Order::find($orderId);
                $orderNumber = $order ? $order->order_number : $orderId;
                $finalDescription = "Pengembalian point karena order #{$orderNumber} dibatalkan/expired";
            }

            $userPoint->increment('points', $points);
            $userPoint->decrement('used_points', $points);
            UserPointTransaction::create([
                'user_id'          => $userId,
                'order_id'         => $orderId,
                'transaction_type' => 'ADJUSTMENT',
                'points'           => +$points,
                'description'      => $finalDescription,
                'reference_id'     => (string) $orderId,
            ]);
        });
    }
}
