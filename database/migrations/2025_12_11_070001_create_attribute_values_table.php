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
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('attributes')->onDelete('cascade');
            $table->string('value', 100)->comment('Attribute value (e.g., Red, Blue, S, M, L)');
            $table->string('slug', 100);
            $table->integer('sort')->default(0);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            
            $table->index('attribute_id');
            $table->index('status');
            $table->index('sort');
            $table->unique(['attribute_id', 'slug'], 'av_attr_id_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};

