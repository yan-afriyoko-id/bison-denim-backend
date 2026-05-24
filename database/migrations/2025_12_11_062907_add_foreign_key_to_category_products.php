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
        // Add foreign key constraint to category_products after products table exists
        Schema::table('category_products', function (Blueprint $table) {
            $table->foreign('fk_product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_products', function (Blueprint $table) {
            $table->dropForeign(['fk_product_id']);
        });
    }
};

