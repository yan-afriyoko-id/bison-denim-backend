<?php

namespace App\Interfaces;

interface RoleRepositoryInterface
{
    /**
     * Get all roles.
     *
     * @return mixed
     */
    public function getAll();

    /**
     * Find role by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Find role by name.
     *
     * @param string $name
     * @return mixed
     */
    public function findByName(string $name);

    /**
     * Create new role.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update role.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * Delete role.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Assign permissions to role.
     *
     * @param int $id
     * @param array $permissionIds
     * @return mixed
     */
    public function syncPermissions(int $id, array $permissionIds);

    /**
     * Get role permissions.
     *
     * @param int $id
     * @return mixed
     */
    public function getPermissions(int $id);

    /**
     * Paginate roles.
     *
     * @param int $perPage
     * @return mixed
     */
    public function paginate(int $perPage = 15);

    /**
     * Search roles.
     *
     * @param string $query
     * @return mixed
     */
    public function search(string $query);
}

