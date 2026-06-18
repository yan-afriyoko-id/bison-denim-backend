<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Voucher;
use App\Models\PaymentGroup;
use App\Services\Point\PointService;
use App\Services\Order\OrderStockReductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();
            $externalId = $payload['external_id'] ?? null;
            $status = $payload['status'] ?? null;
            $xenditId = $payload['id'] ?? null;

            if (!$externalId || !$status) {
                return response()->json(['message' => 'Invalid payload'], 400);
            }

            // Try to find single order by order_number first
            $order = Order::where('order_number', $externalId)->first();

            $paymentGroup = null;
            if (!$order) {
                $paymentGroup = PaymentGroup::where('group_number', $externalId)->first();
                if (!$paymentGroup) {
                    Log::warning('XENDIT ORDER/GROUP NOT FOUND', [
                        'external_id' => $externalId,
                    ]);
                    return response()->json(['message' => 'Order or payment group not found'], 404);
                }
            }

            if ($order) {
                $oldPaymentStatus = $order->payment_status;

                switch ($status) {
                    case 'PAID':
                    case 'SETTLED':
                        if ($oldPaymentStatus !== 'PAID') {
                            $order->update([
                                'payment_status' => 'PAID',
                                'payment_reference_code' => $xenditId,
                                'status' => 'PACKING',
                            ]);

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
                                $stockService = app(OrderStockReductionService::class);
                                $stockService->convertReservedToActual($orderItems);
                            }

                            if ($order->fk_voucher_id) {
                                $voucher = Voucher::find($order->fk_voucher_id);
                                if ($voucher) {
                                    $voucher->increment('voucher_used');
                                }
                            }

                            try {
                                $pointService = app(PointService::class);
                                $pointService->addPointsFromOrder($order->fk_user_id, $order->id, $order->total_amount);
                                $pointService->deductPoints(
                                    userId: $order->fk_user_id,
                                    points: $order->points_used,
                                    orderId: $order->id,
                                    description: "Digunakan untuk pembayaran order #{$order->order_number}"
                                );
                            } catch (\Exception $e) {
                                Log::error('XENDIT WEBHOOK: Failed to add points', [
                                    'order_id' => $order->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }
                        break;

                    case 'EXPIRED':
                        if ($order->status !== 'CANCELLED') {
                            $order->update([
                                'payment_status' => 'FAILED',
                                'status' => 'CANCELLED',
                            ]);

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
                                $stockService = app(OrderStockReductionService::class);
                                $stockService->releaseReservedStock($orderItems);
                            }

                            if ($order->fk_voucher_id) {
                                $voucher = Voucher::find($order->fk_voucher_id);
                                if ($voucher && $voucher->voucher_used > 0) {
                                    $voucher->decrement('voucher_used');
                                }
                            }
                        }
                        break;
                }
            } elseif ($paymentGroup) {
                $group = $paymentGroup;
                if (!$group->relationLoaded('orders')) {
                    $group->load('orders.orderItems');
                }

                switch ($status) {
                    case 'PAID':
                    case 'SETTLED':
                        $group->update(['status' => 'PAID']);

                        foreach ($group->orders as $order) {
                            $prevStatus = $order->payment_status;
                            $order->update([
                                'payment_status' => 'PAID',
                                'payment_reference_code' => $xenditId,
                                'status' => 'PACKING',
                            ]);

                            if ($prevStatus !== 'PAID') {
                                $orderItems = $order->orderItems->map(function ($item) {
                                    return [
                                        'variant_id' => $item->fk_variant_id,
                                        'qty' => $item->qty,
                                        'store_id' => $item->store_id,
                                    ];
                                })->toArray();

                                if (!empty($orderItems)) {
                                    $stockService = app(OrderStockReductionService::class);
                                    $stockService->convertReservedToActual($orderItems);
                                }

                                if ($order->fk_voucher_id) {
                                    $voucher = Voucher::find($order->fk_voucher_id);
                                    if ($voucher) {
                                        $voucher->increment('voucher_used');
                                    }
                                }

                                try {
                                    $pointService = app(PointService::class);
                                    $pointService->addPointsFromOrder($order->fk_user_id, $order->id, $order->total_amount);
                                    $pointService->deductPoints(
                                        userId: $order->fk_user_id,
                                        points: $order->points_used,
                                        orderId: $order->id,
                                        description: "Digunakan untuk pembayaran order #{$order->order_number}"
                                    );
                                } catch (\Exception $e) {
                                    Log::error('XENDIT WEBHOOK: Failed to add points', [
                                        'order_id' => $order->id,
                                        'error' => $e->getMessage(),
                                    ]);
                                }
                            }
                        }
                        break;

                    case 'EXPIRED':
                        $group->update(['status' => 'FAILED']);
                        foreach ($group->orders as $order) {
                            $order->update(['payment_status' => 'FAILED', 'status' => 'CANCELLED']);

                            $orderItems = $order->orderItems->map(function ($item) {
                                return [
                                    'variant_id' => $item->fk_variant_id,
                                    'qty' => $item->qty,
                                    'store_id' => $item->store_id,
                                ];
                            })->toArray();

                            if (!empty($orderItems)) {
                                $stockService = app(OrderStockReductionService::class);
                                $stockService->releaseReservedStock($orderItems);
                            }

                            if ($order->fk_voucher_id) {
                                $voucher = Voucher::find($order->fk_voucher_id);
                                if ($voucher && $voucher->voucher_used > 0) {
                                    $voucher->decrement('voucher_used');
                                }
                            }
                        }
                        break;
                }
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('XENDIT WEBHOOK ERROR', [
                'error' => $e->getMessage(),
                'external_id' => $request->input('external_id'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
