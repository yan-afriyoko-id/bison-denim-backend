<?php

namespace App\Interfaces;

use App\Models\Config;

interface ConfigRepositoryInterface
{
    /**
     * Get all configs
     */
    public function getAll();

    /**
     * Get config by key
     */
    public function getByKey(string $key): ?Config;

    /**
     * Get value by key
     */
    public function getValue(string $key, mixed $default = null);

    /**
     * Get all configs
     */
    public function getAllAsKeyValue() : array;

    /**
     * Create new config
     */
    public function create(array $data): Config;

    /**
     * Update config by key
     */
    public function updateByKey(string $key, array $data): Config;

    /**
     * Delete config by key
     */
    public function deleteByKey(string $key): bool;

    /**
     * Set or update config value by key
     */
    public function set(string $key, mixed $value, array $metadata = []): Config;
}

