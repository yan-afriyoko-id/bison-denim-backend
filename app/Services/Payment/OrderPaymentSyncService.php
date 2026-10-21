<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentGroup;
use App\Models\Voucher;
use App\Services\Order\OrderStockReductionService;
use App\Services\Point\PointService;
use Illuminate\Support\Facades\Log;

class OrderPaymentSyncService
{
    public function __construct(
        private readonly OrderStockReductionService $orderStockReductionService,
        private readonly PointService $pointService,
    ) {
    }

    public function syncMidtransOrder(Order $order): bool
    {
        if (!$this->shouldSyncMidtransOrder($order)) {
            return false;
        }

        $status = MidtransService::checkTransactionStatus($order->order_number);

        if (!$status) {
            return false;
        }

        return $this->applyMidtransStatus($order, $status);
    }

    private function shouldSyncMidtransOrder(Order $order): bool
    {
        if ($order->payment_status !== 'PENDING') {
            return false;
        }

        if (!$order->payment_snap_token) {
            return false;
        }

        if (in_array($order->payment_method, ['xendit', 'doku'], true)) {
            return false;
        }

        return !$this->isXenditInvoiceUrl($order->payment_snap_token) &&
            !DokuService::isCheckoutUrl($order->payment_snap_token);
    }

    public function syncDokuOrder(Order $order): bool
    {
        if ($order->payment_status !== 'PENDING' || $order->payment_method !== 'doku') {
            return false;
        }

        $status = DokuService::checkStatus($order->order_number);

        if (!$status) {
            return false;
        }

        return $this->applyDokuStatus($order, $status);
    }

    /**
     * Apply a DOKU status payload (HTTP Notification or Check Status response) to an order.
     * FAILED is ignored on purpose: DOKU Checkout lets the customer retry on the same page.
     */
    public function applyDokuStatus(Order $order, array $status): bool
    {
        return match ($this->dokuState($status)) {
            'SUCCESS' => $this->markPaid($order, $this->dokuPaidStatus($status)),
            'EXPIRED' => $this->markExpired($order),
            default => false,
        };
    }

    public function applyDokuGroupStatus(PaymentGroup $group, array $status): bool
    {
        $group->loadMissing('orders.orderItems');

        switch ($this->dokuState($status)) {
            case 'SUCCESS':
                $paidStatus = $this->dokuPaidStatus($status);
                $expectedGross = (int) $group->orders->sum('total_amount');

                if ($paidStatus['gross_amount'] !== null && $paidStatus['gross_amount'] !== $expectedGross) {
                    Log::warning('DOKU GROSS AMOUNT MISMATCH (GROUP)', [
                        'group_number' => $group->group_number,
                        'expected' => $expectedGross,
                        'received' => $paidStatus['gross_amount'],
                    ]);

                    return false;
                }

                $group->update(['status' => 'PAID']);

                // Amount was verified against the group total above
                $paidStatus['gross_amount'] = null;
                foreach ($group->orders as $order) {
                    $this->markPaid($order, $paidStatus);
                }

                return true;

            case 'EXPIRED':
                $group->update(['status' => 'FAILED']);

                foreach ($group->orders as $order) {
                    $this->markExpired($order);
                }

                return true;
        }

        return false;
    }

    private function dokuState(array $status): string
    {
        $transactionStatus = strtoupper((string) ($status['transaction']['status'] ?? ''));
        $orderStatus = strtoupper((string) ($status['order']['status'] ?? ''));

        if ($transactionStatus === 'SUCCESS') {
            return 'SUCCESS';
        }

        if ($transactionStatus === 'EXPIRED' || $orderStatus === 'ORDER_EXPIRED') {
            return 'EXPIRED';
        }

        return $transactionStatus;
    }

