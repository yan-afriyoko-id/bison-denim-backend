<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest\StoreUserRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Http\Resources\UserResource\UserResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users (paginated).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $sortBy = $request->get('sort_by');
        $sortDirection = $request->get('sort_direction', 'desc');

        $users = $this->userRepository->paginate((int) $perPage, $sortBy, $sortDirection);

        // Load roles for all users
        $users->getCollection()->load('roles');

        return response()->json([
            'success' => true,
            'data' => [
                'users' => UserResource::collection($users->items()),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
                'sort' => [
                    'sort_by' => $sortBy ?? 'created_at',
                    'sort_direction' => $sortDirection,
                ],
            ],
        ], 200);
    }

    /**
     * Get all users without pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $users = $this->userRepository->all();
        $users->load('roles');

        return response()->json([
            'success' => true,
            'data' => [
                'users' => UserResource::collection($users),
            ],
        ], 200);
    }

    /**
     * Store a newly created user.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userRepository->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 201);
    }

    /**
     * Display the specified user.
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
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => new UserResource($user),
            ],
        ], 200);
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $data = [];
        if ($request->has('name')) {
            $data['name'] = $request->name;
        }
        if ($request->has('email')) {
            $data['email'] = $request->email;
        }
        if ($request->has('password')) {
            $data['password'] = $request->password;
        }

        $updatedUser = $this->userRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [
                'user' => new UserResource($updatedUser),
            ],
        ], 200);
    }

    /**
     * Remove the specified user.
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
                'message' => 'User not found',
            ], 404);
        }

        $this->userRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ], 200);
    }

    /**
     * Get current authenticated user profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully',
            'data' => [
                'profile' => new UserResource($user),
            ],
        ], 200);
    }

    /**
     * Update current authenticated user profile.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        // Validate input
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $user->id],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }

                    $cleaned = preg_replace('/[\s\-()]/', '', trim($value));

                    if (!preg_match('/^\+?[0-9]{2,15}$/', $cleaned)) {
                        $fail('Phone number must contain only digits and may start with +');
                    }
                },
            ],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:MALE,FEMALE,OTHER'],
            'avatar' => ['nullable', 'string'],
        ], [
            'email.unique' => 'Email already in use by another user.',
            'email.email' => 'Please enter a valid email address.',
            'gender.in' => 'Gender must be MALE, FEMALE, or OTHER.',
        ]);

        // Remove null values from validated data
        $data = array_filter($validated, fn($value) => !is_null($value));

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid fields to update',
            ], 422);
        }

        // Update user
        $updatedUser = $this->userRepository->update($user->id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'profile' => new UserResource($updatedUser),
            ],
        ], 200);
    }
}

