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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->foreignId('attribute_value_id')->constrained('attribute_values')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['product_attribute_id', 'attribute_value_id'], 'pav_pa_id_av_id_unique');
            $table->index('product_attribute_id');
            $table->index('attribute_value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};

