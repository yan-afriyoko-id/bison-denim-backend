<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest\StoreClientRequest;
use App\Http\Requests\ClientRequest\UpdateClientRequest;
use App\Http\Resources\ClientResource\ClientResource;
use App\Models\Client;
use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * @var ClientRepositoryInterface
     */
    protected ClientRepositoryInterface $clientRepository;

    /**
     * ClientController constructor.
     *
     * @param ClientRepositoryInterface $clientRepository
     */
    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Display a listing of clients (paginated).
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

        $clients = $this->clientRepository->paginate((int) $perPage, $sortBy, $sortDirection);

        return response()->json([
            'success' => true,
            'data' => [
                'clients' => ClientResource::collection($clients->items()),
                'pagination' => [
                    'current_page' => $clients->currentPage(),
                    'per_page' => $clients->perPage(),
                    'total' => $clients->total(),
                    'last_page' => $clients->lastPage(),
                    'from' => $clients->firstItem(),
                    'to' => $clients->lastItem(),
                ],
                'sort' => [
                    'sort_by' => $sortBy ?? 'created_at',
                    'sort_direction' => $sortDirection,
                ],
            ],
        ], 200);
    }

    /**
     * Get all clients without pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $clients = $this->clientRepository->all();

        return response()->json([
            'success' => true,
            'data' => [
                'clients' => ClientResource::collection($clients),
            ],
        ], 200);
    }

    /**
     * Store a newly created client.
     *
     * @param StoreClientRequest $request
     * @return JsonResponse
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->clientRepository->create([
            'id_client' => Client::generateIdClient(),
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully',
            'data' => [
                'client' => new ClientResource($client),
            ],
        ], 201);
    }

    /**
     * Display the specified client.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'client' => new ClientResource($client),
            ],
        ], 200);
    }

    /**
     * Update the specified client.
     *
     * @param UpdateClientRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request, int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
            ], 404);
        }

        $data = [];
        if ($request->has('name')) {
            $data['name'] = $request->name;
        }
        if ($request->has('phone')) {
            $data['phone'] = $request->phone;
        }
        if ($request->has('address')) {
            $data['address'] = $request->address;
        }

        $updatedClient = $this->clientRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Client updated successfully',
            'data' => [
                'client' => new ClientResource($updatedClient),
            ],
        ], 200);
    }

    /**
     * Remove the specified client.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $client = $this->clientRepository->findById($id);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Client not found',
            ], 404);
        }

        $this->clientRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Client deleted successfully',
        ], 200);
    }
}

