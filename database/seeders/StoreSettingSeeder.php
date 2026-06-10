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
            'logo_website' => 'https://via.placeholder.com/200x50?text=BISON+DENIM',
            'favicon' => 'https://via.placeholder.com/32x32?text=K',
            'store_address' => 'Jakarta, Indonesia',
            'store_name' => 'Bison Denim',
            'store_phone' => '+62-XXX-XXXX-XXXX',
            'store_email' => 'support@bisondenim.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

