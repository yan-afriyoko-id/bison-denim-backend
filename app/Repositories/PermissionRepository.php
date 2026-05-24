<?php

namespace App\Repositories;

use App\Interfaces\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Get all permissions.
     *
     * @return mixed
     */
    public function getAll()
    {
        return Permission::where('guard_name', 'web')->get();
    }

    /**
     * Find permission by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return Permission::where('guard_name', 'web')->find($id);
    }

    /**
     * Find permission by name.
     *
     * @param string $name
     * @return mixed
     */
    public function findByName(string $name)
    {
        return Permission::where('guard_name', 'web')
            ->where('name', $name)
            ->first();
    }

    /**
     * Create new permission.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        // Check if permission already exists
        $existing = $this->findByName($data['name']);
        if ($existing) {
            return $existing;
        }

        return Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web',
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Update permission.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $permission = $this->findById($id);

        if (!$permission) {
            return null;
        }

        $permission->update([
            'name' => $data['name'] ?? $permission->name,
            'description' => $data['description'] ?? $permission->description,
        ]);

        return $permission;
    }

    /**
     * Delete permission.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $permission = $this->findById($id);

        if (!$permission) {
            return false;
        }

        return $permission->delete();
    }

    /**
     * Get permissions by multiple IDs.
     *
     * @param array $ids
     * @return mixed
     */
    public function getByIds(array $ids)
    {
        return Permission::where('guard_name', 'web')
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * Get permissions by category/module.
     *
     * @param string $module
     * @return mixed
     */
    public function getByModule(string $module)
    {
        return Permission::where('guard_name', 'web')
            ->where('name', 'like', "{$module}.%")
            ->get();
    }

    /**
     * Paginate permissions.
     *
     * @param int $perPage
     * @return mixed
     */
    public function paginate(int $perPage = 15)
    {
        return Permission::where('guard_name', 'web')
            ->paginate($perPage);
    }

    /**
     * Search permissions.
     *
     * @param string $query
     * @return mixed
     */
    public function search(string $query)
    {
        return Permission::where('guard_name', 'web')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }
}

