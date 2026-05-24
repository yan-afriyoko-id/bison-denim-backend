<?php

namespace App\Interfaces;

interface PermissionRepositoryInterface
{
    /**
     * Get all permissions.
     *
     * @return mixed
     */
    public function getAll();

    /**
     * Find permission by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id);

    /**
     * Find permission by name.
     *
     * @param string $name
     * @return mixed
     */
    public function findByName(string $name);

    /**
     * Create new permission.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update permission.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * Delete permission.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get permissions by multiple IDs.
     *
     * @param array $ids
     * @return mixed
     */
    public function getByIds(array $ids);

    /**
     * Get permissions by category/module.
     *
     * @param string $module
     * @return mixed
     */
    public function getByModule(string $module);

    /**
     * Paginate permissions.
     *
     * @param int $perPage
     * @return mixed
     */
    public function paginate(int $perPage = 15);

    /**
     * Search permissions.
     *
     * @param string $query
     * @return mixed
     */
    public function search(string $query);
}

