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
            'taxonomy_name' => 'ASUS',
            'taxonomy_slug' => 'asus',
            'taxonomy_type' => 4,
            'taxonomy_sort' => 1,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Dell',
            'taxonomy_slug' => 'dell',
            'taxonomy_type' => 4,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Apple',
            'taxonomy_slug' => 'apple',
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
            'taxonomy_name' => 'Silver',
            'taxonomy_slug' => 'silver',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Gold',
            'taxonomy_slug' => 'gold',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 3,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'White',
            'taxonomy_slug' => 'white',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 4,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Brown',
            'taxonomy_slug' => 'brown',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 5,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Red',
            'taxonomy_slug' => 'red',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 6,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Blue',
            'taxonomy_slug' => 'blue',
            'taxonomy_type' => 5,
            'taxonomy_sort' => 7,
            'taxonomy_status' => 'ACTIVE',
        ]);

        // Attributes - Sizes (type 6)
        TaxoList::create([
            'taxonomy_name' => 'LM D3',
            'taxonomy_slug' => 'lm-d3',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 1,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'LM D4',
            'taxonomy_slug' => 'lm-d4',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Small',
            'taxonomy_slug' => 'small',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 3,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Medium',
            'taxonomy_slug' => 'medium',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 4,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Large',
            'taxonomy_slug' => 'large',
            'taxonomy_type' => 6,
            'taxonomy_sort' => 5,
            'taxonomy_status' => 'ACTIVE',
        ]);

        // Attributes - Models (type 7)
        TaxoList::create([
            'taxonomy_name' => 'JF-2B310-2door-120cm',
            'taxonomy_slug' => 'jf-2b310-2door-120cm',
            'taxonomy_type' => 7,
            'taxonomy_sort' => 1,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Standard Model',
            'taxonomy_slug' => 'standard-model',
            'taxonomy_type' => 7,
            'taxonomy_sort' => 2,
            'taxonomy_status' => 'ACTIVE',
        ]);

        TaxoList::create([
            'taxonomy_name' => 'Premium Model',
            'taxonomy_slug' => 'premium-model',
            'taxonomy_type' => 7,
            'taxonomy_sort' => 3,
            'taxonomy_status' => 'ACTIVE',
        ]);
    }
}
