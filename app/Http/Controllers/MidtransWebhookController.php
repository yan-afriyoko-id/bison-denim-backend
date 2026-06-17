<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Voucher;
use App\Models\PaymentGroup;
use App\Services\Point\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            $settings = config('settings');
            $serverKey = $settings['midtrans_server_key'] ?? env('MIDTRANS_SERVER_KEY');
            $isProduction = $settings['midtrans_is_production'] ?? env('MIDTRANS_IS_PRODUCTION', false);
            $isProduction = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN);

            if (empty($serverKey)) {
                return response()->json(['message' => 'Invalid server key'], 500);
            }

            Config::$serverKey = $serverKey;
            Config::$isProduction = $isProduction;

            // Try to find single order by order_number first
            $order = Order::where('order_number', $request->order_id)->first();

            // If single order not found, try payment group
            $paymentGroup = null;
            if (!$order) {
                $paymentGroup = PaymentGroup::where('group_number', $request->order_id)->first();
                if (!$paymentGroup) {
                    Log::warning('MIDTRANS ORDER/GROUP NOT FOUND', [
                        'order_id' => $request->order_id,
                    ]);
                    return response()->json(['message' => 'Order or payment group not found'], 404);
                }
            }

            $expectedSignature = hash(
                'sha512',
                $request->order_id .
                    $request->status_code .
                    $request->gross_amount .
                    Config::$serverKey
            );

            if ($request->signature_key !== $expectedSignature) {
                Log::error('MIDTRANS INVALID SIGNATURE', [
                    'order_id' => $request->order_id,
                    'order_number' => $order?->order_number ?? $paymentGroup?->group_number,
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            if ($order) {
                $oldPaymentStatus = $order->payment_status;

                switch ($request->transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $expectedGross = (int) $order->total_amount;
                        $receivedGross = (int) $request->gross_amount;
                        if ($receivedGross !== $expectedGross) {
                            Log::warning('MIDTRANS GROSS AMOUNT MISMATCH', [
                                'order_id' => $order->id,
                                'order_number' => $order->order_number,
                                'expected' => $expectedGross,
                                'received' => $receivedGross,
                            ]);
                            return response()->json(['message' => 'Amount mismatch'], 400);
                        }

                        $order->update([
                            'payment_status' => 'PAID',
                            'payment_reference_code' => $request->transaction_id,
                            'payment_type' => $request->payment_type,
                            'status' => 'PACKING',
                        ]);

                        if ($oldPaymentStatus !== 'PAID') {
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
                                $orderStockReductionService = app(\App\Services\Order\OrderStockReductionService::class);
                                $orderStockReductionService->convertReservedToActual($orderItems);
                            }
                        }

                        // Mark voucher as used when payment becomes PAID
                        if ($oldPaymentStatus !== 'PAID' && $order->fk_voucher_id) {
                            $voucher = Voucher::find($order->fk_voucher_id);
                            if ($voucher) {
                                $voucher->increment('voucher_used');
                            }
                        }

                        // Add points if order becomes PAID (only if not already PAID before)
                        if ($oldPaymentStatus !== 'PAID') {
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
                                Log::error('MIDTRANS WEBHOOK: Failed to add points', [
                                    'order_id' => $order->id,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        }

                        break;

                    case 'pending':
                        $order->update([
                            'payment_status' => 'PENDING',
                        ]);
                        break;

                    case 'expire':
                        $order->refresh();

                        if ($order->status === 'CANCELLED' && $order->payment_status === 'FAILED') {
                            break;
                        }

                        // Auto-cancel order when payment expires
                        if ($order->status !== 'CANCELLED') {
                            $order->update([
                                'payment_status' => 'FAILED',
                                'status' => 'CANCELLED',
                            ]);
                        }

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
                            $orderStockReductionService = app(\App\Services\Order\OrderStockReductionService::class);
                            $orderStockReductionService->releaseReservedStock($orderItems);
                        }

                        // Restore voucher if payment fails (was PAID before)
                        if ($oldPaymentStatus === 'PAID' && $order->fk_voucher_id) {
                            $voucher = Voucher::find($order->fk_voucher_id);
                            if ($voucher && $voucher->voucher_used > 0) {
                                $voucher->decrement('voucher_used');
                            }
                        }
                        break;

                    case 'cancel':
                    case 'deny':
                        $order->update([
                            'payment_status' => 'FAILED',
                        ]);

                        // Restore voucher if payment fails (was PAID before)
                        if ($oldPaymentStatus === 'PAID' && $order->fk_voucher_id) {
                            $voucher = Voucher::find($order->fk_voucher_id);
                            if ($voucher && $voucher->voucher_used > 0) {
                                $voucher->decrement('voucher_used');
                            }
                        }
                        break;
                }
            } else {
                // Process payment group
                $group = $paymentGroup;
                if (!$group->relationLoaded('orders')) {
                    $group->load('orders.orderItems');
                }

                $oldStatus = $group->status;

                switch ($request->transaction_status) {
                    case 'capture':
                    case 'settlement':
                        $expectedGross = (int) $group->orders->sum('total_amount');
                        $receivedGross = (int) $request->gross_amount;
                        if ($receivedGross !== $expectedGross) {
                            Log::warning('MIDTRANS GROSS AMOUNT MISMATCH (GROUP)', [
                                'group_number' => $group->group_number,
                                'expected' => $expectedGross,
                                'received' => $receivedGross,
                            ]);
                            return response()->json(['message' => 'Amount mismatch'], 400);
                        }

                        $group->update(['status' => 'PAID']);

                        // Process each linked order similarly to single-order flow
                        foreach ($group->orders as $order) {
                            $prevStatus = $order->payment_status;
                            $order->update([
                                'payment_status' => 'PAID',
                                'payment_reference_code' => $request->transaction_id,
                                'payment_type' => $request->payment_type,
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
                                    $orderStockReductionService = app(\App\Services\Order\OrderStockReductionService::class);
                                    $orderStockReductionService->convertReservedToActual($orderItems);
                                }
                            }

                            // voucher
                            if ($prevStatus !== 'PAID' && $order->fk_voucher_id) {
                                $voucher = Voucher::find($order->fk_voucher_id);
                                if ($voucher) {
                                    $voucher->increment('voucher_used');
                                }
                            }

                            // points
                            if ($prevStatus !== 'PAID') {
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
                                    Log::error('MIDTRANS WEBHOOK: Failed to add points', [
                                        'order_id' => $order->id,
                                        'error' => $e->getMessage(),
                                    ]);
                                }
                            }
                        }
                        break;

                    case 'pending':
                        $group->update(['status' => 'PENDING']);
                        foreach ($group->orders as $order) {
                            $order->update(['payment_status' => 'PENDING']);
                        }
                        break;

                    case 'expire':
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
                                $orderStockReductionService = app(\App\Services\Order\OrderStockReductionService::class);
                                $orderStockReductionService->releaseReservedStock($orderItems);
                            }

                            // restore voucher if was used
                            if ($order->fk_voucher_id) {
                                $voucher = Voucher::find($order->fk_voucher_id);
                                if ($voucher && $voucher->voucher_used > 0) {
                                    $voucher->decrement('voucher_used');
                                }
                            }

                            
                        }
                        break;

                    case 'cancel':
                    case 'deny':
                        $group->update(['status' => 'FAILED']);
                        foreach ($group->orders as $order) {
                            $order->update(['payment_status' => 'FAILED']);
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
            Log::error('MIDTRANS WEBHOOK ERROR', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id ?? null,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
