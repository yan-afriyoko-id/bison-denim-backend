<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class LogoFaviconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $logoPath = $baseUrl . '/uploads/logo/logo.png';
        $faviconPath = $baseUrl . '/uploads/favicon/favicon.png';
        Config::updateOrCreate(
            ['key' => 'store_logo_website'],
            [
                'value' => $logoPath,
                'description' => 'Store logo website (image URL or file path)',
                'type' => 'string',
            ]
        );
        Config::updateOrCreate(
            ['key' => 'store_favicon'],
            [
                'value' => $faviconPath,
                'description' => 'Store favicon (image URL or file path)',
                'type' => 'string',
            ]
        );
    }
}
