<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\ProductCategory;
use App\Models\ProductVariantStock;
use App\Models\ProductVariantOption;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Store;
use App\Models\TaxoList;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create default store
        $defaultStore = Store::firstOrCreate(
            ['code' => 'STORE-001'],
            [
                'name' => 'Main Store',
                'code' => 'STORE-001',
                'status' => 'ACTIVE',
            ]
        );

        // Create or get Attribute (Warna)
        $warnaAttribute = Attribute::firstOrCreate(
            ['slug' => 'warna'],
            [
                'name' => 'Warna',
                'slug' => 'warna',
                'sort' => 1,
                'status' => 'ACTIVE',
            ]
        );

        // Create or get Attribute (Tipe)
        $tipeAttribute = Attribute::firstOrCreate(
            ['slug' => 'tipe'],
            [
                'name' => 'Tipe',
                'slug' => 'tipe',
                'sort' => 2,
                'status' => 'ACTIVE',
            ]
        );

        // Create or get Attribute (Ukuran)
        $ukuranAttribute = Attribute::firstOrCreate(
            ['slug' => 'ukuran'],
            [
                'name' => 'Ukuran',
                'slug' => 'ukuran',
                'sort' => 3,
                'status' => 'ACTIVE',
            ]
        );

        // Product 1 - Lemari Pakaian minimalis TW 05
        $product1 = Product::firstOrCreate([
            'name' => 'Heim Studio MOKU Lemari',
            'slug' => 'heim-studio-moku-lemari',
            'is_freeshiping' => 'ACTIVE',
            'product_information' => 'Heim Studio MOKU Lemari',
            'meta_keywords' => 'heim studio,moku,lemari',
            'meta_description' => 'Heim Studio MOKU Lemari',
            'meta_title' => 'Heim Studio MOKU Lemari',
            'weight' => 10,
            'type_weight' => 'KG',
            'size_long' => 80,
            'size_tall' => 120,
            'size_wide' => 40,
            'type_size' => 'CM',
            'sort' => 1,
            'tags' => 'heim studio,moku,lemari',
            'status' => 'PUBLISH',
            'base_price' => 1669000,
        ]);

        // Add product 1 to category - Lemari Pakaian
        $lemariPakaianCategory = TaxoList::where('taxonomy_slug', 'lemari-pakaian')
            ->where('taxonomy_type', 2)
            ->first();
        if ($lemariPakaianCategory) {
            ProductCategory::firstOrCreate([
                'fk_product_id' => $product1->id,
                'fk_category_id' => $lemariPakaianCategory->id
            ]);
        }

        // ========== PRODUCT 1 ATTRIBUTES SETUP ==========
        // Create ProductAttribute for Warna
        $product1WarnaAttribute = ProductAttribute::firstOrCreate(
            [
                'product_id' => $product1->id,
                'attribute_id' => $warnaAttribute->id,
            ],
            [
                'sort' => 1,
            ]
        );

        // Create ProductAttribute for Tipe
        $product1TipeAttribute = ProductAttribute::firstOrCreate(
            [
                'product_id' => $product1->id,
                'attribute_id' => $tipeAttribute->id,
            ],
            [
                'sort' => 2,
            ]
        );

        // Create AttributeValues for Warna
        $putihValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $warnaAttribute->id,
                'slug' => 'putih',
            ],
            [
                'attribute_id' => $warnaAttribute->id,
                'value' => 'Putih',
                'slug' => 'putih',
                'sort' => 1,
                'status' => 'ACTIVE',
            ]
        );

        $coklatValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $warnaAttribute->id,
                'slug' => 'coklat',
            ],
            [
                'attribute_id' => $warnaAttribute->id,
                'value' => 'Coklat',
                'slug' => 'coklat',
                'sort' => 2,
                'status' => 'ACTIVE',
            ]
        );

        // Create AttributeValues for Tipe
        $Lemari6LaciValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $tipeAttribute->id,
                'slug' => 'lemari-6-laci',
            ],
            [
                'attribute_id' => $tipeAttribute->id,
                'value' => 'Lemari 6 Laci',
                'slug' => 'lemari-6-laci',
                'sort' => 1,
                'status' => 'ACTIVE',
            ]
        );

        $LemariPakaian2PintuValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $tipeAttribute->id,
                'slug' => 'lemari-pakaian-2-pintu',
            ],
            [
                'attribute_id' => $tipeAttribute->id,
                'value' => 'Lemari Pakaian 2 Pintu',
                'slug' => 'lemari-pakaian-2-pintu',
                'sort' => 2,
                'status' => 'ACTIVE',
            ]
        );

        // Link AttributeValues to ProductAttribute via ProductAttributeValue
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product1WarnaAttribute->id,
            'attribute_value_id' => $putihValue->id,
        ]);
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product1WarnaAttribute->id,
            'attribute_value_id' => $coklatValue->id,
        ]);
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product1WarnaAttribute->id,
            'attribute_value_id' => $Lemari6LaciValue->id,
        ]);

        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product1TipeAttribute->id,
            'attribute_value_id' => $LemariPakaian2PintuValue->id,
        ]);
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product1TipeAttribute->id,
            'attribute_value_id' => $Lemari6LaciValue->id,
        ]);

        // ========== PRODUCT 1 VARIANTS (4 combinations) ==========
        // Variant 1: Putih + Lemari 6 Laci (1669000)
        $product1Variant1 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product1->id,
            'variant_name' => 'Putih - Lemari 6 Laci',
            'sku' => 'HEIM-STUDIO-MOKU-LEMARI-6-LACI-PUTIH',
            'price' => 1669000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/NRA-401093.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant1->id,
            'attribute_id' => $warnaAttribute->id,
            'attribute_value_id' => $putihValue->id,
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant1->id,
            'attribute_id' => $tipeAttribute->id,
            'attribute_value_id' => $Lemari6LaciValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product1Variant1->id,
            'store_id' => $defaultStore->id,
            'qty' => 15,
            'reserved_qty' => 0,
        ]);

        // Variant 2: Coklat + Lemari 6 Laci (1699000)
        $product1Variant2 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product1->id,
            'variant_name' => 'Coklat - Lemari 6 Laci',
            'sku' => 'HEIM-STUDIO-MOKU-LEMARI-6-LACI-COKAT',
            'price' => 1699000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/NRA-372626.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant2->id,
            'attribute_id' => $warnaAttribute->id,
            'attribute_value_id' => $coklatValue->id,
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant2->id,
            'attribute_id' => $tipeAttribute->id,
            'attribute_value_id' => $Lemari6LaciValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product1Variant2->id,
            'store_id' => $defaultStore->id,
            'qty' => 20,
            'reserved_qty' => 0,
        ]);

        // Variant 3: Putih + Lemari Pakaian 2 Pintu (2149000)
        $product1Variant3 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product1->id,
            'variant_name' => 'Putih - Lemari Pakaian 2 Pintu',
            'sku' => 'HEIM-STUDIO-MOKU-LEMARI-2-PINTU-PUTIH',
            'price' => 2149000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/NRA-401096.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant3->id,
            'attribute_id' => $warnaAttribute->id,
            'attribute_value_id' => $putihValue->id,
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant3->id,
            'attribute_id' => $tipeAttribute->id,
            'attribute_value_id' => $LemariPakaian2PintuValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product1Variant3->id,
            'store_id' => $defaultStore->id,
            'qty' => 12,
            'reserved_qty' => 0,
        ]);

        // Variant 4: Coklat + Lemari Pakaian 2 Pintu (349000)
        $product1Variant4 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product1->id,
            'variant_name' => 'Coklat - Lemari Pakaian 2 Pintu',
            'sku' => 'HEIM-STUDIO-MOKU-LEMARI-2-PINTU-COKAT',
            'price' => 2149000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/NRA-375453.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant4->id,
            'attribute_id' => $warnaAttribute->id,
            'attribute_value_id' => $coklatValue->id,
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product1Variant4->id,
            'attribute_id' => $tipeAttribute->id,
            'attribute_value_id' => $LemariPakaian2PintuValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product1Variant4->id,
            'store_id' => $defaultStore->id,
            'qty' => 18,
            'reserved_qty' => 0,
        ]);

        // Images for Product 1
        ProductImage::firstOrCreate([
            'fk_product_id' => $product1->id,
            'path' => 'https://images.tokopedia.net/img/cache/700/VqbcmM/2023/6/2/2563406a-6919-4b8a-a1e7-f85774bfa392.jpg',
            'order_number' => 1,
            'is_featured' => true,
        ]);


        // Product 2 - Heim Studio CHOJI Bangku Pijak Lipat
        $product2 = Product::firstOrCreate([
            'name' => 'Heim Studio CHOJI Bangku Pijak Lipat',
            'slug' => 'heim-studio-choji-bangku-pijak-lipat',
            'is_freeshiping' => 'ACTIVE',
            'product_information' => 'Heim Studio CHOJI Bangku Pijak Lipat',
            'meta_keywords' => 'heim studio,choji,bangku,pijak,lipat',
            'meta_description' => 'Heim Studio CHOJI Bangku Pijak Lipat',
            'meta_title' => 'Heim Studio CHOJI Bangku Pijak Lipat',
            'weight' => 1.8,
            'type_weight' => 'KG',
            'size_long' => 34,
            'size_tall' => 23,
            'size_wide' => 1.8,
            'type_size' => 'CM',
            'sort' => 2,
            'tags' => 'heim studio,choji,bangku,pijak,lipat',
            'status' => 'PUBLISH',
            'base_price' => 59000,
        ]);

        // Add product 2 to category - Kursi
        $kursiCategory = TaxoList::where('taxonomy_slug', 'kursi')
            ->where('taxonomy_type', 2)
            ->first();
        if ($kursiCategory) {
            ProductCategory::firstOrCreate([
                'fk_product_id' => $product2->id,
                'fk_category_id' => $kursiCategory->id
            ]);
        }

        // Create ProductAttribute for Warna
        $product2UkuranAttribute = ProductAttribute::firstOrCreate(
            [
                'product_id' => $product2->id,
                'attribute_id' => $ukuranAttribute->id,
            ],
            [
                'sort' => 1,
            ]
        );

        // Create AttributeValue for Ukuran
        $ukuran22CmValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $ukuranAttribute->id,
                'slug' => '22-cm',
            ],
            [
                'attribute_id' => $ukuranAttribute->id,
                'value' => '22 CM',
                'slug' => '22-cm',
                'sort' => 1,
                'status' => 'ACTIVE',
            ]
        );

        $ukuran19CmValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $ukuranAttribute->id,
                'slug' => '19-cm',
            ],
            [
                'attribute_id' => $ukuranAttribute->id,
                'value' => '19 CM',
                'slug' => '19-cm',
                'sort' => 2,
                'status' => 'ACTIVE',
            ]
        );

        $ukuran39CmValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $ukuranAttribute->id,
                'slug' => '39-cm',
            ],
            [
                'attribute_id' => $ukuranAttribute->id,
                'value' => '39 CM',
                'slug' => '39-cm',
                'sort' => 3,
                'status' => 'ACTIVE',
            ]
        );

        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product2UkuranAttribute->id,
            'attribute_value_id' => $ukuran22CmValue->id,
        ]);
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product2UkuranAttribute->id,
            'attribute_value_id' => $ukuran19CmValue->id,
        ]);
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product2UkuranAttribute->id,
            'attribute_value_id' => $ukuran39CmValue->id,
        ]);

        // ========== PRODUCT 2 VARIANTS (3 combinations) ==========
        // Variant 1: 22 CM (599000)
        $product2Variant1 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product2->id,
            'variant_name' => '22 CM',
            'sku' => 'HEIM-STUDIO-CHOJI-BANGKU-PIJAK-LIPAT-22-CM',
            'price' => 89000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/NRA-475168.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product2Variant1->id,
            'attribute_id' => $ukuranAttribute->id,
            'attribute_value_id' => $ukuran22CmValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product2Variant1->id,
            'store_id' => $defaultStore->id,
            'qty' => 15,
            'reserved_qty' => 0,
        ]);
        // Variant 2: 19 CM (79000)
        $product2Variant2 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product2->id,
            'variant_name' => '19 CM',
            'sku' => 'HEIM-STUDIO-CHOJI-BANGKU-PIJAK-LIPAT-19-CM',
            'price' => 59000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/NRA-475167.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product2Variant2->id,
            'attribute_id' => $ukuranAttribute->id,
            'attribute_value_id' => $ukuran19CmValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product2Variant2->id,
            'store_id' => $defaultStore->id,
            'qty' => 15,
            'reserved_qty' => 0,
        ]);
        // Variant 3: 39 CM (129000)
        $product2Variant3 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product2->id,
            'variant_name' => '39 CM',
            'sku' => 'HEIM-STUDIO-CHOJI-BANGKU-PIJAK-LIPAT-39-CM',
            'price' => 129000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/NRA-475169.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product2Variant3->id,
            'attribute_id' => $ukuranAttribute->id,
            'attribute_value_id' => $ukuran39CmValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product2Variant3->id,
            'store_id' => $defaultStore->id,
            'qty' => 15,
            'reserved_qty' => 0,
        ]); 

        // Images for Product 2
        ProductImage::firstOrCreate([
            'fk_product_id' => $product2->id,
            'path' => 'https://media.dekoruma.com/catalogue/NRA-475169.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
            'order_number' => 1,
            'is_featured' => true,
        ]);

        // Product 3 - Aveda Bar Stool AC 602
        $product3 = Product::firstOrCreate([
            'name' => 'Aveda Bar Stool AC 602',
            'slug' => 'aveda-bar-stool-ac-602',
            'is_freeshiping' => 'ACTIVE',
            'product_information' => 'Aveda Bar Stool AC 602',
            'meta_keywords' => 'aveda,bar,stool,ac,602',
            'meta_description' => 'Aveda Bar Stool AC 602',
            'meta_title' => 'Aveda Bar Stool AC 602',
            'weight' => 1.8,
            'type_weight' => 'KG',
            'sort' => 3,
            'tags' => 'aveda,bar,stool,ac,602',
            'status' => 'PUBLISH',
            'base_price' => 1094000,
        ]);

        // Add product 3 to category - Kursi
        $kursiCategoryProduct3 = TaxoList::where('taxonomy_slug', 'kursi')
            ->where('taxonomy_type', 2)
            ->first();
        if ($kursiCategoryProduct3) {
            ProductCategory::firstOrCreate([
                'fk_product_id' => $product3->id,
                'fk_category_id' => $kursiCategoryProduct3->id
            ]);
        }

        // Create ProductAttribute for Warna
        $product3WarnaAttribute = ProductAttribute::firstOrCreate(
            [
                'product_id' => $product3->id,
                'attribute_id' => $warnaAttribute->id,
            ],
            [
                'sort' => 1,
            ]
        );

        // Create AttributeValue for Warna
        $putihValueProduct3 = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $warnaAttribute->id,
                'slug' => 'putih',
            ],
            [
                'attribute_id' => $warnaAttribute->id,
                'value' => 'Putih',
                'slug' => 'putih',
                'sort' => 1,
                'status' => 'ACTIVE',
            ]
        );

        $coklatMudaValue = AttributeValue::firstOrCreate(
            [
                'attribute_id' => $warnaAttribute->id,
                'slug' => 'coklat-muda',
            ],
            [
                'attribute_id' => $warnaAttribute->id,
                'value' => 'Coklat Muda',
                'slug' => 'coklat-muda',
                'sort' => 2,
                'status' => 'ACTIVE',
            ]
        );

        // Link AttributeValues to ProductAttribute via ProductAttributeValue
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product3WarnaAttribute->id,
            'attribute_value_id' => $putihValueProduct3->id,
        ]);
        ProductAttributeValue::firstOrCreate([
            'product_attribute_id' => $product3WarnaAttribute->id,
            'attribute_value_id' => $coklatMudaValue->id,
        ]);

        // Create ProductVariant for variant 1 (Putih)
        $product3Variant1 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product3->id,
            'variant_name' => 'Putih',
            'sku' => 'AVEDA-BAR-STOOL-AC-602-PUTIH',
            'price' => 1094000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/AVE-195.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product3Variant1->id,
            'attribute_id' => $warnaAttribute->id,
            'attribute_value_id' => $putihValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product3Variant1->id,
            'store_id' => $defaultStore->id,
            'qty' => 15,
            'reserved_qty' => 0,
        ]);
        
        $product3Variant2 = ProductVariant::firstOrCreate([
            'fk_product_id' => $product3->id,
            'variant_name' => 'Coklat Muda',
            'sku' => 'AVEDA-BAR-STOOL-AC-602-COKLAT-MUDA',
            'price' => 1094000,
            'status' => 'ACTIVE',
            'image_path' => 'https://media.dekoruma.com/catalogue/AVE-193.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
        ]);
        ProductVariantOption::firstOrCreate([
            'variant_id' => $product3Variant2->id,
            'attribute_id' => $warnaAttribute->id,
            'attribute_value_id' => $coklatMudaValue->id,
        ]);
        ProductVariantStock::firstOrCreate([
            'variant_id' => $product3Variant2->id,
            'store_id' => $defaultStore->id,
            'qty' => 15,
            'reserved_qty' => 0,
        ]);

        // Images for Product 3
        ProductImage::firstOrCreate([
            'fk_product_id' => $product3->id,
            'path' => 'https://media.dekoruma.com/catalogue/AVE-193.jpg?dpr=1.1&fit=bounds&height=1000&optimize=high&quality=60&trim-color=ffffff&width=1000',
            'order_number' => 1,
            'is_featured' => true,
        ]);

    }
}
