<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guardName = 'web';

        // Create Roles
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => $guardName]
        );

        $admin = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => $guardName]
        );

        $manager = Role::firstOrCreate(
            ['name' => 'Manager', 'guard_name' => $guardName]
        );

        $user = Role::firstOrCreate(
            ['name' => 'User', 'guard_name' => $guardName]
        );

        // Get all permissions
        $allPermissions = Permission::where('guard_name', $guardName)->get();

        // Super Admin gets all permissions
        $superAdmin->syncPermissions($allPermissions);

        // Admin gets all permissions except role management
        $adminPermissions = $allPermissions->filter(function ($permission) {
            return !str_starts_with($permission->name, 'roles.');
        });
        $admin->syncPermissions($adminPermissions);

        // Manager gets read, create, and update permissions (no delete)
        $managerPermissions = $allPermissions->filter(function ($permission) {
            return !str_ends_with($permission->name, '.delete') && !str_starts_with($permission->name, 'roles.');
        });
        $manager->syncPermissions($managerPermissions);

        // User gets only catalog read permissions. Configs hold payment gateway secrets, and
        // orders/users/permissions/vouchers.read expose every customer's data or admin-only voucher lists.
        $userExcluded = ['configs.read', 'orders.read', 'users.read', 'permissions.read', 'vouchers.read'];
        $userPermissions = $allPermissions->filter(function ($permission) use ($userExcluded) {
            return str_ends_with($permission->name, '.read')
                && !str_starts_with($permission->name, 'roles.')
                && !in_array($permission->name, $userExcluded, true);
        });
        $user->syncPermissions($userPermissions);
    }
}
