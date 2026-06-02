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
        Schema::create('user_shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('first_name', 250);
            $table->string('last_name', 250)->nullable();
            $table->string('phone', 50);
            $table->string('email', 250)->nullable();
            $table->string('label_place', 250)->nullable()->comment('Label seperti: Rumah, Kantor, dll');
            $table->longText('address');
            $table->string('city', 250);
            $table->integer('city_id')->nullable()->comment('ID kota/kabupaten dari RajaOngkir');
            $table->string('city_label', 250)->nullable()->comment('Nama kota/kabupaten untuk display');
            $table->string('province', 250);
            $table->integer('province_id')->nullable()->comment('ID provinsi dari RajaOngkir');
            $table->string('province_label', 250)->nullable()->comment('Nama provinsi untuk display');
            $table->integer('district_id')->nullable()->comment('ID kecamatan dari RajaOngkir');
            $table->string('district_label', 250)->nullable()->comment('Nama kecamatan untuk display');
            $table->integer('sub_district_id')->nullable()->comment('ID kelurahan dari RajaOngkir');
            $table->string('sub_district_label', 250)->nullable()->comment('Nama kelurahan untuk display');
            $table->string('postal_code', 50);
            $table->text('note_address')->nullable()->comment('Catatan alamat pengiriman');
            $table->boolean('is_primary')->default(false)->comment('Alamat utama');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
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
        Schema::dropIfExists('user_shipping_addresses');
    }
};
