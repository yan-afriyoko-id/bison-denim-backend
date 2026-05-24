<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource\AuthResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * UserManagementController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Get all users with their roles and permissions.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userRepository->all();

        return response()->json([
            'success' => true,
            'data' => AuthResource::collection($users),
        ], 200);
    }

    /**
     * Get single user with roles and permissions.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AuthResource($user),
        ], 200);
    }

    /**
     * Assign role to user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function assignRole(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ], [
            'role.required' => 'Role is required.',
            'role.exists' => 'The selected role does not exist.',
        ]);

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Assign role
        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => "Role '{$request->role}' assigned to user successfully.",
            'data' => new AuthResource($user),
        ], 200);
    }

    /**
     * Remove role from user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function removeRole(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ], [
            'role.required' => 'Role is required.',
            'role.exists' => 'The selected role does not exist.',
        ]);

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Remove role
        $user->removeRole($request->role);

        return response()->json([
            'success' => true,
            'message' => "Role '{$request->role}' removed from user successfully.",
            'data' => new AuthResource($user),
        ], 200);
    }

    /**
     * Sync roles for user (replace all roles).
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function syncRoles(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ], [
            'roles.required' => 'Roles are required.',
            'roles.array' => 'Roles must be an array.',
            'roles.*.exists' => 'One or more selected roles do not exist.',
        ]);

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Sync roles (replace all roles)
        $user->syncRoles($request->roles);

        return response()->json([
            'success' => true,
            'message' => 'User roles updated successfully.',
            'data' => new AuthResource($user),
        ], 200);
    }

    /**
     * Get all available roles.
     *
     * @return JsonResponse
     */
    public function getAllRoles(): JsonResponse
    {
        $roles = Role::where('guard_name', 'web')
            ->with('permissions')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'permissions_count' => $role->permissions->count(),
                    'permissions' => $role->permissions->pluck('name'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $roles,
        ], 200);
    }

    /**
     * Get user's permissions.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getUserPermissions(int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'roles' => $user->getRoleNames(),
                'direct_permissions' => $user->getDirectPermissions()->pluck('name'),
                'all_permissions' => $user->getAllPermissions()->pluck('name'),
            ],
        ], 200);
    }

    /**
     * Create new user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ], [
            'name.required' => 'Name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email already exists.',
            'phone.unique' => 'Phone number already exists.',
            'dob.required' => 'Date of birth is required.',
            'dob.before' => 'Date of birth must be in the past.',
            'gender.required' => 'Gender is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'roles.*.exists' => 'One or more selected roles do not exist.',
        ]);

        // Create user
        $user = $this->userRepository->create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'password' => $request->password,
        ]);

        // Assign roles if provided
        if ($request->has('roles') && is_array($request->roles)) {
            $user->syncRoles($request->roles);
        } else {
            // Assign default "User" role
            $user->assignRole('User');
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => new AuthResource($user),
        ], 201);
    }

    /**
     * Update user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $request->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:100', 'unique:users,email,' . $id],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'unique:users,phone,' . $id],
            'dob' => ['sometimes', 'date', 'before:today'],
            'gender' => ['sometimes', 'in:MALE,FEMALE'],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ], [
            'email.unique' => 'Email already exists.',
            'phone.unique' => 'Phone number already exists.',
            'dob.before' => 'Date of birth must be in the past.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'roles.*.exists' => 'One or more selected roles do not exist.',
        ]);

        // Update user data
        $updateData = [];
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('last_name')) {
            $updateData['last_name'] = $request->last_name;
        }
        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }
        if ($request->has('phone')) {
            $updateData['phone'] = $request->phone;
        }
        if ($request->has('dob')) {
            $updateData['dob'] = $request->dob;
        }
        if ($request->has('gender')) {
            $updateData['gender'] = $request->gender;
        }
        if ($request->has('password')) {
            $updateData['password'] = $request->password;
        }

        if (!empty($updateData)) {
            $this->userRepository->update($id, $updateData);
        }

        // Update roles if provided
        if ($request->has('roles') && is_array($request->roles)) {
            $user->syncRoles($request->roles);
        }

        // Refresh user data
        $user = $this->userRepository->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => new AuthResource($user),
        ], 200);
    }

    /**
     * Delete user.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Prevent deleting the first admin user
        if ($id === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the first admin user.',
            ], 403);
        }

        $this->userRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ], 200);
    }

    /**
     * Mark user email as verified.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function verifyEmail(int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified.',
            ], 400);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully.',
            'data' => new AuthResource($user),
        ], 200);
    }
}

