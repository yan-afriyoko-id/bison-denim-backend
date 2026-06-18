<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserShippingAddress;
use App\Models\Voucher;
use App\Services\Order\OrderStockReductionService;
use App\Services\Payment\MidtransService;
use App\Services\Payment\OrderPaymentSyncService;
use App\Services\Payment\XenditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected OrderStockReductionService $orderStockReductionService;
    protected OrderPaymentSyncService $orderPaymentSyncService;

    public function __construct(
        OrderStockReductionService $orderStockReductionService,
        OrderPaymentSyncService $orderPaymentSyncService
    )
    {
        $this->orderStockReductionService = $orderStockReductionService;
        $this->orderPaymentSyncService = $orderPaymentSyncService;
    }

    /**
     * Create a payment for multiple orders (payment group)
     * Supports both Midtrans (snap_token) and Xendit (invoice_url)
     * Expects JSON: { order_ids: [1,2,3], payment_method?: "midtrans"|"xendit" }
     */
    public function createMidtransSnapForOrders(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
            'payment_method' => 'nullable|string|in:midtrans,xendit',
        ]);

        $paymentMethod = $data['payment_method'] ?? 'midtrans';
        $orderIds = $data['order_ids'];

        $orders = Order::whereIn('id', $orderIds)->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Orders not found'], 404);
        }

        // Only allow creating group for orders that are payable (pending)
        $invalid = $orders->first(fn($o) => $o->payment_status !== 'PENDING');
        if ($invalid) {
            return response()->json(['message' => 'One or more orders are not in payable state'], 422);
        }

        $itemDetails = [];
        $grossAmount = 0;

        foreach ($orders as $order) {
            if (!$order->relationLoaded('orderItems')) {
                $order->load('orderItems');
            }

            foreach ($order->orderItems as $item) {
                $itemDetails[] = [
                    'id' => !empty($item->sku) ? (string) $item->sku : (string) ($item->fk_product_id ?? $item->id),
                    'price' => (int) $item->purchase_price,
                    'quantity' => (int) $item->qty,
                    'name' => $item->product_name,
                ];
            }

            if ($order->shipping_cost > 0) {
                $itemDetails[] = [
                    'id' => 'shipping_' . $order->order_number,
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Shipping for ' . $order->order_number,
                ];
            }

            if (($order->discount_amount ?? 0) > 0) {
                $itemDetails[] = [
                    'id' => 'voucher_' . $order->order_number,
                    'price' => -1 * (int) $order->discount_amount,
                    'quantity' => 1,
                    'name' => 'Voucher Discount for ' . $order->order_number,
                ];
            }

            if (($order->product_protection_amount ?? 0) > 0) {
                $itemDetails[] = [
                    'id' => 'protection_' . $order->order_number,
                    'price' => (int) $order->product_protection_amount,
                    'quantity' => 1,
                    'name' => 'Product Protection for ' . $order->order_number,
                ];
            }

            $grossAmount += (int) $order->total_amount;
        }

        // Build combined customer details from first order
        $firstOrder = $orders->first();
        if (!$firstOrder->relationLoaded('user')) {
            $firstOrder->load('user');
        }

        $phone = $this->getPhoneWithFallback($firstOrder);
        $phone = $this->formatPhoneNumber($phone);

        // Generate unique group number
        $groupNumber = 'PG-' . (string)(method_exists(\Illuminate\Support\Str::class, 'uuid') ? \Illuminate\Support\Str::uuid() : uniqid());

        if ($paymentMethod === 'xendit') {
            return $this->createXenditGroupPayment($request, $orders, $firstOrder, $grossAmount, $groupNumber, $phone);
        }

        return $this->createMidtransGroupPayment($orders, $firstOrder, $itemDetails, $grossAmount, $groupNumber, $phone);
    }

    private function createMidtransGroupPayment($orders, $firstOrder, array $itemDetails, int $grossAmount, string $groupNumber, string $phone): JsonResponse
    {
        $shippingAddressFull = $this->formatAddress(
            $firstOrder->shipping_address,
            $firstOrder->shipping_city,
            $firstOrder->shipping_province,
            $firstOrder->shipping_postal_code
        );

        $billingAddressFull = $this->formatAddress(
            $firstOrder->billing_address ?? $firstOrder->shipping_address,
            $firstOrder->billing_city ?? $firstOrder->shipping_city,
            $firstOrder->billing_province ?? $firstOrder->shipping_province,
            $firstOrder->billing_postal_code ?? $firstOrder->shipping_postal_code
        );

        $customerDetails = [
            'first_name' => $firstOrder->shipping_first_name,
            'last_name' => $firstOrder->shipping_last_name ?? '',
            'email' => $firstOrder->contact_email,
            'phone' => $phone,
            'billing_address' => [
                'first_name' => $firstOrder->billing_first_name ?? $firstOrder->shipping_first_name,
                'last_name' => $firstOrder->billing_last_name ?? $firstOrder->shipping_last_name ?? '',
                'email' => $firstOrder->contact_email,
                'phone' => $phone,
                'address' => $billingAddressFull ?: ($firstOrder->billing_address ?? $firstOrder->shipping_address),
                'city' => $firstOrder->billing_city ?? $firstOrder->shipping_city,
                'postal_code' => $firstOrder->billing_postal_code ?? $firstOrder->shipping_postal_code,
                'country_code' => 'IDN',
            ],
            'shipping_address' => [
                'first_name' => $firstOrder->shipping_first_name,
                'last_name' => $firstOrder->shipping_last_name ?? '',
                'email' => $firstOrder->contact_email,
                'phone' => $phone,
                'address' => $shippingAddressFull ?: $firstOrder->shipping_address,
                'city' => $firstOrder->shipping_city,
                'postal_code' => $firstOrder->shipping_postal_code,
                'country_code' => 'IDN',
            ],
        ];

        $params = [
            'transaction_details' => [
                'order_id' => $groupNumber,
                'gross_amount' => max(0, (int) $grossAmount),
            ],
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = MidtransService::createSnapToken($params);

            $paymentGroup = \App\Models\PaymentGroup::create([
                'uuid' => (string) (\Illuminate\Support\Str::uuid() ?? ''),
                'group_number' => $groupNumber,
                'gross_amount' => (int) $grossAmount,
                'payment_snap_token' => $snapToken,
                'status' => 'PENDING',
                'fk_user_id' => $firstOrder->fk_user_id ?? null,
            ]);

            foreach ($orders as $order) {
                $paymentGroup->orders()->attach($order->id);
            }

            return response()->json(['snap_token' => $snapToken, 'payment_group_id' => $paymentGroup->id]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat token pembayaran group: ' . $e->getMessage()], 500);
        }
    }

    private function createXenditGroupPayment(Request $request, $orders, $firstOrder, int $grossAmount, string $groupNumber, string $phone): JsonResponse
    {
        $successRedirectUrl = $request->input('success_redirect_url', config('app.frontend_url') . '/account/orders?tab=paid');
        $failureRedirectUrl = $request->input('failure_redirect_url', config('app.frontend_url') . '/account/orders?tab=unpaid');
        $callbackUrl = $this->getXenditCallbackUrl();
        $successRedirectUrl = $this->appendQueryParameters($successRedirectUrl, [
            'xendit_return' => 'success',
            'external_id' => $groupNumber,
        ]);
        $failureRedirectUrl = $this->appendQueryParameters($failureRedirectUrl, [
            'xendit_return' => 'failed',
            'external_id' => $groupNumber,
        ]);

        $params = [
            'external_id' => $groupNumber,
            'amount' => max(0, (int) $grossAmount),
            'description' => 'Payment for orders group ' . $groupNumber,
            'customer' => array_filter([
                'given_names' => $firstOrder->shipping_first_name,
                'surname' => $firstOrder->shipping_last_name ?? '',
                'email' => $firstOrder->contact_email,
                'mobile_number' => $phone,
            ]),
            'customer_notification_preference' => [
                'invoice_created' => ['email', 'whatsapp'],
                'invoice_reminder' => ['email', 'whatsapp'],
                'invoice_paid' => ['email', 'whatsapp'],
            ],
            'success_redirect_url' => $successRedirectUrl,
            'failure_redirect_url' => $failureRedirectUrl,
            'callback_url' => $callbackUrl,
            'currency' => 'IDR',
        ];

        try {
            $invoice = XenditService::createInvoice($params);

            $paymentGroup = \App\Models\PaymentGroup::create([
                'uuid' => (string) (\Illuminate\Support\Str::uuid() ?? ''),
                'group_number' => $groupNumber,
                'gross_amount' => (int) $grossAmount,
                'payment_snap_token' => $invoice['invoice_url'] ?? null,
                'status' => 'PENDING',
                'fk_user_id' => $firstOrder->fk_user_id ?? null,
            ]);

            foreach ($orders as $order) {
                $paymentGroup->orders()->attach($order->id);

                $order->update([
                    'payment_method' => 'xendit',
                    'payment_reference_code' => $invoice['id'] ?? null,
                ]);
            }

            return response()->json([
                'invoice_url' => $invoice['invoice_url'] ?? null,
                'external_id' => $groupNumber,
                'payment_group_id' => $paymentGroup->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal membuat invoice Xendit group: ' . $e->getMessage()], 500);
        }
    }

    public function createMidtransSnap(Request $request, Order $order): JsonResponse
    {
        $requestedPaymentMethod = $request->input('payment_method');
        $orderUsesXendit = $order->payment_method === 'xendit' ||
            $this->isXenditInvoiceUrl($order->payment_snap_token);

        if ($order->payment_status !== 'PENDING') {
            return response()->json([
                'message' => 'Order is not payable'
            ], 422);
        }

        if (
            $requestedPaymentMethod === 'xendit' ||
            ($orderUsesXendit && $requestedPaymentMethod !== 'midtrans')
        ) {
            return response()->json([
                'message' => 'Order ini menggunakan Xendit. Silakan lanjutkan melalui invoice Xendit.'
            ], 422);
        }

        if (
            $requestedPaymentMethod === 'midtrans' &&
            $orderUsesXendit
        ) {
            $order->update([
                'payment_method' => 'midtrans',
                'payment_reference_code' => null,
                'payment_snap_token' => null,
            ]);
            $order->refresh();
        }

        // Check if order is expired and auto-cancel if needed
        if ($order->payment_snap_token) {
            try {
                $order->refresh();

                if ($this->orderPaymentSyncService->syncMidtransOrder($order)) {
                    $order->refresh();

                    if ($order->payment_status === 'PAID') {
                        return response()->json([
                            'message' => 'Pembayaran sudah berhasil.',
                            'payment_status' => $order->payment_status,
                            'status' => $order->status,
                        ], 422);
                    }
                }

                if ($order->status === 'CANCELLED' && $order->payment_status === 'FAILED') {
                    return response()->json([
                        'message' => 'Pembayaran sudah kedaluwarsa. Pesanan telah dibatalkan.'
                    ], 422);
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

                    // Restore voucher if voucher was used
                    if ($order->fk_voucher_id) {
                        $voucher = Voucher::find($order->fk_voucher_id);
                        if ($voucher && $voucher->voucher_used > 0) {
                            $voucher->decrement('voucher_used');
                        }
                    }

                    return response()->json([
                        'message' => 'Pembayaran sudah kedaluwarsa. Pesanan telah dibatalkan.'
                    ], 422);
                }
            } catch (\Exception $e) {
            }

            // Reuse token if exists and not expired
            return response()->json([
                'snap_token' => $order->payment_snap_token
            ]);
        }

        // Load user relationship if not already loaded
        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }

        // Get phone with fallback chain: Order.contact_phone > User.phone > empty string
        $phone = $this->getPhoneWithFallback($order);

        // Format phone number
        $phone = $this->formatPhoneNumber($phone);

        // Build full address strings
        $shippingAddressFull = $this->formatAddress(
            $order->shipping_address,
            $order->shipping_city,
            $order->shipping_province,
            $order->shipping_postal_code
        );

        $billingAddressFull = $this->formatAddress(
            $order->billing_address ?? $order->shipping_address,
            $order->billing_city ?? $order->shipping_city,
            $order->billing_province ?? $order->shipping_province,
            $order->billing_postal_code ?? $order->shipping_postal_code
        );

        // Load order items if not already loaded
        if (!$order->relationLoaded('orderItems')) {
            $order->load('orderItems');
        }

        // Build customer details with addresses
        $customerDetails = [
            'first_name' => $order->shipping_first_name,
            'last_name' => $order->shipping_last_name ?? '',
            'email' => $order->contact_email,
            'phone' => $phone,

            // Billing Address
            'billing_address' => [
                'first_name' => $order->billing_first_name ?? $order->shipping_first_name,
                'last_name' => $order->billing_last_name ?? $order->shipping_last_name ?? '',
                'email' => $order->contact_email,
                'phone' => $phone,
                'address' => $billingAddressFull ?: ($order->billing_address ?? $order->shipping_address),
                'city' => $order->billing_city ?? $order->shipping_city,
                'postal_code' => $order->billing_postal_code ?? $order->shipping_postal_code,
                'country_code' => 'IDN',
            ],

            // Shipping Address
            'shipping_address' => [
                'first_name' => $order->shipping_first_name,
                'last_name' => $order->shipping_last_name ?? '',
                'email' => $order->contact_email,
                'phone' => $phone,
                'address' => $shippingAddressFull ?: $order->shipping_address,
                'city' => $order->shipping_city,
                'postal_code' => $order->shipping_postal_code,
                'country_code' => 'IDN',
            ],
        ];

        // Build item details
        $itemDetails = $order->orderItems->map(fn($item) => [
            'id' => !empty($item->sku) ? (string) $item->sku : (string) ($item->fk_product_id ?? $item->id),
            'price' => (int) $item->purchase_price,
            'quantity' => (int) $item->qty,
            'name' => $item->product_name,
        ])->toArray();

        // Add shipping cost as item
        if ($order->shipping_cost > 0) {
            $itemDetails[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Shipping',
            ];
        }

        // Add voucher discount as negative item
        if (($order->discount_amount ?? 0) > 0) {
            $itemDetails[] = [
                'id' => 'voucher',
                'price' => -1 * (int) $order->discount_amount,
                'quantity' => 1,
                'name' => 'Voucher Discount',
            ];
        }

        if (($order->product_protection_amount ?? 0) > 0) {
            $itemDetails[] = [
                'id' => 'product_protection',
                'price' => (int) $order->product_protection_amount,
                'quantity' => 1,
                'name' => 'Product Damage Protection',
            ];
        }

        $grossAmount = (int) $order->total_amount;

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => max(0, $grossAmount),
            ],
            'customer_details' => $customerDetails,
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = MidtransService::createSnapToken($params);

            $order->update([
                'payment_method' => 'midtrans',
                'payment_snap_token' => $snapToken,
            ]);

            return response()->json([
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createXenditInvoice(Request $request, Order $order): JsonResponse
    {
        if ($order->payment_status !== 'PENDING') {
            return response()->json([
                'message' => 'Order is not payable'
            ], 422);
        }

        if (
            $order->payment_method === 'midtrans' &&
            $order->payment_snap_token &&
            !$this->isXenditInvoiceUrl($order->payment_snap_token)
        ) {
            $order->update([
                'payment_method' => 'xendit',
                'payment_reference_code' => null,
                'payment_snap_token' => null,
            ]);
            $order->refresh();
        }

        if (
            ($order->payment_method === 'xendit' || $this->isXenditInvoiceUrl($order->payment_snap_token)) &&
            $order->payment_reference_code &&
            $order->payment_snap_token
        ) {
            if ($order->payment_method !== 'xendit') {
                $order->update(['payment_method' => 'xendit']);
            }

            return response()->json([
                'invoice_url' => $order->payment_snap_token,
                'external_id' => $order->order_number,
            ]);
        }

        if (!$order->relationLoaded('user')) {
            $order->load('user');
        }

        $phone = $this->getPhoneWithFallback($order);
        $phone = $this->formatPhoneNumber($phone);

        if (!$order->relationLoaded('orderItems')) {
            $order->load('orderItems');
        }

        $successRedirectUrl = $request->input('success_redirect_url', config('app.frontend_url') . '/account/orders?tab=paid');
        $failureRedirectUrl = $request->input('failure_redirect_url', config('app.frontend_url') . '/account/orders?tab=unpaid');
        $callbackUrl = $this->getXenditCallbackUrl();
        $successRedirectUrl = $this->appendQueryParameters($successRedirectUrl, [
            'xendit_return' => 'success',
            'external_id' => $order->order_number,
            'order_id' => $order->id,
        ]);
        $failureRedirectUrl = $this->appendQueryParameters($failureRedirectUrl, [
            'xendit_return' => 'failed',
            'external_id' => $order->order_number,
            'order_id' => $order->id,
        ]);

        $params = [
            'external_id' => $order->order_number,
            'amount' => max(0, (int) $order->total_amount),
            'description' => 'Payment for order ' . $order->order_number,
            'customer' => array_filter([
                'given_names' => $order->shipping_first_name,
                'surname' => $order->shipping_last_name ?? '',
                'email' => $order->contact_email,
                'mobile_number' => $phone,
            ]),
            'customer_notification_preference' => [
                'invoice_created' => ['email', 'whatsapp'],
                'invoice_reminder' => ['email', 'whatsapp'],
                'invoice_paid' => ['email', 'whatsapp'],
            ],
            'success_redirect_url' => $successRedirectUrl,
            'failure_redirect_url' => $failureRedirectUrl,
            'callback_url' => $callbackUrl,
            'currency' => 'IDR',
        ];

        try {
            $invoice = XenditService::createInvoice($params);

            $order->update([
                'payment_method' => 'xendit',
                'payment_reference_code' => $invoice['id'] ?? null,
                'payment_snap_token' => $invoice['invoice_url'] ?? null,
            ]);

            return response()->json([
                'invoice_url' => $invoice['invoice_url'] ?? null,
                'external_id' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat invoice Xendit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get phone number with fallback chain
     * Priority: Order.contact_phone > UserShippingAddress.phone > User.phone > empty string
     * 
     * @param Order $order
     * @return string
     */
    private function getPhoneWithFallback(Order $order): string
    {
        // 1. Try order contact_phone first
        $phone = $order->contact_phone;

        if ($this->isValidPhoneNumber($phone)) {
            return $phone;
        }

        // 2. Fallback to shipping address phone
        $shippingAddressPhone = null;
        if ($order->user && $order->fk_user_id) {
            $shippingAddress = UserShippingAddress::where('user_id', $order->fk_user_id)
                ->where('first_name', $order->shipping_first_name)
                ->where('city', $order->shipping_city)
                ->where('postal_code', $order->shipping_postal_code)
                ->first();

            if ($shippingAddress && $shippingAddress->phone) {
                $phone = $shippingAddress->phone;
                $shippingAddressPhone = $phone;

                if ($this->isValidPhoneNumber($phone)) {
                    return $phone;
                }
            }
        }

        // 3. Fallback to user phone
        if ($order->user && $order->user->phone) {
            $phone = $order->user->phone;

            if ($this->isValidPhoneNumber($phone)) {
                return $phone;
            }
        }

        return $order->contact_phone ?? '';
    }

    /**
     * Check if phone number is valid
     * 
     * @param string|null $phone
     * @return bool
     */
    private function isValidPhoneNumber(?string $phone): bool
    {
        if (empty($phone)) {
            return false;
        }

        // Convert to string and trim
        $phone = trim((string) $phone);

        if (empty($phone)) {
            return false;
        }

        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        return preg_match('/^(\+62|62|0)?[0-9]{2,15}$/', $cleaned) === 1;
    }

    /**
     * Format phone number to Midtrans format (+62xxxxxxxxxx)
     * 
     * @param string|null $phone
     * @return string
     */
    private function formatPhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $phone = trim((string) $phone);

        if (empty($phone)) {
            return '';
        }

        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        if (empty($cleaned)) {
            return '';
        }

        if (str_starts_with($cleaned, '0')) {
            return '+62' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '62') && !str_starts_with($cleaned, '+62')) {
            return '+' . $cleaned;
        }

        if (str_starts_with($cleaned, '+62')) {
            return $cleaned;
        }

        if (preg_match('/^\d{8,}$/', $cleaned)) {
            return '+62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Format address string from components
     */
    private function formatAddress(?string $address, ?string $city, ?string $province, ?string $postalCode): string
    {
        $parts = array_filter([
            $address,
            $city,
            $province,
            $postalCode,
        ]);

        return implode(', ', $parts) ?: '';
    }

    private function getXenditCallbackUrl(): string
    {
        $configuredUrl = config('services.xendit.callback_url');

        return rtrim($configuredUrl ?: url('/api/xendit/webhook'), '/');
    }

    private function appendQueryParameters(string $url, array $params): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . http_build_query($params);
    }

    private function isXenditInvoiceUrl(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false && str_contains($value, 'xendit.co');
    }
}
