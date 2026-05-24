<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('store_settings')->insert([
            'logo_website' => 'https://via.placeholder.com/200x50?text=KARSINDO',
            'favicon' => 'https://via.placeholder.com/32x32?text=K',
            'store_address' => 'Jakarta, Indonesia',
            'store_name' => 'Karsindo',
            'store_phone' => '+62-XXX-XXXX-XXXX',
            'store_email' => 'support@karsindo.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

