<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->string('slug', 250)->unique();
            
            // Information
            $table->longText('product_information')->nullable();
            
            // SEO Metadata
            $table->longText('meta_keywords')->nullable()->comment('Keywords untuk meta head tags');
            $table->longText('meta_description')->nullable()->comment('Description untuk meta head tags');
            $table->longText('meta_title')->nullable()->comment('Title untuk meta head tags');
            
            // Specifications
            $table->string('material')->nullable()->comment('Material produk (e.g., MDF, honeycomb)');
            $table->string('finishing')->nullable()->comment('Finishing produk (e.g., high gloss lacquered)');
            $table->string('color')->nullable()->comment('Warna produk');
            $table->decimal('weight', 8, 2)->nullable()->comment('Berat dalam kg/gram');
            $table->enum('type_weight', ['GRAM', 'KG'])->nullable();
            $table->decimal('size_long', 8, 2)->nullable()->comment('Panjang dalam cm/m');
            $table->decimal('size_wide', 8, 2)->nullable()->comment('Lebar dalam cm/m');
            $table->decimal('size_tall', 8, 2)->nullable()->comment('Tinggi dalam cm/m');
            $table->enum('type_size', ['CM', 'M'])->nullable();
            $table->decimal('package_long', 8, 2)->nullable()->comment('Dimensi kemasan - panjang');
            $table->decimal('package_wide', 8, 2)->nullable()->comment('Dimensi kemasan - lebar');
            $table->decimal('package_tall', 8, 2)->nullable()->comment('Dimensi kemasan - tinggi');
            $table->string('sku')->nullable()->comment('Stock Keeping Unit');
            $table->decimal('base_price', 15, 2)->nullable()->comment('Harga transaksi produk (untuk produk tanpa variant)');
            $table->decimal('base_strike_price', 15, 2)->nullable()->comment('Harga yang dicoret/strike price (optional, untuk produk tanpa variant)');
            $table->decimal('base_discount_percent', 5, 2)->nullable()->comment('Persentase diskon (dihitung otomatis di BE, untuk produk tanpa variant)');
            
            // Product Settings
            $table->enum('is_freeshiping', ['ACTIVE', 'INACTIVE'])->default('INACTIVE');
            $table->integer('sort')->default(0)->comment('Nomor urut untuk sorting');
            $table->string('tags')->nullable()->comment('Tag produk (comma-separated)');
            $table->enum('status', ['PUBLISH', 'INACTIVE', 'DRAFT'])->default('DRAFT');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('slug');
            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
