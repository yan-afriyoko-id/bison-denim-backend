<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserPointResource;
use App\Http\Resources\UserPointTransactionResource;
use App\Models\UserPoint;
use App\Models\UserPointTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointController extends Controller
{
    /**
     * Get user's point balance
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $userPoint = UserPoint::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'points' => 0,
                    'earned_points' => 0,
                    'used_points' => 0,
                    'cumulative_total' => 0,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'User points retrieved successfully',
                'data' => new UserPointResource($userPoint),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user points',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's point transaction history
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function transactions(Request $request): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $perPage = $request->input('per_page', 15);
            $transactionType = $request->input('transaction_type');

            $query = UserPointTransaction::where('user_id', $user->id);

            if ($transactionType) {
                $query->where('transaction_type', $transactionType);
            }

            $transactions = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Point transactions retrieved successfully',
                'data' => [
                    'transactions' => UserPointTransactionResource::collection($transactions->items()),
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'last_page' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total(),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve point transactions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
