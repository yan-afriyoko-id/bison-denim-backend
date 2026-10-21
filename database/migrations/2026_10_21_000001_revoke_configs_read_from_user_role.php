<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Customers (User role) could read every config, including payment gateway secret keys.
 * Active payment gateways are now exposed through the public GET /api/payment/gateways endpoint.
 */
return new class extends Migration
{
    public function up(): void
    {
        $role = Role::where('name', 'User')->where('guard_name', 'web')->first();
        $permission = Permission::where('name', 'configs.read')->where('guard_name', 'web')->first();

        if ($role && $permission && $role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $role = Role::where('name', 'User')->where('guard_name', 'web')->first();
        $permission = Permission::where('name', 'configs.read')->where('guard_name', 'web')->first();

        if ($role && $permission) {
            $role->givePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
