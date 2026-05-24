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
        Schema::create('brand_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_brand_id');
            $table->unsignedBigInteger('fk_product_id');

            $table->foreign('fk_brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('fk_product_id')->references('id')->on('products')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_products');
    }
};
