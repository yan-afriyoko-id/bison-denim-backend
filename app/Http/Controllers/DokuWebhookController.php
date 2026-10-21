<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentGroup;
use App\Services\Payment\DokuService;
use App\Services\Payment\OrderPaymentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    public function __construct(
        private readonly OrderPaymentSyncService $orderPaymentSyncService,
    ) {
    }

    public function handle(Request $request)
    {
        try {
            if (!DokuService::verifyNotification($request, $this->notificationPath($request))) {
                Log::error('DOKU INVALID SIGNATURE', [
                    'request_id' => $request->header('Request-Id'),
                    'invoice_number' => $request->input('order.invoice_number'),
                ]);
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            $payload = $request->all();
            $invoiceNumber = $payload['order']['invoice_number'] ?? null;

            if (!$invoiceNumber) {
                return response()->json(['message' => 'Invalid payload'], 400);
            }

            // Try to find single order by order_number first
            $order = Order::where('order_number', $invoiceNumber)->first();

            if ($order) {
                $this->orderPaymentSyncService->applyDokuStatus($order, $payload);
            } else {
                $paymentGroup = PaymentGroup::where('group_number', $invoiceNumber)->first();
                if (!$paymentGroup) {
                    Log::warning('DOKU ORDER/GROUP NOT FOUND', [
                        'invoice_number' => $invoiceNumber,
                    ]);
                    return response()->json(['message' => 'Order or payment group not found'], 404);
                }

                $this->orderPaymentSyncService->applyDokuGroupStatus($paymentGroup, $payload);
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('DOKU WEBHOOK ERROR', [
                'error' => $e->getMessage(),
                'invoice_number' => $request->input('order.invoice_number'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request-Target used in the signature is the notification URL path registered in the DOKU Back Office.
     */
    private function notificationPath(Request $request): string
    {
        $configuredUrl = config('services.doku.notification_url');
        $path = $configuredUrl ? parse_url($configuredUrl, PHP_URL_PATH) : null;

        return $path ?: '/' . ltrim($request->path(), '/');
    }
}
