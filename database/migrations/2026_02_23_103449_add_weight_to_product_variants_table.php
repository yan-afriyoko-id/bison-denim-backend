<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('weight', 10, 2)->nullable()->after('price')->comment('Berat per unit (misal gram atau kg)');
            $table->enum('type_weight', ['GRAM', 'KG'])->default('GRAM')->after('weight')->comment('Satuan berat');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['weight', 'type_weight']);
        });
    }
};