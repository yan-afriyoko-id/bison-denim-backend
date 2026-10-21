<?php
namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

/**
 * DOKU Checkout (non-SNAP) integration.
 *
 * @see https://developers.doku.com/accept-payments/doku-checkout/integration-guide/backend-integration
 */
class DokuService
{
    public const CHECKOUT_PATH = '/checkout/v1/payment';
    public const STATUS_PATH = '/orders/v1/status/';
    public const DEFAULT_PAYMENT_DUE_MINUTES = 1440;

    private static function credentials(): array
    {
        $config = config('settings');
        $clientId = $config['doku_client_id'] ?? env('DOKU_CLIENT_ID');
        $secretKey = $config['doku_secret_key'] ?? env('DOKU_SECRET_KEY');

        if (empty($clientId) || empty($secretKey)) {
            throw new Exception(
                'DOKU Client ID / Secret Key tidak di-set dengan benar. ' .
                'Silakan set doku_client_id dan doku_secret_key di Settings CMS atau environment variable DOKU_CLIENT_ID / DOKU_SECRET_KEY.'
            );
        }

        return [$clientId, $secretKey];
    }

    private static function baseUrl(): string
    {
        $config = config('settings');
        $isProduction = $config['doku_is_production'] ?? env('DOKU_IS_PRODUCTION', false);
        $isProduction = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN);

        return $isProduction
            ? 'https://api.doku.com'
            : 'https://api-sandbox.doku.com';
    }

    public static function paymentDueMinutes(): int
    {
        $minutes = (int) (config('settings')['doku_payment_due_minutes'] ?? 0);

        return $minutes > 0 ? $minutes : self::DEFAULT_PAYMENT_DUE_MINUTES;
    }

    /**
     * Build the "HMACSHA256=..." signature. Pass $body = null for GET requests (no Digest component).
     */
    public static function generateSignature(
        string $clientId,
        string $secretKey,
        string $requestId,
        string $timestamp,
        string $requestTarget,
        ?string $body = null
    ): string {
        $components = [
            'Client-Id:' . $clientId,
            'Request-Id:' . $requestId,
            'Request-Timestamp:' . $timestamp,
            'Request-Target:' . $requestTarget,
        ];

        if ($body !== null) {
            $components[] = 'Digest:' . base64_encode(hash('sha256', $body, true));
        }

        $signature = base64_encode(hash_hmac('sha256', implode("\n", $components), $secretKey, true));

        return 'HMACSHA256=' . $signature;
    }

    private static function headers(string $requestTarget, ?string $body = null): array
    {
        [$clientId, $secretKey] = self::credentials();

        $requestId = (string) Str::uuid();
        $timestamp = now('UTC')->format('Y-m-d\TH:i:s\Z');

        return [
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => self::generateSignature($clientId, $secretKey, $requestId, $timestamp, $requestTarget, $body),
        ];
    }

    /**
     * Create a DOKU Checkout payment page.
     *
     * @return array The `response` object (payment.url, payment.token_id, ...)
     */
    public static function createCheckout(array $params): array
    {
        // The Digest must be computed over the exact bytes sent
        $body = json_encode($params, JSON_UNESCAPED_SLASHES);
        $headers = self::headers(self::CHECKOUT_PATH, $body);

        $response = Http::withHeaders($headers)
            ->withBody($body, 'application/json')
            ->post(self::baseUrl() . self::CHECKOUT_PATH);

        $data = json_decode($response->body(), true, 512, JSON_BIGINT_AS_STRING);

        if ($response->failed() || empty($data['response']['payment']['url'])) {
            Log::error('DOKU CREATE CHECKOUT ERROR', [
                'status' => $response->status(),
                'response' => $data ?? $response->body(),
                'invoice_number' => $params['order']['invoice_number'] ?? null,
            ]);

            $message = isset($data['error_messages'])
                ? implode(', ', (array) $data['error_messages'])
                : ($data['message'][0] ?? $response->body());

            throw new Exception('Gagal membuat pembayaran DOKU: ' . $message);
        }

        return $data['response'];
    }

    /**
     * Check order status by invoice number.
     *
     * @return array|null Status payload (order, transaction, service, ...) or null if not found/error
     */
    public static function checkStatus(string $invoiceNumber): ?array
    {
        try {
            $requestTarget = self::STATUS_PATH . $invoiceNumber;
            $headers = self::headers($requestTarget);

            $response = Http::withHeaders($headers)->get(self::baseUrl() . $requestTarget);

            // 404 means no payment channel has been chosen yet, i.e. still pending
            if ($response->failed()) {
                if ($response->status() !== 404) {
                    Log::error('DOKU CHECK STATUS ERROR', [
                        'invoice_number' => $invoiceNumber,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]);
                }

                return null;
            }

            return json_decode($response->body(), true, 512, JSON_BIGINT_AS_STRING);
        } catch (\Exception $e) {
            Log::error('DOKU CHECK STATUS ERROR', [
                'invoice_number' => $invoiceNumber,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verify the signature of an incoming HTTP Notification.
     */
    public static function verifyNotification(Request $request, string $requestTarget): bool
    {
        try {
            [, $secretKey] = self::credentials();
        } catch (\Exception $e) {
            return false;
        }

        $clientId = (string) $request->header('Client-Id');
        $requestId = (string) $request->header('Request-Id');
        $timestamp = (string) $request->header('Request-Timestamp');
        $signature = (string) $request->header('Signature');

        if ($clientId === '' || $requestId === '' || $timestamp === '' || $signature === '') {
            return false;
        }

        $expected = self::generateSignature(
            $clientId,
            $secretKey,
            $requestId,
            $timestamp,
            $requestTarget,
            $request->getContent()
        );

        return hash_equals($expected, $signature);
    }

    public static function isCheckoutUrl(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false && str_contains($value, 'doku.com');
    }
}
