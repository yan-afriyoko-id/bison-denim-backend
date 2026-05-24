<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->foreignId('order_item_id')
                  ->nullable()
                  ->constrained('order_items')
                  ->onDelete('cascade');
                  
            $table->unique(['user_id', 'order_item_id'], 'unique_user_order_item_review');
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropColumn('order_item_id');
            $table->dropUnique('unique_user_order_item_review');
        });
    }
};