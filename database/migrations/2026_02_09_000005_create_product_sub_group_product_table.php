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
        Schema::create('product_sub_group_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_sub_group_id')->constrained('product_sub_groups')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('sort')->default(0);
            $table->timestamps();
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['product_sub_group_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sub_group_product');
    }
};
