<?php
namespace App\Services\Payment;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public static function init()
    {
        $config = config('settings');
        $serverKey = $config['midtrans_server_key'] ?? env('MIDTRANS_SERVER_KEY');
        $isProduction = $config['midtrans_is_production'] ?? env('MIDTRANS_IS_PRODUCTION', false);
        $isProduction = filter_var($isProduction, FILTER_VALIDATE_BOOLEAN);

        if (empty($serverKey) || 
            $serverKey === 'your_midtrans_server_key' ||
            str_contains($serverKey, 'xxxxx')) {
            throw new Exception(
                'Midtrans server key tidak di-set dengan benar. ' .
                'Silakan set midtrans_server_key di Settings CMS atau environment variable MIDTRANS_SERVER_KEY.'
            );
        }

        Config::$serverKey = $serverKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public static function createSnapToken(array $params)
    {
        try {
            self::init();
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Midtrans server key')) {
                throw $e;
            }

            throw new Exception(
                'Gagal membuat Snap token: ' . $e->getMessage() .
                '. Pastikan Midtrans server key dan client key sudah benar di Settings CMS.',
                0,
                $e
            );
        }
    }

    /**
     * Check transaction status from Midtrans API
     * 
     * @param string $orderId Order number (e.g., ORD-20260204-0001)
     * @return array|null Transaction data or null if not found/error
     */
    public static function checkTransactionStatus(string $orderId): ?array
    {
        try {
            self::init();
            
            $status = Transaction::status($orderId);
            
            return [
                'transaction_status' => $status->transaction_status ?? null,
                'fraud_status' => $status->fraud_status ?? null,
                'payment_type' => $status->payment_type ?? null,
                'order_id' => $status->order_id ?? null,
                'transaction_id' => $status->transaction_id ?? null,
                'status_code' => $status->status_code ?? null,
                'gross_amount' => $status->gross_amount ?? null,
                'expiry_time' => $status->expiry_time ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('MIDTRANS CHECK STATUS ERROR', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }

    /**
     * Check if transaction is expired
     * 
     * @param string $orderId Order number
     * @return bool True if expired, false otherwise
     */
    public static function isTransactionExpired(string $orderId): bool
    {
        $status = self::checkTransactionStatus($orderId);
        
        if (!$status) {
            return false;
        }

        $transactionStatus = $status['transaction_status'] ?? null;
        
        // Check if status is expire
        if ($transactionStatus === 'expire') {
            return true;
        }

        // Check expiry_time if available
        if (isset($status['expiry_time'])) {
            try {
                $expiryTime = \Carbon\Carbon::parse($status['expiry_time']);
                if ($expiryTime->isPast()) {
                    return true;
                }
            } catch (\Exception $e) {
                // Ignore parsing errors
            }
        }

        return false;
    }
}
