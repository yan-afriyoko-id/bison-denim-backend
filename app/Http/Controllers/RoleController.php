<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest\StoreRoleRequest;
use App\Http\Requests\RoleRequest\UpdateRoleRequest;
use App\Http\Resources\RoleResource\RoleResource;
use App\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * @var RoleRepositoryInterface
     */
    protected RoleRepositoryInterface $roleRepository;

    /**
     * RoleController constructor.
     *
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get all roles with pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $roles = $this->roleRepository->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => RoleResource::collection($roles->items()),
            'pagination' => [
                'current_page' => $roles->currentPage(),
                'from' => $roles->firstItem(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'to' => $roles->lastItem(),
                'total' => $roles->total(),
            ],
        ], 200);
    }

    /**
     * Search roles.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required.',
            ], 400);
        }

        $roles = $this->roleRepository->search($query);

        return response()->json([
            'success' => true,
            'data' => RoleResource::collection($roles),
        ], 200);
    }

    /**
     * Get single role.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role),
        ], 200);
    }

    /**
     * Create new role.
     *
     * @param StoreRoleRequest $request
     * @return JsonResponse
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleRepository->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Assign permissions if provided
        if ($request->has('permissions') && is_array($request->permissions)) {
            $this->roleRepository->syncPermissions($role->id, $request->permissions);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'data' => new RoleResource($this->roleRepository->findById($role->id)),
        ], 201);
    }

    /**
     * Update role.
     *
     * @param UpdateRoleRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
            ], 404);
        }

        $this->roleRepository->update($id, [
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Sync permissions if provided
        if ($request->has('permissions') && is_array($request->permissions)) {
            $this->roleRepository->syncPermissions($id, $request->permissions);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'data' => new RoleResource($this->roleRepository->findById($id)),
        ], 200);
    }

    /**
     * Delete role.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
            ], 404);
        }

        // Prevent deleting system roles
        $systemRoles = ['Super Admin', 'Admin', 'Manager', 'User'];
        if (in_array($role->name, $systemRoles)) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete system role '{$role->name}'.",
            ], 403);
        }

        $this->roleRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ], 200);
    }

    /**
     * Get role permissions.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getPermissions(int $id): JsonResponse
    {
        $role = $this->roleRepository->findById($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
            ], 404);
        }

        $permissions = $this->roleRepository->getPermissions($id);

        return response()->json([
            'success' => true,
            'data' => [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions_count' => count($permissions),
                'permissions' => $permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'description' => $permission->description,
                    ];
                }),
            ],
        ], 200);
    }
}

