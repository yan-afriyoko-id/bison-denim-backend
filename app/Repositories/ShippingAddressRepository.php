<?php

namespace App\Repositories;

use App\Interfaces\ShippingAddressRepositoryInterface;
use App\Models\UserShippingAddress;
use Illuminate\Database\Eloquent\Collection;

class ShippingAddressRepository implements ShippingAddressRepositoryInterface
{
    /**
     * Get all shipping addresses for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return UserShippingAddress::where('user_id', $userId)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find a shipping address by ID.
     *
     * @param int $id
     * @param int|null $userId Optional user ID for authorization check
     * @return UserShippingAddress|null
     */
    public function findById(int $id, ?int $userId = null): ?UserShippingAddress
    {
        $query = UserShippingAddress::where('id', $id);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->first();
    }

    /**
     * Create a new shipping address.
     *
     * @param array $data
     * @return UserShippingAddress
     */
    public function create(array $data): UserShippingAddress
    {
        // If this is set as primary, unset other primary addresses
        if (isset($data['is_primary']) && $data['is_primary']) {
            UserShippingAddress::where('user_id', $data['user_id'])
                ->update(['is_primary' => false]);
        }

        return UserShippingAddress::create($data);
    }

    /**
     * Update a shipping address.
     *
     * @param int $id
     * @param array $data
     * @param int|null $userId Optional user ID for authorization check
     * @return UserShippingAddress|null
     */
    public function update(int $id, array $data, ?int $userId = null): ?UserShippingAddress
    {
        $address = $this->findById($id, $userId);

        if (!$address) {
            return null;
        }

        // If this is set as primary, unset other primary addresses
        if (isset($data['is_primary']) && $data['is_primary']) {
            UserShippingAddress::where('user_id', $address->user_id)
                ->where('id', '!=', $id)
                ->update(['is_primary' => false]);
        }

        $address->update($data);

        return $address->fresh();
    }

    /**
     * Delete a shipping address.
     *
     * @param int $id
     * @param int|null $userId Optional user ID for authorization check
     * @return bool
     */
    public function delete(int $id, ?int $userId = null): bool
    {
        $address = $this->findById($id, $userId);

        if (!$address) {
            return false;
        }

        return $address->delete();
    }

    /**
     * Set an address as primary and unset others for the user.
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function setAsPrimary(int $id, int $userId): bool
    {
        $address = $this->findById($id, $userId);

        if (!$address) {
            return false;
        }

        // Unset all primary addresses for this user
        UserShippingAddress::where('user_id', $userId)
            ->update(['is_primary' => false]);

        // Set this address as primary
        $address->update(['is_primary' => true]);

        return true;
    }
}

