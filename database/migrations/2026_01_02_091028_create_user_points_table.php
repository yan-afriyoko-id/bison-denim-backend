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
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('points')->default(0)->comment('Total poin yang dimiliki user');
            $table->integer('earned_points')->default(0)->comment('Total poin yang pernah diperoleh');
            $table->integer('used_points')->default(0)->comment('Total poin yang pernah digunakan');
            $table->integer('cumulative_total')->default(0)->comment('Total kumulatif pembelian dari semua order PAID');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_points');
    }
};
