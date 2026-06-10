<?php

namespace Database\Seeders;

use App\Models\TaxoList;
use Illuminate\Database\Seeder;

class TaxoListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Brands (type 4)
        TaxoList::create([
            'taxonomy_name' => 'Denim Republic',
            'taxonomy_slug' => 'denim-republic',
            'taxonomy_type' => 4,
            'taxonomy_sort' => 1,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Blue Stone',
            'taxonomy_slug' => 'blue-stone',
            'taxonomy_type' => 4,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Indigo Wear',
            'taxonomy_slug' => 'indigo-wear',
            'taxonomy_type' => 4,
            'taxonomy_sort' => 3,
            'taxonomy_status' => 'ACTIVE',
        ]);

        // Attributes - Colors (type 5)
        TaxoList::create([
            'taxonomy_name' => 'Black',
            'taxonomy_slug' => 'black',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 1,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'White',
            'taxonomy_slug' => 'white',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Indigo',
            'taxonomy_slug' => 'indigo',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 3,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Navy',
            'taxonomy_slug' => 'navy',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 4,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Grey',
            'taxonomy_slug' => 'grey',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 5,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Brown',
            'taxonomy_slug' => 'brown',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 6,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Olive',
            'taxonomy_slug' => 'olive',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 7,
            'taxonomy_status' => 'ACTIVE',
        ]);

        // Attributes - Sizes (type 6)
        TaxoList::create([
            'taxonomy_name' => 'XS',
            'taxonomy_slug' => 'xs',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 1,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'S',
            'taxonomy_slug' => 's',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'M',
            'taxonomy_slug' => 'm',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 3,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'L',
            'taxonomy_slug' => 'l',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 4,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'XL',
            'taxonomy_slug' => 'xl',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 5,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'XXL',
            'taxonomy_slug' => 'xxl',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 6,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Slim Fit',
            'taxonomy_slug' => 'slim-fit',
            'taxonomy_type' => 7,
            'taxonomy_sort' => 1,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Regular Fit',
            'taxonomy_slug' => 'regular-fit',
            'taxonomy_type' => 7,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Relaxed Fit',
            'taxonomy_slug' => 'relaxed-fit',
            'taxonomy_type' => 7,
            'taxonomy_sort' => 3,
            'taxonomy_status' => 'ACTIVE',
        ]);
    }
}
