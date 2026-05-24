<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    /**
     * Get all roles.
     *
     * @return mixed
     */
    public function getAll()
    {
        return Role::where('guard_name', 'web')
            ->with('permissions')
            ->get();
    }

    /**
     * Find role by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return Role::where('guard_name', 'web')
            ->with('permissions')
            ->find($id);
    }

    /**
     * Find role by name.
     *
     * @param string $name
     * @return mixed
     */
    public function findByName(string $name)
    {
        return Role::where('guard_name', 'web')
            ->where('name', $name)
            ->with('permissions')
            ->first();
    }

    /**
     * Create new role.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Update role.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $role = $this->findById($id);

        if (!$role) {
            return null;
        }

        $role->update([
            'name' => $data['name'] ?? $role->name,
            'description' => $data['description'] ?? $role->description,
        ]);

        return $role;
    }

    /**
     * Delete role.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $role = $this->findById($id);

        if (!$role) {
            return false;
        }

        return $role->delete();
    }

    /**
     * Assign permissions to role.
     *
     * @param int $id
     * @param array $permissionIds
     * @return mixed
     */
    public function syncPermissions(int $id, array $permissionIds)
    {
        $role = $this->findById($id);

        if (!$role) {
            return null;
        }

        $role->syncPermissions($permissionIds);

        return $role->load('permissions');
    }

    /**
     * Get role permissions.
     *
     * @param int $id
     * @return mixed
     */
    public function getPermissions(int $id)
    {
        $role = $this->findById($id);

        if (!$role) {
            return null;
        }

        return $role->permissions;
    }

    /**
     * Paginate roles.
     *
     * @param int $perPage
     * @return mixed
     */
    public function paginate(int $perPage = 15)
    {
        return Role::where('guard_name', 'web')
            ->with('permissions')
            ->paginate($perPage);
    }

    /**
     * Search roles.
     *
     * @param string $query
     * @return mixed
     */
    public function search(string $query)
    {
        return Role::where('guard_name', 'web')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->with('permissions')
            ->get();
    }
}

