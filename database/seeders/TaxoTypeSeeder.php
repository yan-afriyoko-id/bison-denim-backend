<?php

namespace Database\Seeders;

use App\Models\TaxoType;
use Illuminate\Database\Seeder;

class TaxoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxoTypes = [
            [
                'taxo_type_name' => 'Collection',
                'taxo_type_description' => 'Product collections and special offers',
            ],
            [
                'taxo_type_name' => 'Category',
                'taxo_type_description' => 'Main product categories',
            ],
            [
                'taxo_type_name' => 'Subcategory',
                'taxo_type_description' => 'Product subcategories',
            ],
            [
                'taxo_type_name' => 'Brand',
                'taxo_type_description' => 'Product brands',
            ],
            [
                'taxo_type_name' => 'Color',
                'taxo_type_description' => 'Product color attributes',
            ],
            [
                'taxo_type_name' => 'Size',
                'taxo_type_description' => 'Product size attributes',
            ],
            [
                'taxo_type_name' => 'Model',
                'taxo_type_description' => 'Product model attributes',
            ],
            [
                'taxo_type_name' => 'Specification',
                'taxo_type_description' => 'Product specifications',
            ],
        ];

        foreach ($taxoTypes as $taxoType) {
            TaxoType::create($taxoType);
        }
    }
}
