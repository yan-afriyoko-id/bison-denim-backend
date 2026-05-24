<?php

namespace App\Services\RajaOngkir;

use Illuminate\Support\Facades\DB;

class RajaOngkirHelper
{
    /**
     * Get RajaOngkir API token from database or fallback to config
     * 
     * @return string
     */
    public static function getToken(): string
    {
        try {
            $tokenFromDb = DB::table('configs')
                ->where('key', 'rajaongkir_key')
                ->value('value');
            
            if (!empty($tokenFromDb) && !empty(trim($tokenFromDb))) {
                return trim($tokenFromDb);
            }
        } catch (\Throwable $e) {
            // Fallback to config if database query fails
        }
        
        return config('rajaongkir.token', '');
    }
    
    /**
     * Get RajaOngkir base URL from config
     * 
     * @return string
     */
    public static function getBaseUrl(): string
    {
        return config('rajaongkir.base_url', 'https://rajaongkir.komerce.id/api/v1');
    }
}
