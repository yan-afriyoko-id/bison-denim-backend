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
        Schema::create('category_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_product_id');
            $table->foreignId('fk_category_id')->constrained('taxo_lists')->onDelete('cascade');
            $table->timestamps();
            $table->foreign('fk_product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['fk_product_id', 'fk_category_id'], 'cp_prod_id_cat_id_unique');
            
            // Indexes
            $table->index('fk_product_id');
            $table->index('fk_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_products');
    }
};
