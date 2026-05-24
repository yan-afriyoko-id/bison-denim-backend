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
        Schema::create('store_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('fk_product_id')->constrained('products')->onDelete('cascade');
            $table->integer('stock')->default(0)->comment('Stok produk di store ini');
            $table->decimal('shipping_cost', 15, 2)->nullable()->comment('Biaya pengiriman dari store ini');
            $table->integer('estimated_days_min')->nullable()->comment('Estimasi hari pengiriman minimum');
            $table->integer('estimated_days_max')->nullable()->comment('Estimasi hari pengiriman maksimum');
            $table->boolean('is_available')->default(true)->comment('Apakah produk tersedia di store ini');
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['store_id', 'fk_product_id']);
            
            // Indexes
            $table->index('store_id');
            $table->index('fk_product_id');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_products');
    }
};
