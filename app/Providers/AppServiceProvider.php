<?php

namespace App\Providers;

use App\Interfaces\BlogRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ConfigRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\StoreRepositoryInterface;
use App\Interfaces\BrandRepositoryInterface;
use App\Interfaces\ShippingAddressRepositoryInterface;
use App\Repositories\BlogRepository;
use App\Repositories\CategoryBlogRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ConfigRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\StoreRepository;
use App\Repositories\BrandRepository;
use App\Repositories\ShippingAddressRepository;
use App\Interfaces\CategoryBlogRepositoryInterface;
use App\Interfaces\MainBannerRepositoryInterface;
use App\Repositories\ProductReviewRepository;
use App\Interfaces\ProductReviewRepositoryInterface;
use App\Repositories\PopupBannerRepository;
use App\Interfaces\PopupBannerRepositoryInterface;
use App\Repositories\MainBannerRepository;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        $this->app->bind(
            ClientRepositoryInterface::class,
            ClientRepository::class
        );

        $this->app->bind(
            BlogRepositoryInterface::class,
            BlogRepository::class
        );

        $this->app->bind(
            CategoryBlogRepositoryInterface::class,
            CategoryBlogRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            RoleRepository::class
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            PermissionRepository::class
        );

        $this->app->bind(
            ConfigRepositoryInterface::class,
            ConfigRepository::class
        );

        $this->app->bind(
            StoreRepositoryInterface::class,
            StoreRepository::class
        );

        $this->app->bind(
            BrandRepositoryInterface::class,
            BrandRepository::class
        );

        $this->app->bind(
            ShippingAddressRepositoryInterface::class,
            ShippingAddressRepository::class
        );

        $this->app->bind(
            ProductReviewRepositoryInterface::class,
            ProductReviewRepository::class
        );

        $this->app->bind(
            PopupBannerRepositoryInterface::class,
            PopupBannerRepository::class
        );

        $this->app->bind(
            MainBannerRepositoryInterface::class,
            MainBannerRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ConfigRepositoryInterface $configRepo): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return config('app.frontend_url')
                . '/reset-password?token=' . $token . '&email=' . $user->email;
        });

        try {
            $settings = $configRepo->getAllAsKeyValue();
            Config::set(
                'mail.default',
                $settings['email_driver'] ?? config('mail.default')
            );

            Config::set(
                'mail.mailers.smtp.host',
                $settings['email_host'] ?? config('mail.mailers.smtp.host')
            );

            Config::set(
                'mail.mailers.smtp.port',
                $settings['email_port'] ?? config('mail.mailers.smtp.port')
            );

            Config::set(
                'mail.mailers.smtp.username',
                $settings['email_username'] ?? config('mail.mailers.smtp.username')
            );

            Config::set(
                'mail.mailers.smtp.password',
                $settings['email_password'] ?? config('mail.mailers.smtp.password')
            );

            Config::set(
                'mail.mailers.smtp.encryption',
                $settings['email_encryption'] ?? config('mail.mailers.smtp.encryption')
            );

            Config::set(
                'mail.from.address',
                $settings['email_from_address'] ?? config('mail.from.address')
            );

            Config::set(
                'mail.from.name',
                $settings['email_from_name'] ?? config('mail.from.name')
            );
            Config::set(
                'app.name',
                $settings['app_name'] ?? config('app.name')
            );
        } catch (\Throwable $e) {
        }
    }
}
