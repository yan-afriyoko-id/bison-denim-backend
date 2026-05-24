<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest\StorePermissionRequest;
use App\Http\Requests\PermissionRequest\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource\PermissionResource;
use App\Interfaces\PermissionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * @var PermissionRepositoryInterface
     */
    protected PermissionRepositoryInterface $permissionRepository;

    /**
     * PermissionController constructor.
     *
     * @param PermissionRepositoryInterface $permissionRepository
     */
    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Get all permissions with pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $permissions = $this->permissionRepository->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions->items()),
            'pagination' => [
                'current_page' => $permissions->currentPage(),
                'from' => $permissions->firstItem(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'to' => $permissions->lastItem(),
                'total' => $permissions->total(),
            ],
        ], 200);
    }

    /**
     * Get all permissions (without pagination).
     *
     * @return JsonResponse
     */
    public function all(): JsonResponse
    {
        $permissions = $this->permissionRepository->getAll();

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions),
        ], 200);
    }

    /**
     * Get permissions by module.
     *
     * @param string $module
     * @return JsonResponse
     */
    public function byModule(string $module): JsonResponse
    {
        $permissions = $this->permissionRepository->getByModule($module);

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions),
        ], 200);
    }

    /**
     * Search permissions.
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

        $permissions = $this->permissionRepository->search($query);

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions),
        ], 200);
    }

    /**
     * Get single permission.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $permission = $this->permissionRepository->findById($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PermissionResource($permission),
        ], 200);
    }

    /**
     * Create new permission.
     *
     * @param StorePermissionRequest $request
     * @return JsonResponse
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->permissionRepository->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully.',
            'data' => new PermissionResource($permission),
        ], 201);
    }

    /**
     * Update permission.
     *
     * @param UpdatePermissionRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        $permission = $this->permissionRepository->findById($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found.',
            ], 404);
        }

        $this->permissionRepository->update($id, [
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully.',
            'data' => new PermissionResource($this->permissionRepository->findById($id)),
        ], 200);
    }

    /**
     * Delete permission.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $permission = $this->permissionRepository->findById($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission not found.',
            ], 404);
        }

        $this->permissionRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully.',
        ], 200);
    }

    /**
     * Get permissions grouped by module.
     *
     * @return JsonResponse
     */
    public function groupedByModule(): JsonResponse
    {
        $permissions = $this->permissionRepository->getAll();

        $grouped = $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'other';
        })->map(function ($perms, $module) {
            return [
                'module' => $module,
                'count' => $perms->count(),
                'permissions' => $perms->map(function ($perm) {
                    return [
                        'id' => $perm->id,
                        'name' => $perm->name,
                        'description' => $perm->description,
                    ];
                }),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $grouped,
        ], 200);
    }
}

