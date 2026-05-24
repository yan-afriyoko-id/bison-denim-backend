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
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('discount_type', ['PERCENTAGE', 'FIXED'])->default('FIXED');
            $table->decimal('discount_value', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
