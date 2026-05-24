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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('cover')->nullable();
            $table->string('title');
            $table->text('short_desc')->nullable();
            $table->longText('long_desc')->nullable();
            $table->foreignId('fk_category')->constrained('category_blogs')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->boolean('status')->default(true);
            $table->boolean('hot_news')->default(false);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
