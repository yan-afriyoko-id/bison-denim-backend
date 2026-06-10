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
                'name' => 'Celana Jeans',
                'slug' => 'celana-jeans',
                'sort' => 1,
                'subcategories' => [
                    ['name' => 'Slim Fit', 'slug' => 'slim-fit', 'sort' => 1],
                    ['name' => 'Regular Fit', 'slug' => 'regular-fit', 'sort' => 2],
                    ['name' => 'Wide Leg', 'slug' => 'wide-leg', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Kemeja',
                'slug' => 'kemeja',
                'sort' => 2,
                'subcategories' => [
                    ['name' => 'Kemeja Formal', 'slug' => 'kemeja-formal', 'sort' => 1],
                    ['name' => 'Kemeja Kasual', 'slug' => 'kemeja-kasual', 'sort' => 2],
                    ['name' => 'Kemeja Flannel', 'slug' => 'kemeja-flannel', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Jaket & Outerwear',
                'slug' => 'jaket-outerwear',
                'sort' => 3,
                'subcategories' => [
                    ['name' => 'Jaket Denim', 'slug' => 'jaket-denim', 'sort' => 1],
                    ['name' => 'Bomber', 'slug' => 'bomber', 'sort' => 2],
                    ['name' => 'Hoodie', 'slug' => 'hoodie', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Kaos & Atasan',
                'slug' => 'kaos-atasan',
                'sort' => 4,
                'subcategories' => [
                    ['name' => 'T-Shirt', 'slug' => 't-shirt', 'sort' => 1],
                    ['name' => 'Polo Shirt', 'slug' => 'polo-shirt', 'sort' => 2],
                    ['name' => 'Tank Top', 'slug' => 'tank-top', 'sort' => 3],
                ],
            ],
            [
                'name' => 'Aksesori',
                'slug' => 'aksesori',
                'sort' => 5,
                'subcategories' => [
                    ['name' => 'Ikat Pinggang', 'slug' => 'ikat-pinggang', 'sort' => 1],
                    ['name' => 'Topi', 'slug' => 'topi', 'sort' => 2],
                    ['name' => 'Tas', 'slug' => 'tas', 'sort' => 3],
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
