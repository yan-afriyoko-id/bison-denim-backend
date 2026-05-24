<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MidtransConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('midtrans_configs')->insert([
            'environment' => 'sandbox',
            'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-xxxxxxxxxxxxxxxxxxxx'),
            'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-xxxxxxxxxxxxxxxxxxxx'),
            'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'G123456789'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

