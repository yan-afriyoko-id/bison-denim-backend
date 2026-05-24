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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Nullable untuk anonymous review');
            $table->integer('rating')->comment('Rating 1-5');
            $table->text('comment')->nullable()->comment('Komentar review');
            $table->date('review_date')->comment('Tanggal review');
            $table->boolean('is_approved')->default(false)->comment('Status approval review');
            $table->timestamps();
            
            // Indexes
            $table->index('fk_product_id');
            $table->index('user_id');
            $table->index('rating');
            $table->index('review_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
