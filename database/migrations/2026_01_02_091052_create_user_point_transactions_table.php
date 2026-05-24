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
        Schema::create('user_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id')->nullable()->comment('Order yang menghasilkan poin');
            $table->enum('transaction_type', ['EARNED', 'USED', 'EXPIRED', 'ADJUSTMENT']);
            $table->integer('points')->comment('Jumlah poin (positif untuk earned, negatif untuk used)');
            $table->text('description')->nullable()->comment('Keterangan transaksi');
            $table->string('reference_id', 250)->nullable()->comment('ID referensi (order_id, voucher_id, dll)');
            $table->timestamp('expires_at')->nullable()->comment('Tanggal kadaluarsa poin (jika ada)');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->index('user_id');
            $table->index('order_id');
            $table->index('transaction_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_point_transactions');
    }
};
