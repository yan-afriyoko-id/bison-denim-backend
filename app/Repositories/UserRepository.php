<?php

namespace App\Repositories;

use App\Models\User;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return User::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get paginated users.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc')
    {
        $query = User::query();
        
        // Validate sortBy to prevent SQL injection
        $allowedSortColumns = ['id', 'name', 'email', 'created_at', 'updated_at'];
        $sortBy = $sortBy && in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
        
        $query->orderBy($sortBy, $sortDirection);
        
        return $query->paginate($perPage);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get user profile.
     *
     * @param int $id
     * @return User|null
     */
    public function getProfile(int $id): ?User
    {
        return User::with(['roles', 'permissions'])->find($id);
    }

    /**
     * Update user password.
     *
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    /**
     * Reset user password.
     *
     * @param int $id
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword(int $id, string $newPassword): bool
    {
        return $this->updatePassword($id, $newPassword);
    }

    /**
     * Update user profile.
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function updateProfile(int $id, array $data): ?User
    {
        $user = $this->findById($id);

        if (!$user) {
            return null;
        }

        $user->update($data);

        // Load relationships setelah update
        return $user->fresh(['roles', 'permissions']);
    }

    /**
     * Verify current password.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }

    /**
     * Update a user.
     *
     * @param int $id
     * @param array $data
     * @return User|null
     */
    public function update(int $id, array $data): ?User
    {
        $user = $this->findById($id);

        if (!$user) {
            return null;
        }

        $user->update($data);

        return $user->fresh();
    }

    /**
     * Delete a user.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }
}
