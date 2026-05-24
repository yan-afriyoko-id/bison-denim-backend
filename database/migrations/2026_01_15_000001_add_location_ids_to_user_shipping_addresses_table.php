<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom ID dan label untuk semua level lokasi (Province, City, District, Sub-District)
     * sesuai dengan best practices marketplace seperti Tokopedia, Shopee, dll.
     * 
     * Alasan:
     * 1. ID diperlukan untuk perhitungan ongkir yang akurat via RajaOngkir API
     * 2. Label diperlukan untuk display di UI (karena nama bisa berubah)
     * 3. District dan Sub-District diperlukan untuk akurasi pengiriman
     */
    public function up(): void
    {
        Schema::table('user_shipping_addresses', function (Blueprint $table) {
            // Province ID dan Label (dari RajaOngkir)
            $table->integer('province_id')->nullable()->after('province')->comment('ID provinsi dari RajaOngkir');
            $table->string('province_label', 250)->nullable()->after('province_id')->comment('Nama provinsi untuk display');
            
            // City ID dan Label (dari RajaOngkir)
            $table->integer('city_id')->nullable()->after('city')->comment('ID kota/kabupaten dari RajaOngkir');
            $table->string('city_label', 250)->nullable()->after('city_id')->comment('Nama kota/kabupaten untuk display');
            
            // District ID dan Label (dari RajaOngkir) - Kecamatan
            $table->integer('district_id')->nullable()->after('city_label')->comment('ID kecamatan dari RajaOngkir');
            $table->string('district_label', 250)->nullable()->after('district_id')->comment('Nama kecamatan untuk display');
            
            // Sub-District ID dan Label (dari RajaOngkir) - Kelurahan
            $table->integer('sub_district_id')->nullable()->after('district_label')->comment('ID kelurahan dari RajaOngkir');
            $table->string('sub_district_label', 250)->nullable()->after('sub_district_id')->comment('Nama kelurahan untuk display');
            
            // Index untuk performa query
            $table->index('province_id');
            $table->index('city_id');
            $table->index('district_id');
            $table->index('sub_district_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_shipping_addresses', function (Blueprint $table) {
            $table->dropIndex(['province_id']);
            $table->dropIndex(['city_id']);
            $table->dropIndex(['district_id']);
            $table->dropIndex(['sub_district_id']);
            
            $table->dropColumn([
                'province_id',
                'province_label',
                'city_id',
                'city_label',
                'district_id',
                'district_label',
                'sub_district_id',
                'sub_district_label',
            ]);
        });
    }
};
