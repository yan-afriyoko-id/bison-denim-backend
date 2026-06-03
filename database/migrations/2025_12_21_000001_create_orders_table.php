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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->integer('queue_number')->nullable()->comment('Nomor antrian per bulan');
            $table->string('order_number', 250)->unique()->comment('Nomor order unik (format: ORD-YYYYMMDD-XXXX)');
            
            // Contact Information
            $table->string('contact_email', 250);
            $table->string('contact_phone', 250);
            
            // Shipping Address
            $table->string('shipping_country', 250)->default('Indonesia');
            $table->string('shipping_first_name', 250);
            $table->string('shipping_last_name', 250)->nullable();
            $table->longText('shipping_address');
            $table->string('shipping_city', 250);
            $table->integer('shipping_city_id')->nullable()->comment('ID kota/kabupaten shipping dari RajaOngkir');
            $table->string('shipping_city_label', 250)->nullable()->comment('Nama kota/kabupaten shipping untuk display');
            $table->string('shipping_province', 250);
            $table->integer('shipping_province_id')->nullable()->comment('ID provinsi shipping dari RajaOngkir');
            $table->string('shipping_province_label', 250)->nullable()->comment('Nama provinsi shipping untuk display');
            $table->integer('shipping_district_id')->nullable()->comment('ID kecamatan shipping dari RajaOngkir');
            $table->string('shipping_district_label', 250)->nullable()->comment('Nama kecamatan shipping untuk display');
            $table->integer('shipping_sub_district_id')->nullable()->comment('ID kelurahan shipping dari RajaOngkir');
            $table->string('shipping_sub_district_label', 250)->nullable()->comment('Nama kelurahan shipping untuk display');
            $table->string('shipping_postal_code', 250);
            $table->string('shipping_label_place', 250)->nullable()->comment('Label tempat (Rumah, Kantor, dll)');
            $table->string('shipping_note_address', 250)->nullable()->comment('Catatan alamat pengiriman');
            
            // Billing Address (optional, bisa sama dengan shipping)
            $table->string('billing_country', 250)->nullable();
            $table->string('billing_first_name', 250)->nullable();
            $table->string('billing_last_name', 250)->nullable();
            $table->longText('billing_address')->nullable();
            $table->string('billing_city', 250)->nullable();
            $table->integer('billing_city_id')->nullable()->comment('ID kota/kabupaten billing dari RajaOngkir');
            $table->string('billing_city_label', 250)->nullable()->comment('Nama kota/kabupaten billing untuk display');
            $table->string('billing_province', 250)->nullable();
            $table->integer('billing_province_id')->nullable()->comment('ID provinsi billing dari RajaOngkir');
            $table->string('billing_province_label', 250)->nullable()->comment('Nama provinsi billing untuk display');
            $table->integer('billing_district_id')->nullable()->comment('ID kecamatan billing dari RajaOngkir');
            $table->string('billing_district_label', 250)->nullable()->comment('Nama kecamatan billing untuk display');
            $table->integer('billing_sub_district_id')->nullable()->comment('ID kelurahan billing dari RajaOngkir');
            $table->string('billing_sub_district_label', 250)->nullable()->comment('Nama kelurahan billing untuk display');
            $table->string('billing_postal_code', 250)->nullable();
            $table->string('billing_label_place', 250)->nullable();
            $table->string('billing_note_address', 250)->nullable();
            
            // Courier/Shipping Information
            $table->string('courier_agent', 250)->nullable()->comment('Kurir (jne, tiki, pos, dll)');
            $table->string('courier_agent_service', 250)->nullable()->comment('Layanan kurir (REG, ONS, dll)');
            $table->string('courier_agent_service_desc', 250)->nullable()->comment('Deskripsi layanan kurir');
            $table->string('courier_estimate_delivered', 250)->nullable()->comment('Estimasi pengiriman (e.g., "2-3 hari")');
            $table->string('courier_resi_number', 250)->nullable()->comment('Nomor resi pengiriman');
            $table->integer('courier_cost')->default(0)->comment('Biaya pengiriman');
            
            // Payment Information
            $table->string('payment_method', 250)->nullable()->comment('Metode pembayaran (bank_transfer, e_wallet, dll)');
            $table->string('payment_reference_code', 250)->nullable()->comment('Kode referensi pembayaran');
            $table->string('payment_snap_token', 250)->nullable()->comment('Snap token untuk Midtrans');
            $table->enum('payment_status', ['PENDING', 'PAID', 'FAILED', 'CANCELLED', 'REFUNDED'])->default('PENDING');
            
            // Order Notes
            $table->longText('invoice_note')->nullable()->comment('Catatan untuk invoice');
            $table->longText('delivery_order_note')->nullable()->comment('Catatan untuk delivery order');
            
            // Order Totals
            $table->integer('subtotal')->default(0)->comment('Subtotal produk (sebelum diskon)');
            $table->integer('discount_amount')->default(0)->comment('Total diskon');
            $table->integer('points_used')->default(0);
            $table->integer('shipping_cost')->default(0)->comment('Biaya pengiriman');
            $table->integer('total_amount')->default(0)->comment('Total akhir yang harus dibayar');
            
            // Foreign Keys
            $table->foreignId('fk_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('fk_voucher_id')->nullable()->comment('Voucher yang digunakan (jika ada)');
            
            // Order Status
            $table->enum('status', ['PENDING', 'PACKING', 'DELIVERING', 'DELIVERED', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('order_number');
            $table->index('fk_user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('shipping_province_id');
            $table->index('shipping_city_id');
            $table->index('shipping_district_id');
            $table->index('shipping_sub_district_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
