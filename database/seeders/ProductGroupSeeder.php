<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductSubGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penawaranSpesial = ProductGroup::create([
            'title' => 'Penawaran Spesial',
            'key' => 'penawaran-spesial',
        ]);

        $officialStore = ProductGroup::create([
            'title' => 'Official Store',
            'key' => 'official-store',
        ]);

        $rekomendasiUntukmu = ProductGroup::create([
            'title' => 'Rekomendasi Untukmu',
            'key' => 'rekomendasi-untukmu',
        ]);

        $subGroupOfficialStore = ['Official Store Pria', 'Official Store Wanita', 'Official Store Unisex'];
        foreach ($subGroupOfficialStore as $subGroup) {
            ProductSubGroup::create([
                'product_group_id' => $officialStore->id,
                'title' => $subGroup,
            ]);
        }

        $subGroupPenawaranSpesial = ['Flash Sale Denim', 'Promo Kemeja', 'Diskon Jaket'];
        foreach ($subGroupPenawaranSpesial as $subGroup) {
            ProductSubGroup::create([
                'product_group_id' => $penawaranSpesial->id,
                'title' => $subGroup,
            ]);
        }

        $subGroupRekomendasiUntukmu = ['New Arrivals', 'Terlaris', 'Koleksi Musim Ini', 'Beli 1 Gratis 1'];
        foreach ($subGroupRekomendasiUntukmu as $subGroup) {
            ProductSubGroup::create([
                'product_group_id' => $rekomendasiUntukmu->id,
                'title' => $subGroup,
            ]);
        }
    }
}
