<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_group_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_group_id');
            $table->unsignedBigInteger('order_id');
            $table->timestamps();

            $table->foreign('payment_group_id')->references('id')->on('payment_groups')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unique(['payment_group_id', 'order_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_group_order');
    }
};
