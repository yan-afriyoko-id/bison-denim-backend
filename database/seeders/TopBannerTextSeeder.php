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
                'value' => 'Free Shipping JABODETABEK pembelanjaan Rp75.000+',
                'description' => 'Text banner di atas website (top banner)',
                'type' => 'text',
            ]
        );
    }
}
