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
        Schema::create('product_sub_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_group_id')->constrained('product_groups')->onDelete('cascade');
            $table->string('title');
            $table->integer('sort')->default(0);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            
            $table->unique(['product_group_id', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sub_groups');
    }
};
