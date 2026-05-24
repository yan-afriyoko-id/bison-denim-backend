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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Configuration key (e.g., email_smtp, mailtrap_host)');
            $table->longText('value')->nullable()->comment('Configuration value');
            $table->string('description')->nullable()->comment('Description of the configuration');
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'text'])->default('string')->comment('Data type of the value');
            $table->timestamps();
            
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};

