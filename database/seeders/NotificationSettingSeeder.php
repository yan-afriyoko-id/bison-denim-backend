<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'email_notification_purchase',
                'setting_name' => 'Email Notification - Purchase',
                'is_active' => true,
                'description' => 'Send email notification when customer makes a purchase',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'email_notification_product',
                'setting_name' => 'Email Notification - Product',
                'is_active' => true,
                'description' => 'Send email notification for product updates',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'email_notification_order_status',
                'setting_name' => 'Email Notification - Order Status',
                'is_active' => true,
                'description' => 'Send email notification when order status changes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('notification_settings')->insert($settings);
    }
}

