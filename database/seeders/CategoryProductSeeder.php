<?php

namespace Database\Seeders;

use App\Models\TaxoList;
use App\Models\TaxoType;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryTypeId = TaxoType::where('taxo_type_name', 'Category')->value('id');
        $subcategoryTypeId = TaxoType::where('taxo_type_name', 'Subcategory')->value('id');

        if (!$categoryTypeId || !$subcategoryTypeId) {
            $this->command?->warn('Category/Subcategory taxo types not found. Skipping category seed.');

            return;
        }

        $categories = [
            [
                'name' => 'Lemari Pakaian',
                'slug' => 'lemari-pakaian',
                'sort' => 1,
                'subcategories' => [
                    ['name' => 'Lemari 2 Pintu', 'slug' => 'lemari-2-pintu', 'sort' => 1],
                    ['name' => 'Lemari 3 Pintu', 'slug' => 'lemari-3-pintu', 'sort' => 2],
                    ['name' => 'Lemari Minimalis', 'slug' => 'lemari-minimalis', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Kursi',
                'slug' => 'kursi',
                'sort' => 2,
                'subcategories' => [
                    ['name' => 'Kursi Makan', 'slug' => 'kursi-makan', 'sort' => 1],
                    ['name' => 'Kursi Bar', 'slug' => 'kursi-bar', 'sort' => 2],
                    ['name' => 'Bangku Lipat', 'slug' => 'bangku-lipat', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Meja',
                'slug' => 'meja',
                'sort' => 3,
                'subcategories' => [
                    ['name' => 'Meja Kerja', 'slug' => 'meja-kerja', 'sort' => 1],
                    ['name' => 'Meja Belajar', 'slug' => 'meja-belajar', 'sort' => 2],
                    ['name' => 'Meja Tamu', 'slug' => 'meja-tamu', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Sofa',
                'slug' => 'sofa',
                'sort' => 4,
                'subcategories' => [
                    ['name' => 'Sofa 1 Dudukan', 'slug' => 'sofa-1-dudukan', 'sort' => 1],
                    ['name' => 'Sofa 2 Dudukan', 'slug' => 'sofa-2-dudukan', 'sort' => 2],
                    ['name' => 'Sofa Bed', 'slug' => 'sofa-bed', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Rak',
                'slug' => 'rak',
                'sort' => 5,
                'subcategories' => [
                    ['name' => 'Rak Buku', 'slug' => 'rak-buku', 'sort' => 1],
                    ['name' => 'Rak Sepatu', 'slug' => 'rak-sepatu', 'sort' => 2],
                    ['name' => 'Rak Dinding', 'slug' => 'rak-dinding', 'sort' => 3],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = TaxoList::updateOrCreate(
                [
                    'taxonomy_slug' => $categoryData['slug'],
                    'taxonomy_type' => $categoryTypeId,
                ],
                [
                    'parent' => null,
                    'taxonomy_name' => $categoryData['name'],
                    'taxonomy_slug' => $categoryData['slug'],
                    'taxonomy_type' => $categoryTypeId,
                    'taxonomy_sort' => $categoryData['sort'],
                    'taxonomy_status' => 'ACTIVE',
                ]
            );

            foreach ($categoryData['subcategories'] as $subcategoryData) {
                TaxoList::updateOrCreate(
                    [
                        'taxonomy_slug' => $subcategoryData['slug'],
                        'taxonomy_type' => $subcategoryTypeId,
                    ],
                    [
                        'parent' => $category->id,
                        'taxonomy_name' => $subcategoryData['name'],
                        'taxonomy_slug' => $subcategoryData['slug'],
                        'taxonomy_type' => $subcategoryTypeId,
                        'taxonomy_sort' => $subcategoryData['sort'],
                        'taxonomy_status' => 'ACTIVE',
                    ]
                );
            }
        }
    }
}
