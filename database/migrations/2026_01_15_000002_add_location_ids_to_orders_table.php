<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Shipping Address - Province
            $table->integer('shipping_province_id')->nullable()->after('shipping_province')->comment('ID provinsi shipping dari RajaOngkir');
            $table->string('shipping_province_label', 250)->nullable()->after('shipping_province_id')->comment('Nama provinsi shipping untuk display');
            
            // Shipping Address - City
            $table->integer('shipping_city_id')->nullable()->after('shipping_city')->comment('ID kota/kabupaten shipping dari RajaOngkir');
            $table->string('shipping_city_label', 250)->nullable()->after('shipping_city_id')->comment('Nama kota/kabupaten shipping untuk display');
            
            // Shipping Address - District (Kecamatan)
            $table->integer('shipping_district_id')->nullable()->after('shipping_city_label')->comment('ID kecamatan shipping dari RajaOngkir');
            $table->string('shipping_district_label', 250)->nullable()->after('shipping_district_id')->comment('Nama kecamatan shipping untuk display');
            
            // Shipping Address - Sub-District (Kelurahan)
            $table->integer('shipping_sub_district_id')->nullable()->after('shipping_district_label')->comment('ID kelurahan shipping dari RajaOngkir');
            $table->string('shipping_sub_district_label', 250)->nullable()->after('shipping_sub_district_id')->comment('Nama kelurahan shipping untuk display');
            
            // Billing Address - Province
            $table->integer('billing_province_id')->nullable()->after('billing_province')->comment('ID provinsi billing dari RajaOngkir');
            $table->string('billing_province_label', 250)->nullable()->after('billing_province_id')->comment('Nama provinsi billing untuk display');
            
            // Billing Address - City
            $table->integer('billing_city_id')->nullable()->after('billing_city')->comment('ID kota/kabupaten billing dari RajaOngkir');
            $table->string('billing_city_label', 250)->nullable()->after('billing_city_id')->comment('Nama kota/kabupaten billing untuk display');
            
            // Billing Address - District (Kecamatan)
            $table->integer('billing_district_id')->nullable()->after('billing_city_label')->comment('ID kecamatan billing dari RajaOngkir');
            $table->string('billing_district_label', 250)->nullable()->after('billing_district_id')->comment('Nama kecamatan billing untuk display');
            
            // Billing Address - Sub-District (Kelurahan)
            $table->integer('billing_sub_district_id')->nullable()->after('billing_district_label')->comment('ID kelurahan billing dari RajaOngkir');
            $table->string('billing_sub_district_label', 250)->nullable()->after('billing_sub_district_id')->comment('Nama kelurahan billing untuk display');
            
            $table->index('shipping_province_id');
            $table->index('shipping_city_id');
            $table->index('shipping_district_id');
            $table->index('shipping_sub_district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['shipping_province_id']);
            $table->dropIndex(['shipping_city_id']);
            $table->dropIndex(['shipping_district_id']);
            $table->dropIndex(['shipping_sub_district_id']);
            
            $table->dropColumn([
                'shipping_province_id',
                'shipping_province_label',
                'shipping_city_id',
                'shipping_city_label',
                'shipping_district_id',
                'shipping_district_label',
                'shipping_sub_district_id',
                'shipping_sub_district_label',
                'billing_province_id',
                'billing_province_label',
                'billing_city_id',
                'billing_city_label',
                'billing_district_id',
                'billing_district_label',
                'billing_sub_district_id',
                'billing_sub_district_label',
            ]);
        });
    }
};
