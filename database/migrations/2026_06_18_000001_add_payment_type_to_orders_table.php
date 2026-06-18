<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_type')) {
                $table->string('payment_type', 250)
                    ->nullable()
                    ->after('payment_reference_code')
                    ->comment('Payment channel/type returned by payment gateway');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_type')) {
                $table->dropColumn('payment_type');
            }
        });
    }
};
