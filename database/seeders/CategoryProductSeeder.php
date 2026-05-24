<?php

namespace Database\Seeders;

use App\Models\TaxoList;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categories (type 2)
        TaxoList::firstOrCreate(
            ['taxonomy_slug' => 'lemari-pakaian', 'taxonomy_type' => 2],
            [
                'taxonomy_name' => 'Lemari Pakaian',
                'taxonomy_slug' => 'lemari-pakaian',
                'taxonomy_type' => 2,
                'taxonomy_sort' => 1,
                'taxonomy_status' => 'ACTIVE',
            ]
        );

        TaxoList::firstOrCreate(
            ['taxonomy_slug' => 'kursi', 'taxonomy_type' => 2],
            [
                'taxonomy_name' => 'Kursi',
                'taxonomy_slug' => 'kursi',
                'taxonomy_type' => 2,
                'taxonomy_sort' => 2,
                'taxonomy_status' => 'ACTIVE',
            ]
        );
    }
}
