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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('review_id')
                ->nullable()
                ->constrained('product_reviews')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['review_id']);
                $table->dropColumn('review_id');
            });
        });
    }
};
