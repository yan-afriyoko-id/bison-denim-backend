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
        Schema::create('product_variant_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->integer('qty')->default(0)->comment('Available stock di store ini');
            $table->integer('reserved_qty')->default(0)->comment('Reserved stock (sudah dipesan)');
            $table->timestamps();
            
            $table->index('variant_id');
            $table->index('store_id');
            $table->unique(['variant_id', 'store_id'], 'pvs_var_id_store_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_stocks');
    }
};

