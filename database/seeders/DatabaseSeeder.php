<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create permissions first
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        // User::factory(10)->create();

        // Create Super Admin user
        $superAdmin = User::factory()->create([
            'name' => 'Test',
            'last_name' => 'User',
            'email' => 'admin@gmail.com',
            'phone' => '+6281234567890',
            'dob' => '1990-05-15',
            'gender' => 'MALE',
        ]);

        // Assign Super Admin role to test user
        $superAdmin->assignRole('Super Admin');

        // Create regular User
        $regularUser = User::factory()->create([
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'user@example.com',
            'phone' => '+6281234567891',
            'dob' => '1995-01-20',
            'gender' => 'MALE',
        ]);

        // Assign User role
        $regularUser->assignRole('User');

        $this->call([
            CategoryBlogSeeder::class,
            TaxoTypeSeeder::class,
            TaxoListSeeder::class,
            CategoryProductSeeder::class,
            ProductSeeder::class,
            ConfigSeeder::class,
            LogoFaviconSeeder::class,
            BrandSeeder::class,
            PointConfigSeeder::class,
            ProductGroupSeeder::class,
            TopBannerTextSeeder::class,
        ]);
    }
}
