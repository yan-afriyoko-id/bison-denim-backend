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
                'name' => 'Karsindo',
                'slug' => 'karsindo',
                'logo' => $baseUrl . '/uploads/brands/karsindo.png',
                'status' => 'ACTIVE',
                'order' => 1,
                'description' => 'Karsindo brand',
            ],
            [
                'name' => 'Karstech',
                'slug' => 'karstech',
                'logo' => $baseUrl . '/uploads/brands/karstech.png',
                'status' => 'ACTIVE',
                'order' => 2,
                'description' => 'Karstech brand',
            ],
            [
                'name' => 'Karvium',
                'slug' => 'karvium',
                'logo' => $baseUrl . '/uploads/brands/karvium.png',
                'status' => 'ACTIVE',
                'order' => 3,
                'description' => 'Karvium brand',
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