    private function dokuPaidStatus(array $status): array
    {
        return [
            'gateway' => 'DOKU',
            'gross_amount' => isset($status['order']['amount']) ? (int) $status['order']['amount'] : null,
            'transaction_id' => $status['transaction']['original_request_id'] ?? null,
            'payment_type' => $status['channel']['id'] ?? $status['service']['id'] ?? null,
        ];
    }

    private function applyMidtransStatus(Order $order, array $status): bool
    {
        $transactionStatus = strtolower((string) ($status['transaction_status'] ?? ''));

        return match ($transactionStatus) {
            'capture', 'settlement' => $this->markPaid($order, $status),
            'expire' => $this->markExpired($order),
            'cancel', 'deny' => $this->markFailed($order),
            default => false,
        };
    }

    private function markPaid(Order $order, array $status): bool
    {
        $order->refresh();

        if ($order->payment_status === 'PAID') {
            return false;
        }

        $receivedGross = isset($status['gross_amount']) ? (int) $status['gross_amount'] : null;

        if ($receivedGross !== null && $receivedGross !== (int) $order->total_amount) {
            Log::warning(($status['gateway'] ?? 'MIDTRANS') . ' STATUS SYNC GROSS AMOUNT MISMATCH', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'expected' => (int) $order->total_amount,
                'received' => $receivedGross,
            ]);

            return false;
        }

        $order->update([
            'payment_status' => 'PAID',
            'payment_reference_code' => $status['transaction_id'] ?? $order->payment_reference_code,
            'payment_type' => $status['payment_type'] ?? $order->payment_type,
            'status' => 'PACKING',
        ]);

        $this->convertReservedStock($order);
        $this->incrementVoucherUsage($order);
        $this->applyPoints($order);

        return true;
    }

    private function markExpired(Order $order): bool
    {
        $order->refresh();

        if ($order->payment_status === 'PAID') {
            return false;
        }

        if ($order->status === 'CANCELLED' && $order->payment_status === 'FAILED') {
            return false;
        }

        $order->update([
            'payment_status' => 'FAILED',
            'status' => 'CANCELLED',
        ]);

        $this->releaseReservedStock($order);

        return true;
    }

    private function markFailed(Order $order): bool
    {
        $order->refresh();

        if ($order->payment_status === 'FAILED') {
            return false;
        }

        $order->update([
            'payment_status' => 'FAILED',
        ]);

        return true;
    }

    private function convertReservedStock(Order $order): void
    {
        $items = $this->orderItemsPayload($order);

        if (!empty($items)) {
            $this->orderStockReductionService->convertReservedToActual($items);
        }
    }

    private function releaseReservedStock(Order $order): void
    {
        $items = $this->orderItemsPayload($order);

        if (!empty($items)) {
            $this->orderStockReductionService->releaseReservedStock($items);
        }
    }

    private function orderItemsPayload(Order $order): array
    {
        if (!$order->relationLoaded('orderItems')) {
            $order->load('orderItems');
        }

        return $order->orderItems->map(function ($item) {
            return [
                'variant_id' => $item->fk_variant_id,
                'qty' => $item->qty,
                'store_id' => $item->store_id,
            ];
        })->toArray();
    }

    private function incrementVoucherUsage(Order $order): void
    {
        if (!$order->fk_voucher_id) {
            return;
        }

        $voucher = Voucher::find($order->fk_voucher_id);

        if ($voucher) {
            $voucher->increment('voucher_used');
        }
    }

    private function applyPoints(Order $order): void
    {
        try {
            $this->pointService->addPointsFromOrder($order->fk_user_id, $order->id, $order->total_amount);
            $this->pointService->deductPoints(
                userId: $order->fk_user_id,
                points: $order->points_used,
                orderId: $order->id,
                description: "Digunakan untuk pembayaran order #{$order->order_number}"
            );
        } catch (\Exception $e) {
            Log::error('PAYMENT STATUS SYNC: Failed to apply points', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function isXenditInvoiceUrl(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false && str_contains($value, 'xendit.co');
    }
}
