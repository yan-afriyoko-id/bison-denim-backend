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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('fk_order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('fk_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('fk_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            
            // Product Information (snapshot saat order dibuat)
            $table->string('product_name', 250)->comment('Nama produk saat order dibuat');
            $table->string('product_image', 250)->nullable()->comment('Gambar produk saat order dibuat');
            $table->string('sku', 250)->comment('SKU produk/variant saat order dibuat');
            $table->longText('variant_description')->nullable()->comment('Deskripsi variant (e.g., "Merah - L")');
            
            // Pricing (snapshot saat order dibuat)
            $table->integer('qty')->default(1)->comment('Jumlah produk');
            $table->integer('actual_price')->default(0)->comment('Harga aktual per item (sebelum diskon)');
            $table->integer('discount_price')->nullable()->comment('Harga setelah diskon per item');
            $table->integer('purchase_price')->default(0)->comment('Harga pembelian per item (final price)');
            $table->integer('subtotal')->default(0)->comment('Subtotal (purchase_price * qty)');
            
            // Additional Information
            $table->longText('note')->nullable()->comment('Catatan khusus untuk item ini');
            
            $table->timestamps();
            
            // Indexes
            $table->index('fk_order_id');
            $table->index('fk_product_id');
            $table->index('fk_variant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};


