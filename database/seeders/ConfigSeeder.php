<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // ===== EMAIL SETTINGS =====
            [
                'key' => 'email_driver',
                'value' => 'smtp',
                'description' => 'Email driver (smtp, mailgun, postmark, etc)',
                'type' => 'string',
            ],
            [
                'key' => 'email_host',
                'value' => 'smtp.mailtrap.io',
                'description' => 'SMTP host',
                'type' => 'string',
            ],
            [
                'key' => 'email_port',
                'value' => '465',
                'description' => 'SMTP port',
                'type' => 'integer',
            ],
            [
                'key' => 'email_username',
                'value' => 'your_mailtrap_username',
                'description' => 'SMTP username/email',
                'type' => 'string',
            ],
            [
                'key' => 'email_password',
                'value' => 'your_mailtrap_password',
                'description' => 'SMTP password',
                'type' => 'string',
            ],
            [
                'key' => 'email_encryption',
                'value' => 'tls',
                'description' => 'SMTP encryption (tls, ssl)',
                'type' => 'string',
            ],
            [
                'key' => 'email_from_name',
                'value' => 'Bison Denim Admin',
                'description' => 'Email from name',
                'type' => 'string',
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@bisondenim.com',
                'description' => 'Email from address',
                'type' => 'string',
            ],

            // ===== GENERAL SETTINGS =====
            [
                'key' => 'app_name',
                'value' => 'Bison Denim',
                'description' => 'Application name',
                'type' => 'string',
            ],
            [
                'key' => 'app_url',
                'value' => 'http://localhost:8000',
                'description' => 'Application URL',
                'type' => 'string',
            ],
            [
                'key' => 'app_timezone',
                'value' => 'Asia/Jakarta',
                'description' => 'Application timezone',
                'type' => 'string',
            ],
            [
                'key' => 'app_locale',
                'value' => 'id',
                'description' => 'Application locale/language',
                'type' => 'string',
            ],

            // ===== STORE SETTINGS =====
            [
                'key' => 'store_name',
                'value' => 'Bison Denim',
                'description' => 'Store name',
                'type' => 'string',
            ],
            [
                'key' => 'store_email',
                'value' => 'store@bisondenim.com',
                'description' => 'Store email',
                'type' => 'string',
            ],
            [
                'key' => 'store_phone',
                'value' => '+62812345678',
                'description' => 'Store phone number',
                'type' => 'string',
            ],
            [
                'key' => 'store_address',
                'value' => 'Jakarta, Indonesia',
                'description' => 'Store address',
                'type' => 'string',
            ],
            [
                'key' => 'store_city',
                'value' => 'Jakarta',
                'description' => 'Store city',
                'type' => 'string',
            ],
            [
                'key' => 'store_province',
                'value' => 'DKI Jakarta',
                'description' => 'Store province',
                'type' => 'string',
            ],
            [
                'key' => 'store_country',
                'value' => 'Indonesia',
                'description' => 'Store country',
                'type' => 'string',
            ],
            [
                'key' => 'store_postal_code',
                'value' => '12345',
                'description' => 'Store postal code',
                'type' => 'string',
            ],
            [
                'key' => 'store_currency',
                'value' => 'IDR',
                'description' => 'Store currency',
                'type' => 'string',
            ],
            [
                'key' => 'store_logo_website',
                'value' => 'https://via.placeholder.com/300x100?text=Bison Denim+Logo',
                'description' => 'Store logo website (image URL or file path)',
                'type' => 'string',
            ],
            [
                'key' => 'store_favicon',
                'value' => 'https://via.placeholder.com/32x32?text=Bison Denim',
                'description' => 'Store favicon (image URL or file path)',
                'type' => 'string',
            ],

            // ===== SOCIAL MEDIA SETTINGS =====
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/bisondenim',
                'description' => 'Instagram URL',
                'type' => 'string',
            ],
            [
                'key' => 'social_tiktok',
                'value' => 'https://tiktok.com/@bisondenim',
                'description' => 'TikTok URL',
                'type' => 'string',
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/bisondenim',
                'description' => 'Facebook URL',
                'type' => 'string',
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/@bisondenim',
                'description' => 'YouTube URL',
                'type' => 'string',
            ],
            [
                'key' => 'social_pinterest',
                'value' => 'https://pinterest.com/bisondenim',
                'description' => 'Pinterest URL',
                'type' => 'string',
            ],
            [
                'key' => 'social_whatsapp',
                'value' => '6281234567890',
                'description' => 'WhatsApp number',
                'type' => 'string',
            ],
    
            // ===== PAYMENT SETTINGS =====
            [
                'key' => 'midtrans_server_key',
                'value' => 'your_midtrans_server_key',
                'description' => 'Midtrans server key',
                'type' => 'string',
            ],
            [
                'key' => 'midtrans_client_key',
                'value' => 'your_midtrans_client_key',
                'description' => 'Midtrans client key',
                'type' => 'string',
            ],
            [
                'key' => 'midtrans_is_production',
                'value' => 'false',
                'description' => 'Midtrans production mode',
                'type' => 'boolean',
            ],

            // ===== RAJAONGKIR SETTINGS =====
            [
                'key' => 'rajaongkir_key',
                'value' => '',
                'description' => 'RajaOngkir API Key',
                'type' => 'string',
            ],

            // ===== PRODUCT PROTECTION SETTINGS =====
            [
                'key' => 'product_protection',
                'value' => 10,
                'description' => 'Product Protection Percent',
                'type' => 'integer',
            ],

            // ===== NOTIFICATION SETTINGS =====
            [
                'key' => 'notification_email_enabled',
                'value' => 'true',
                'description' => 'Enable email notifications',
                'type' => 'boolean',
            ],
            [
                'key' => 'notification_sms_enabled',
                'value' => 'false',
                'description' => 'Enable SMS notifications',
                'type' => 'boolean',
            ],
        ];

        foreach ($configs as $config) {
            Config::firstOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
