<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN status 
            ENUM(
                'ORDER','PAYMENT','PROCESS','SHIPPED','COMPLETE',
                'PENDING','PACKING','DELIVERING','DELIVERED','COMPLETED',
                'CANCELLED'
            )
        ");
        
        DB::statement("UPDATE orders SET status = 'PENDING' WHERE status IN ('ORDER','PAYMENT')");
        DB::statement("UPDATE orders SET status = 'PACKING' WHERE status = 'PROCESS'");
        DB::statement("UPDATE orders SET status = 'DELIVERING' WHERE status = 'SHIPPED'");
        DB::statement("UPDATE orders SET status = 'COMPLETED' WHERE status = 'COMPLETE'");

        DB::statement("
            ALTER TABLE orders 
            MODIFY COLUMN status 
            ENUM('PENDING','PACKING','DELIVERING','DELIVERED','COMPLETED','CANCELLED')
            DEFAULT 'PENDING'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ke enum lama
        DB::statement("UPDATE orders SET status = 'ORDER' WHERE status = 'PENDING'");
        DB::statement("UPDATE orders SET status = 'PROCESS' WHERE status = 'PACKING'");
        DB::statement("UPDATE orders SET status = 'SHIPPED' WHERE status = 'DELIVERING'");
        DB::statement("UPDATE orders SET status = 'COMPLETE' WHERE status = 'COMPLETED'");
        
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('ORDER', 'PAYMENT', 'PROCESS', 'SHIPPED', 'COMPLETE', 'CANCELLED') DEFAULT 'ORDER'");
    }
};
