<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->integer('city_id')->nullable()->after('city')->comment('City ID from RajaOngkir for shipping cost calculation');
            $table->index('city_id');
        });
    }

    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex(['city_id']);
            $table->dropColumn('city_id');
        });
    }
};
