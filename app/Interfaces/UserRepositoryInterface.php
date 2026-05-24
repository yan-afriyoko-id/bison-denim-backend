<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Get paginated users.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc');

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get user profile.
     *
     * @param int $id
     * @return User|null
     */
    public function getProfile(int $id): ?User;

    /**
     * Update user password.
     *
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(int $id, string $newPassword): bool;

    /**
     * Reset user password.
     *
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword(int $id, string $newPassword): bool;

    /**
     * Update user profile.
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function updateProfile(int $id, array $data): ?User;

    /**
     * Verify current password.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function verifyPassword(User $user, string $password): bool;

    /**
     * Update a user.
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data): ?User;

    /**
     * Delete a user.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
