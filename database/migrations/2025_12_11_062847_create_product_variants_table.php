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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_product_id')->constrained('products')->onDelete('cascade');
            // Variant Info
            $table->string('variant_name')->nullable()->comment('Variant combination name (e.g., "Red - L", "Blue - S")');
            $table->string('sku')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 15, 2)->nullable()->comment('Harga transaksi variant');
            $table->decimal('weight', 10, 2)->nullable()->comment('Berat per unit (misal gram atau kg)');
            $table->enum('type_weight', ['GRAM', 'KG'])->default('GRAM')->comment('Satuan berat');
            $table->decimal('strike_price', 15, 2)->nullable()->comment('Harga yang dicoret/strike price (optional)');
            $table->decimal('discount_percent', 5, 2)->nullable()->comment('Persentase diskon (dihitung otomatis di BE)');
            $table->boolean('is_ignore_stock')->default(false);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('fk_product_id');
            $table->index('status');
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
