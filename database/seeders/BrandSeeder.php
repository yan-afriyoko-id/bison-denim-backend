<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');

        $brands = [
            [
                'name' => 'Denim Republic',
                'slug' => 'denim-republic',
                'logo' => "$baseUrl/uploads/brands/denim-republic.png",
                'status' => 'ACTIVE',
                'order' => 1,
                'description' => 'A premium denim label specializing in high-quality jeans, jackets, and workwear essentials.',
            ],
            [
                'name' => 'Blue Stone',
                'slug' => 'blue-stone',
                'logo' => "$baseUrl/uploads/brands/blue-stone.png",
                'status' => 'ACTIVE',
                'order' => 2,
                'description' => 'A casual wear label offering comfortable everyday clothing with a clean, modern aesthetic.',
            ],
            [
                'name' => 'Indigo Wear',
                'slug' => 'indigo-wear',
                'logo' => "$baseUrl/uploads/brands/indigo-wear.png",
                'status' => 'ACTIVE',
                'order' => 3,
                'description' => 'A streetwear label rooted in urban culture, blending bold graphics with comfortable street-ready fits.',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['name' => $brand['name']],
                $brand
            );
        }
    }
}
