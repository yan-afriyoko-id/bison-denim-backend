<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brandProductPairs = [
            ['fk_brand_id' => 1, 'fk_product_id' => 1],
            ['fk_brand_id' => 2, 'fk_product_id' => 2],
            ['fk_brand_id' => 3, 'fk_product_id' => 3],
        ];

        DB::table('brand_products')->insert($brandProductPairs);
    }
}
