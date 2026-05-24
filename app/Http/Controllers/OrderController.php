<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource\OrderResource;
use App\Models\Order;
use App\Models\Voucher;
use App\Services\Order\OrderStockReductionService;
use App\Services\Payment\MidtransService;
use App\Services\Point\PointService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderStockReductionService $orderStockReductionService;

    public function __construct(OrderStockReductionService $orderStockReductionService)
    {
        $this->orderStockReductionService = $orderStockReductionService;
    }
    /**
     * Get user's orders
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
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
            $status = $request->input('status');
            $search = $request->input('search');

            $this->checkAndCancelExpiredOrders($user->id);

            $query = Order::with(['orderItems.review', 'user']);

            if (!$user->hasPermissionTo('orders.read')) {
                $query->where('fk_user_id', $user->id);
            }

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            // Search by order number
            if (!empty($search)) {
                $query->whereRaw(
                    'UPPER(order_number) LIKE ?',
                    ['%' . strtoupper($search) . '%']
                );
            }

            $orders = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => [
                    'orders' => OrderResource::collection($orders->items()),
                    'pagination' => [
                        'current_page' => $orders->currentPage(),
                        'last_page' => $orders->lastPage(),
                        'per_page' => $orders->perPage(),
                        'total' => $orders->total(),
                    ],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single order by ID
     * 
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $query = Order::with(['orderItems.review', 'user'])
                ->where('id', $id);

            if (!$user->hasPermissionTo('orders.read')) {
                $query->where('fk_user_id', $user->id);
            }

            $order = $query->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            if (
                $order->status === 'PENDING' &&
                $order->payment_status === 'PENDING' &&
                $order->payment_snap_token
            ) {

                try {
                    $isExpired = MidtransService::isTransactionExpired($order->order_number);

                    if ($isExpired) {
                        $order->refresh();

                        if ($order->status === 'CANCELLED' || $order->payment_status === 'FAILED') {
                        } else {
                            $order->update([
                                'payment_status' => 'FAILED',
                                'status' => 'CANCELLED',
                            ]);

                            $order->refresh();

                            if (!$order->relationLoaded('orderItems')) {
                                $order->load('orderItems');
                            }

                            $orderItems = $order->orderItems->map(function ($item) {
                                return [
                                    'variant_id' => $item->fk_variant_id,
                                    'qty' => $item->qty,
                                    'store_id' => $item->store_id,
                                ];
                            })->toArray();

                            if (!empty($orderItems)) {
                                $this->orderStockReductionService->releaseReservedStock($orderItems);
                            }

                            if ($order->fk_voucher_id) {
                                $voucher = Voucher::find($order->fk_voucher_id);
                                if ($voucher && $voucher->voucher_used > 0) {
                                    $voucher->decrement('voucher_used');
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => [
                    'order' => new OrderResource($order),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order by order number
     * 
     * @param string $orderNumber
     * @return JsonResponse
     */
    public function showByOrderNumber(string $orderNumber): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $order = Order::with(['orderItems.review', 'user'])
                ->where('order_number', $orderNumber)
                ->where('fk_user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => [
                    'order' => new OrderResource($order),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order payment status
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePaymentStatus(int $id, Request $request): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $order = Order::where('id', $id)
                ->where('fk_user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            // Validate payment status
            $validated = $request->validate([
                'payment_status' => 'required|in:PENDING,PAID,FAILED,CANCELLED,REFUNDED',
                'payment_method' => 'nullable|string|max:250',
                'payment_reference_code' => 'nullable|string|max:250',
            ]);

            $updateData = [
                'payment_status' => $validated['payment_status'],
                'payment_method' => $validated['payment_method'] ?? $order->payment_method,
                'payment_reference_code' => $validated['payment_reference_code'] ?? $order->payment_reference_code,
            ];

            if ($validated['payment_status'] === 'PAID' && $order->status === 'PENDING') {
                $updateData['status'] = 'PACKING';
            }

            $oldPaymentStatus = $order->payment_status;
            $order->update($updateData);

            if ($validated['payment_status'] === 'PAID' && $oldPaymentStatus !== 'PAID' && $order->fk_voucher_id) {
                $voucher = Voucher::find($order->fk_voucher_id);
                if ($voucher) {
                    $voucher->increment('voucher_used');
                }
            }

            if ($oldPaymentStatus === 'PAID' && $validated['payment_status'] !== 'PAID' && $order->fk_voucher_id) {
                $voucher = Voucher::find($order->fk_voucher_id);
                if ($voucher && $voucher->voucher_used > 0) {
                    $voucher->decrement('voucher_used');
                }
            }

            if ($validated['payment_status'] === 'PAID' && $oldPaymentStatus !== 'PAID') {
                try {
                    $pointService = app(PointService::class);
                    $pointService->addPointsFromOrder($user->id, $order->id, $order->total_amount);
                } catch (\Exception $e) {
                }
            }

            // Load relationships for response
            $order->load(['orderItems.review', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Order payment status updated successfully',
                'data' => [
                    'order' => new OrderResource($order),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order payment status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order status and resi number (Admin only)
     * 
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            // Validate request
            $validated = $request->validate([
                'status' => 'required|in:PENDING,PACKING,DELIVERING,DELIVERED,COMPLETED,CANCELLED',
                'courier_resi_number' => 'nullable|string|max:250',
            ]);

            $oldStatus = $order->status;
            $newStatus = $validated['status'];

            // Update order
            $updateData = [
                'status' => $newStatus,
            ];

            // Update resi number if provided
            if (isset($validated['courier_resi_number'])) {
                $updateData['courier_resi_number'] = $validated['courier_resi_number'];
            }

            $order->update($updateData);

            if ($newStatus === 'CANCELLED' && $oldStatus !== 'CANCELLED') {
                if (!$order->relationLoaded('orderItems')) {
                    $order->load('orderItems');
                }

                $orderItems = $order->orderItems->map(function ($item) {
                    return [
                        'variant_id' => $item->fk_variant_id,
                        'qty' => $item->qty,
                        'store_id' => $item->store_id,
                    ];
                })->toArray();

                if (!empty($orderItems)) {
                    $this->orderStockReductionService->releaseReservedStock($orderItems);
                }

                if ($order->fk_voucher_id && $order->payment_status === 'PAID') {
                    $voucher = Voucher::find($order->fk_voucher_id);
                    if ($voucher && $voucher->voucher_used > 0) {
                        $voucher->decrement('voucher_used');
                    }
                }
            }

            // Load relationships for response
            $order->load(['orderItems.review', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => [
                    'order' => new OrderResource($order),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel order by user (for PENDING orders only)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $order = Order::where('id', $id)
                ->where('fk_user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            if ($order->payment_status !== 'PENDING') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pesanan yang belum dibayar yang dapat dibatalkan',
                ], 422);
            }

            if ($order->status === 'CANCELLED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan sudah dibatalkan',
                ], 422);
            }

            $order->update([
                'payment_status' => 'FAILED',
                'status' => 'CANCELLED',
            ]);

            if (!$order->relationLoaded('orderItems')) {
                $order->load(['orderItems.review']);
            }

            $orderItems = $order->orderItems->map(function ($item) {
                return [
                    'variant_id' => $item->fk_variant_id,
                    'qty' => $item->qty,
                    'store_id' => $item->store_id,
                ];
            })->toArray();

            if (!empty($orderItems)) {
                $this->orderStockReductionService->releaseReservedStock($orderItems);
            }

            $order->load(['orderItems.review', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan',
                'data' => [
                    'order' => new OrderResource($order),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete order by user (for DELIVERED orders only)
     *
     * @param int $id
     * @return JsonResponse
     */
    public function complete(int $id): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $order = Order::where('id', $id)
                ->where('fk_user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            if ($order->status === 'COMPLETED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan sudah selesai',
                ], 422);
            }

            if ($order->status !== 'DELIVERED') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pesanan yang sudah diantar yang dapat dibatalkan',
                ], 422);
            }

            $order->update([
                'status' => 'COMPLETED',
            ]);

            if (!$order->relationLoaded('orderItems')) {
                $order->load(['orderItems.review']);
            }

            $orderItems = $order->orderItems->map(function ($item) {
                return [
                    'variant_id' => $item->fk_variant_id,
                    'qty' => $item->qty,
                    'store_id' => $item->store_id,
                ];
            })->toArray();

            if (!empty($orderItems)) {
                $this->orderStockReductionService->releaseReservedStock($orderItems);
            }

            $order->load(['orderItems.review', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan selesai',
                'data' => [
                    'order' => new OrderResource($order),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyelesaikan pesanan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function checkAndCancelExpiredOrders(int $userId): void
    {
        try {
            $cutoffTime = now()->subHours(48);

            $pendingOrders = Order::where('fk_user_id', $userId)
                ->where('payment_status', 'PENDING')
                ->where('status', '!=', 'CANCELLED')
                ->whereNotNull('payment_snap_token')
                ->whereNull('deleted_at')
                ->where('created_at', '>=', $cutoffTime)
                ->limit(20)
                ->get();

            if ($pendingOrders->isEmpty()) {
                return;
            }

            $cancelledCount = 0;

            foreach ($pendingOrders as $order) {
                try {
                    $order->refresh();

                    if ($order->status === 'CANCELLED' || $order->payment_status === 'FAILED') {
                        continue;
                    }

                    $isExpired = MidtransService::isTransactionExpired($order->order_number);

                    if ($isExpired) {
                        $order->update([
                            'payment_status' => 'FAILED',
                            'status' => 'CANCELLED',
                        ]);

                        $order->refresh();

                        if (!$order->relationLoaded('orderItems')) {
                            $order->load('orderItems');
                        }

                        $orderItems = $order->orderItems->map(function ($item) {
                            return [
                                'variant_id' => $item->fk_variant_id,
                                'qty' => $item->qty,
                                'store_id' => $item->store_id,
                            ];
                        })->toArray();

                        if (!empty($orderItems)) {
                            $this->orderStockReductionService->releaseReservedStock($orderItems);
                        }

                        $cancelledCount++;
                    }
                } catch (\Exception $e) {
                }
            }
        } catch (\Exception $e) {
        }
    }
}
