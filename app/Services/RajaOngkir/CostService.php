<?php

namespace App\Services\RajaOngkir;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CostService
{
    public function getCost(int $destination, int $weight, string $courier = 'jne', ?int $origin = null, ?string $price = null): array
    {
        try {
            $methodUrl = '/calculate/district/domestic-cost';
            $origin = $origin ?? config('rajaongkir.origin_city_code');
            
            $postInput = [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ];
            
            if ($price !== null) {
                $postInput['price'] = $price;
            }

            $headers = [
                'Key' => RajaOngkirHelper::getToken(),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];

            $response = Http::withHeaders($headers)
                ->asForm()
                ->post(RajaOngkirHelper::getBaseUrl() . $methodUrl, $postInput);

            $responseBody = json_decode($response->getBody(), true);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                if (isset($responseBody['meta']) && isset($responseBody['data'])) {
                    return [
                        'success' => $responseBody['meta']['status'] === 'success',
                        'data' => $responseBody['data'],
                        'message' => $responseBody['meta']['message'] ?? 'Successfully retrieved shipping cost',
                    ];
                }
                
                if (isset($responseBody['success']) && isset($responseBody['data'])) {
                    return [
                        'success' => $responseBody['success'],
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved shipping cost',
                    ];
                }
                
                if (isset($responseBody['data'])) {
                    return [
                        'success' => true,
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved shipping cost',
                    ];
                }
            }

            return [
                'success' => false,
                'data' => [],
                'message' => $responseBody['meta']['message'] ?? ($responseBody['message'] ?? ($responseBody['error'] ?? 'Failed to retrieve shipping cost')),
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Cost API V2 Error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error connecting to RajaOngkir API: ' . $e->getMessage(),
            ];
        }
    }
}
