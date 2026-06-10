<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class TopBannerTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::firstOrCreate(
            ['key' => 'topbanner'],
            [
                'key' => 'topbanner',
                'value' => 'Free Shipping JABODETABEK pembelanjaan Rp150.000+ | New Arrival Denim Collection',
                'description' => 'Text banner di atas website (top banner)',
                'type' => 'text',
            ]
        );
    }
}
