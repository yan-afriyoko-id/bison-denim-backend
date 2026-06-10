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
        $defaultStore = Store::firstOrCreate(
            ['code' => 'STORE-001'],
            [
                'name' => 'Main Store',
                'code' => 'STORE-001',
                'status' => 'ACTIVE',
            ]
        );

        $warnaAttribute = Attribute::firstOrCreate(
            ['slug' => 'warna'],
            ['name' => 'Warna', 'slug' => 'warna', 'sort' => 1, 'status' => 'ACTIVE']
        );

        $ukuranAttribute = Attribute::firstOrCreate(
            ['slug' => 'ukuran'],
            ['name' => 'Ukuran', 'slug' => 'ukuran', 'sort' => 2, 'status' => 'ACTIVE']
        );

        $product1 = Product::firstOrCreate(
            ['slug' => 'celana-jeans-slim-fit-bd-001'],
            [
                'name' => 'Celana Jeans Slim Fit BD-001',
                'slug' => 'celana-jeans-slim-fit-bd-001',
                'is_freeshiping' => 'ACTIVE',
                'product_information' => 'Celana jeans slim fit berbahan denim premium dengan potongan modern yang nyaman dipakai seharian.',
                'meta_keywords' => 'celana jeans,slim fit,denim,bison denim',
                'meta_description' => 'Celana Jeans Slim Fit premium dari Bison Denim. Bahan denim berkualitas tinggi, potongan slim fit yang stylish.',
                'meta_title' => 'Celana Jeans Slim Fit BD-001 - Bison Denim',
                'weight' => 0.5,
                'type_weight' => 'KG',
                'size_long' => 100,
                'size_tall' => 2,
                'size_wide' => 40,
                'type_size' => 'CM',
                'sort' => 1,
                'tags' => 'celana jeans,slim fit,denim,pria',
                'status' => 'PUBLISH',
                'base_price' => 299000,
            ]
        );

        $celanaJeansCategory = TaxoList::where('taxonomy_slug', 'celana-jeans')
            ->where('taxonomy_type', 2)
            ->first();
        if ($celanaJeansCategory) {
            ProductCategory::firstOrCreate([
                'fk_product_id' => $product1->id,
                'fk_category_id' => $celanaJeansCategory->id,
            ]);
        }

        $p1WarnaAttr = ProductAttribute::firstOrCreate(
            ['product_id' => $product1->id, 'attribute_id' => $warnaAttribute->id],
            ['sort' => 1]
        );
        $p1UkuranAttr = ProductAttribute::firstOrCreate(
            ['product_id' => $product1->id, 'attribute_id' => $ukuranAttribute->id],
            ['sort' => 2]
        );

        $indigoValue = AttributeValue::firstOrCreate(
            ['attribute_id' => $warnaAttribute->id, 'slug' => 'indigo'],
            ['attribute_id' => $warnaAttribute->id, 'value' => 'Indigo', 'slug' => 'indigo', 'sort' => 1, 'status' => 'ACTIVE']
        );
        $hitamValue = AttributeValue::firstOrCreate(
            ['attribute_id' => $warnaAttribute->id, 'slug' => 'hitam'],
            ['attribute_id' => $warnaAttribute->id, 'value' => 'Hitam', 'slug' => 'hitam', 'sort' => 2, 'status' => 'ACTIVE']
        );
        $ukuranMValue = AttributeValue::firstOrCreate(
            ['attribute_id' => $ukuranAttribute->id, 'slug' => 'ukuran-m'],
            ['attribute_id' => $ukuranAttribute->id, 'value' => 'M', 'slug' => 'ukuran-m', 'sort' => 1, 'status' => 'ACTIVE']
        );
        $ukuranLValue = AttributeValue::firstOrCreate(
            ['attribute_id' => $ukuranAttribute->id, 'slug' => 'ukuran-l'],
            ['attribute_id' => $ukuranAttribute->id, 'value' => 'L', 'slug' => 'ukuran-l', 'sort' => 2, 'status' => 'ACTIVE']
        );

        foreach ([$indigoValue, $hitamValue] as $val) {
            ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $p1WarnaAttr->id,
                'attribute_value_id' => $val->id,
            ]);
        }
        foreach ([$ukuranMValue, $ukuranLValue] as $val) {
            ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $p1UkuranAttr->id,
                'attribute_value_id' => $val->id,
            ]);
        }

        // Variants: Indigo-M, Indigo-L, Hitam-M, Hitam-L
        $p1Variants = [
            ['name' => 'Indigo - M', 'sku' => 'BD-JEANS-SLIM-INDIGO-M', 'price' => 299000, 'warna' => $indigoValue, 'ukuran' => $ukuranMValue, 'qty' => 20],
            ['name' => 'Indigo - L', 'sku' => 'BD-JEANS-SLIM-INDIGO-L', 'price' => 299000, 'warna' => $indigoValue, 'ukuran' => $ukuranLValue, 'qty' => 25],
            ['name' => 'Hitam - M',  'sku' => 'BD-JEANS-SLIM-HITAM-M',  'price' => 309000, 'warna' => $hitamValue,  'ukuran' => $ukuranMValue, 'qty' => 18],
            ['name' => 'Hitam - L',  'sku' => 'BD-JEANS-SLIM-HITAM-L',  'price' => 309000, 'warna' => $hitamValue,  'ukuran' => $ukuranLValue, 'qty' => 22],
        ];

        foreach ($p1Variants as $v) {
            $variant = ProductVariant::firstOrCreate(
                ['fk_product_id' => $product1->id, 'sku' => $v['sku']],
                ['variant_name' => $v['name'], 'sku' => $v['sku'], 'price' => $v['price'], 'status' => 'ACTIVE',
                 'image_path' => 'https://via.placeholder.com/600x800?text=Jeans+'.$v['name']]
            );
            ProductVariantOption::firstOrCreate(['variant_id' => $variant->id, 'attribute_id' => $warnaAttribute->id, 'attribute_value_id' => $v['warna']->id]);
            ProductVariantOption::firstOrCreate(['variant_id' => $variant->id, 'attribute_id' => $ukuranAttribute->id, 'attribute_value_id' => $v['ukuran']->id]);
            ProductVariantStock::firstOrCreate(['variant_id' => $variant->id, 'store_id' => $defaultStore->id], ['qty' => $v['qty'], 'reserved_qty' => 0]);
        }

        ProductImage::firstOrCreate(
            ['fk_product_id' => $product1->id, 'order_number' => 1],
            ['path' => 'https://via.placeholder.com/600x800?text=Celana+Jeans+BD-001', 'order_number' => 1, 'is_featured' => true]
        );

        // =====================================================
        // Product 2 - Kemeja Flannel Kotak-Kotak
        // =====================================================
        $product2 = Product::firstOrCreate(
            ['slug' => 'kemeja-flannel-kotak-kotak-bd-002'],
            [
                'name' => 'Kemeja Flannel Kotak-Kotak BD-002',
                'slug' => 'kemeja-flannel-kotak-kotak-bd-002',
                'is_freeshiping' => 'ACTIVE',
                'product_information' => 'Kemeja flannel motif kotak-kotak dengan bahan lembut dan hangat, cocok untuk tampilan kasual sehari-hari.',
                'meta_keywords' => 'kemeja flannel,kotak-kotak,kasual,bison denim',
                'meta_description' => 'Kemeja Flannel Kotak-Kotak BD-002 dari Bison Denim. Bahan lembut dan hangat untuk tampilan kasual.',
                'meta_title' => 'Kemeja Flannel Kotak-Kotak BD-002 - Bison Denim',
                'weight' => 0.3,
                'type_weight' => 'KG',
                'size_long' => 75,
                'size_tall' => 2,
                'size_wide' => 55,
                'type_size' => 'CM',
                'sort' => 2,
                'tags' => 'kemeja,flannel,kotak,kasual',
                'status' => 'PUBLISH',
                'base_price' => 189000,
            ]
        );

        $kemejaCatergory = TaxoList::where('taxonomy_slug', 'kemeja')
            ->where('taxonomy_type', 2)
            ->first();
        if ($kemejaCatergory) {
            ProductCategory::firstOrCreate([
                'fk_product_id' => $product2->id,
                'fk_category_id' => $kemejaCatergory->id,
            ]);
        }

        $p2UkuranAttr = ProductAttribute::firstOrCreate(
            ['product_id' => $product2->id, 'attribute_id' => $ukuranAttribute->id],
            ['sort' => 1]
        );

        $ukuranXLValue = AttributeValue::firstOrCreate(
            ['attribute_id' => $ukuranAttribute->id, 'slug' => 'ukuran-xl'],
            ['attribute_id' => $ukuranAttribute->id, 'value' => 'XL', 'slug' => 'ukuran-xl', 'sort' => 3, 'status' => 'ACTIVE']
        );

        foreach ([$ukuranMValue, $ukuranLValue, $ukuranXLValue] as $val) {
            ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $p2UkuranAttr->id,
                'attribute_value_id' => $val->id,
            ]);
        }

        $p2Variants = [
            ['name' => 'M',  'sku' => 'BD-FLANNEL-M',  'price' => 189000, 'ukuran' => $ukuranMValue,  'qty' => 30],
            ['name' => 'L',  'sku' => 'BD-FLANNEL-L',  'price' => 189000, 'ukuran' => $ukuranLValue,  'qty' => 35],
            ['name' => 'XL', 'sku' => 'BD-FLANNEL-XL', 'price' => 199000, 'ukuran' => $ukuranXLValue, 'qty' => 20],
        ];

        foreach ($p2Variants as $v) {
            $variant = ProductVariant::firstOrCreate(
                ['fk_product_id' => $product2->id, 'sku' => $v['sku']],
                ['variant_name' => $v['name'], 'sku' => $v['sku'], 'price' => $v['price'], 'status' => 'ACTIVE',
                 'image_path' => 'https://via.placeholder.com/600x800?text=Flannel+'.$v['name']]
            );
            ProductVariantOption::firstOrCreate(['variant_id' => $variant->id, 'attribute_id' => $ukuranAttribute->id, 'attribute_value_id' => $v['ukuran']->id]);
            ProductVariantStock::firstOrCreate(['variant_id' => $variant->id, 'store_id' => $defaultStore->id], ['qty' => $v['qty'], 'reserved_qty' => 0]);
        }

        ProductImage::firstOrCreate(
            ['fk_product_id' => $product2->id, 'order_number' => 1],
            ['path' => 'https://via.placeholder.com/600x800?text=Kemeja+Flannel+BD-002', 'order_number' => 1, 'is_featured' => true]
        );

        $product3 = Product::firstOrCreate(
            ['slug' => 'jaket-denim-klasik-bd-003'],
            [
                'name' => 'Jaket Denim Klasik BD-003',
                'slug' => 'jaket-denim-klasik-bd-003',
                'is_freeshiping' => 'ACTIVE',
                'product_information' => 'Jaket denim klasik dengan potongan timeless yang cocok dipadukan dengan berbagai outfit. Bahan denim tebal dan tahan lama.',
                'meta_keywords' => 'jaket denim,klasik,outerwear,bison denim',
                'meta_description' => 'Jaket Denim Klasik BD-003 dari Bison Denim. Potongan timeless, bahan denim premium tahan lama.',
                'meta_title' => 'Jaket Denim Klasik BD-003 - Bison Denim',
                'weight' => 0.7,
                'type_weight' => 'KG',
                'size_long' => 65,
                'size_tall' => 3,
                'size_wide' => 58,
                'type_size' => 'CM',
                'sort' => 3,
                'tags' => 'jaket,denim,outerwear,klasik',
                'status' => 'PUBLISH',
                'base_price' => 499000,
            ]
        );

        $jaketCategory = TaxoList::where('taxonomy_slug', 'jaket-outerwear')
            ->where('taxonomy_type', 2)
            ->first();
        if ($jaketCategory) {
            ProductCategory::firstOrCreate([
                'fk_product_id' => $product3->id,
                'fk_category_id' => $jaketCategory->id,
            ]);
        }

        $p3WarnaAttr = ProductAttribute::firstOrCreate(
            ['product_id' => $product3->id, 'attribute_id' => $warnaAttribute->id],
            ['sort' => 1]
        );

        $navyValue = AttributeValue::firstOrCreate(
            ['attribute_id' => $warnaAttribute->id, 'slug' => 'navy'],
            ['attribute_id' => $warnaAttribute->id, 'value' => 'Navy', 'slug' => 'navy', 'sort' => 3, 'status' => 'ACTIVE']
        );

        foreach ([$hitamValue, $navyValue] as $val) {
            ProductAttributeValue::firstOrCreate([
                'product_attribute_id' => $p3WarnaAttr->id,
                'attribute_value_id' => $val->id,
            ]);
        }

        $p3Variants = [
            ['name' => 'Hitam', 'sku' => 'BD-JAKET-DENIM-HITAM', 'price' => 499000, 'warna' => $hitamValue, 'qty' => 15],
            ['name' => 'Navy',  'sku' => 'BD-JAKET-DENIM-NAVY',  'price' => 499000, 'warna' => $navyValue,  'qty' => 15],
        ];

        foreach ($p3Variants as $v) {
            $variant = ProductVariant::firstOrCreate(
                ['fk_product_id' => $product3->id, 'sku' => $v['sku']],
                ['variant_name' => $v['name'], 'sku' => $v['sku'], 'price' => $v['price'], 'status' => 'ACTIVE',
                 'image_path' => 'https://via.placeholder.com/600x800?text=Jaket+Denim+'.$v['name']]
            );
            ProductVariantOption::firstOrCreate(['variant_id' => $variant->id, 'attribute_id' => $warnaAttribute->id, 'attribute_value_id' => $v['warna']->id]);
            ProductVariantStock::firstOrCreate(['variant_id' => $variant->id, 'store_id' => $defaultStore->id], ['qty' => $v['qty'], 'reserved_qty' => 0]);
        }

        ProductImage::firstOrCreate(
            ['fk_product_id' => $product3->id, 'order_number' => 1],
            ['path' => 'https://via.placeholder.com/600x800?text=Jaket+Denim+BD-003', 'order_number' => 1, 'is_featured' => true]
        );
    }
}
