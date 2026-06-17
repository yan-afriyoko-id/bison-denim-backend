<?php
namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class XenditService
{
    public static function createInvoice(array $params): array
    {
        $config = config('settings');
        $secretKey = $config['xendit_secret_key'] ?? env('XENDIT_SECRET_KEY');
        $isProduction = $config['xendit_is_production'] ?? env('XENDIT_IS_PRODUCTION', false);
        $isProduction = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN);

        if (empty($secretKey) || str_contains($secretKey, 'xxxxx')) {
            throw new Exception(
                'Xendit secret key tidak di-set dengan benar. ' .
                'Silakan set xendit_secret_key di Settings CMS atau environment variable XENDIT_SECRET_KEY.'
            );
        }

        $baseUrl = $isProduction
            ? 'https://api.xendit.co'
            : 'https://api.xendit.co';

        $response = Http::withBasicAuth($secretKey, '')
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($baseUrl . '/v2/invoices', $params);

        if ($response->failed()) {
            $error = $response->json();
            Log::error('XENDIT CREATE INVOICE ERROR', [
                'response' => $error,
                'params' => $params,
            ]);
            throw new Exception(
                'Gagal membuat invoice Xendit: ' . ($error['message'] ?? $response->body())
            );
        }

        return $response->json();
    }

    public static function getInvoice(string $invoiceId): ?array
    {
        $config = config('settings');
        $secretKey = $config['xendit_secret_key'] ?? env('XENDIT_SECRET_KEY');

        if (empty($secretKey)) {
            return null;
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->get("https://api.xendit.co/v2/invoices/{$invoiceId}");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
