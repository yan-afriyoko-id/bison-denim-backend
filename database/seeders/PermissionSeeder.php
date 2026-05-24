<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guardName = 'web';

        // ===== BLOGS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'blogs.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'blogs.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'blogs.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'blogs.delete', 'guard_name' => $guardName]);

        // ===== BLOG CATEGORIES PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'blog-categories.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'blog-categories.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'blog-categories.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'blog-categories.delete', 'guard_name' => $guardName]);

        // ===== PRODUCTS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'products.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'products.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'products.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'products.delete', 'guard_name' => $guardName]);

        // ===== VOUCHERS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'vouchers.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'vouchers.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'vouchers.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'vouchers.delete', 'guard_name' => $guardName]);

        // ===== ORDERS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'orders.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'orders.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'orders.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'orders.delete', 'guard_name' => $guardName]);
        
        // ===== PRODUCT ATTRIBUTES PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'product-attributes.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-attributes.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-attributes.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-attributes.delete', 'guard_name' => $guardName]);

        // ===== PRODUCT CATEGORIES PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'product-categories.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-categories.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-categories.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-categories.delete', 'guard_name' => $guardName]);

        // ===== PRODUCT VARIANTS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'product-variants.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-variants.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-variants.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-variants.delete', 'guard_name' => $guardName]);

        // ===== PRODUCT IMAGES PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'product-images.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-images.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-images.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-images.delete', 'guard_name' => $guardName]);

        // ===== PRODUCT PRICES PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'product-prices.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-prices.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-prices.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'product-prices.delete', 'guard_name' => $guardName]);

        // ===== USERS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'users.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'users.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'users.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'users.delete', 'guard_name' => $guardName]);

        // ===== ROLES PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'roles.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'roles.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'roles.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'roles.delete', 'guard_name' => $guardName]);

        // ===== PERMISSIONS MANAGEMENT =====
        Permission::firstOrCreate(['name' => 'permissions.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'permissions.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'permissions.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'permissions.delete', 'guard_name' => $guardName]);

        // ===== CONFIGURATIONS MANAGEMENT =====
        Permission::firstOrCreate(['name' => 'configs.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'configs.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'configs.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'configs.delete', 'guard_name' => $guardName]);

        // ===== STORES PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'stores.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'stores.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'stores.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'stores.delete', 'guard_name' => $guardName]);

        // ===== BRANDS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'brands.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'brands.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'brands.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'brands.delete', 'guard_name' => $guardName]);

        // ===== MAIN BANNERS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'main-banners.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'main-banners.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'main-banners.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'main-banners.delete', 'guard_name' => $guardName]);

        // ===== POPUP BANNERS PERMISSIONS =====
        Permission::firstOrCreate(['name' => 'popup-banners.create', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'popup-banners.read', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'popup-banners.update', 'guard_name' => $guardName]);
        Permission::firstOrCreate(['name' => 'popup-banners.delete', 'guard_name' => $guardName]);
    }
}
