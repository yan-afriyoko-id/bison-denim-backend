<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            $settings = DB::table('configs')
                ->pluck('value', 'key')
                ->toArray();

            config(['settings' => $settings]);
        } catch (\Throwable $e) {
            // Prevent crash during migrations / CLI
        }
    }
}
