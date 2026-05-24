<?php

namespace App\Interfaces;

use App\Models\UserShippingAddress;
use Illuminate\Database\Eloquent\Collection;

interface ShippingAddressRepositoryInterface
{
    /**
     * Get all shipping addresses for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection;

    /**
     * Find a shipping address by ID.
     *
     * @param int $id
     * @param int|null $userId Optional user ID for authorization check
     * @return UserShippingAddress|null
     */
    public function findById(int $id, ?int $userId = null): ?UserShippingAddress;

    /**
     * Create a new shipping address.
     *
     * @param array $data
     * @return UserShippingAddress
     */
    public function create(array $data): UserShippingAddress;

    /**
     * Update a shipping address.
     *
     * @param int $id
     * @param array $data
     * @param int|null $userId Optional user ID for authorization check
     * @return UserShippingAddress|null
     */
    public function update(int $id, array $data, ?int $userId = null): ?UserShippingAddress;

    /**
     * Delete a shipping address.
     *
     * @param int $id
     * @param int|null $userId Optional user ID for authorization check
     * @return bool
     */
    public function delete(int $id, ?int $userId = null): bool;

    /**
     * Set an address as primary and unset others for the user.
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function setAsPrimary(int $id, int $userId): bool;
}

