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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->integer('limit_user')->nullable();
            $table->integer('voucher_used')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('discount_type', ['PERCENTAGE', 'FIXED'])->default('FIXED');
            $table->decimal('discount_value', 15, 2);
            $table->unsignedBigInteger('min_purchase')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('fk_voucher_id')
                ->references('id')
                ->on('vouchers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['fk_voucher_id']);
        });

        Schema::dropIfExists('vouchers');
    }
};
