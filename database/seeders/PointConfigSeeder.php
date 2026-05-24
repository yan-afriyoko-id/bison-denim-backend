<?php

namespace Database\Seeders;

use App\Interfaces\ConfigRepositoryInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configRepository = app(ConfigRepositoryInterface::class);

        // Minimum usable points (250.000 poin)
        $configRepository->set('point_minimum_usable_points', 250000, [
            'type' => 'integer',
            'description' => 'Minimum poin yang harus dikumpulkan sebelum bisa digunakan',
        ]);

        // Points per million (25.000 poin per 1 juta)
        $configRepository->set('point_points_per_million', 25000, [
            'type' => 'integer',
            'description' => 'Jumlah poin yang diberikan per kelipatan pembelian',
        ]);

        // Million threshold (1.000.000 Rupiah)
        $configRepository->set('point_million_threshold', 1000000, [
            'type' => 'integer',
            'description' => 'Kelipatan pembelian (dalam Rupiah) untuk mendapatkan poin',
        ]);
    }
}
