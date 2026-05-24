<?php

namespace App\Services\RajaOngkir;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProvinceService
{
    public function getProvinces(?int $provinceId = null): array
    {
        try {
            $methodUrl = '/destination/province';
            $params = [];
            
            if ($provinceId) {
                $params['id'] = $provinceId;
            }

            $headers = [
                'Key' => RajaOngkirHelper::getToken(),
            ];

            $response = Http::withHeaders($headers)
                ->get(RajaOngkirHelper::getBaseUrl() . $methodUrl, $params);

            $responseBody = json_decode($response->getBody(), true);
            $statusCode = $response->getStatusCode();


            if ($statusCode === 200) {
                if (isset($responseBody['meta']) && isset($responseBody['data'])) {
                    $provinces = $responseBody['data'];
                    
                    if ($provinceId) {
                        $provinces = array_filter($provinces, function($province) use ($provinceId) {
                            return isset($province['id']) && $province['id'] == $provinceId;
                        });
                        $provinces = array_values($provinces);
                    }
                    
                    return [
                        'success' => $responseBody['meta']['status'] === 'success',
                        'data' => $provinces,
                        'message' => $responseBody['meta']['message'] ?? 'Successfully retrieved provinces',
                    ];
                }
                
                if (isset($responseBody['success']) && isset($responseBody['data'])) {
                    return [
                        'success' => $responseBody['success'],
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved provinces',
                    ];
                }
                
                if (isset($responseBody['data'])) {
                    return [
                        'success' => true,
                        'data' => $responseBody['data'],
                        'message' => $responseBody['message'] ?? 'Successfully retrieved provinces',
                    ];
                }
            }

            return [
                'success' => false,
                'data' => [],
                'message' => $responseBody['meta']['message'] ?? ($responseBody['message'] ?? 'Failed to retrieve provinces'),
            ];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Province API V2 Error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'Error connecting to RajaOngkir API: ' . $e->getMessage(),
            ];
        }
    }
}
