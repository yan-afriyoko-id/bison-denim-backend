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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_product_id')->constrained('products')->onDelete('cascade');
            $table->string('path')->comment('Path/link ke gambar');
            $table->integer('order_number')->default(0)->comment('Urutan tampil gambar (0 = pertama)');
            $table->boolean('is_featured')->default(false)->comment('Gambar utama/featured untuk produk');
            $table->timestamps();
            
            // Indexes
            $table->index('fk_product_id');
            $table->index('order_number');
            $table->index(['fk_product_id', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
