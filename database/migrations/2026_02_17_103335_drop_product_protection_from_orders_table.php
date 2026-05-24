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
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'product_protection_percent')) {
                $table->dropColumn('product_protection_percent');
            }
            if (Schema::hasColumn('orders', 'product_protection_price')) {
                $table->dropColumn('product_protection_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('product_protection_percent', 5, 2)->nullable();
            $table->decimal('product_protection_price', 15, 2)->nullable();
        });
    }
};
