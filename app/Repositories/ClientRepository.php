<?php

namespace App\Repositories;

use App\Models\Client;
use App\Interfaces\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Create a new client.
     *
     * @param array $data
     * @return Client
     */
    public function create(array $data): Client
    {
        return Client::create($data);
    }

    /**
     * Find a client by ID.
     *
     * @param int $id
     * @return Client|null
     */
    public function findById(int $id): ?Client
    {
        return Client::find($id);
    }

    /**
     * Find a client by ID_Client.
     *
     * @param string $idClient
     * @return Client|null
     */
    public function findByIdClient(string $idClient): ?Client
    {
        return Client::where('id_client', $idClient)->first();
    }

    /**
     * Get all clients.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Client::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get paginated clients.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc')
    {
        $query = Client::query();
        
        // Validate sortBy to prevent SQL injection
        $allowedSortColumns = ['id', 'id_client', 'client_name', 'email', 'phone', 'created_at'];
        $sortBy = $sortBy && in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
        
        $query->orderBy($sortBy, $sortDirection);
        
        return $query->paginate($perPage);
    }

    /**
     * Update a client.
     *
     * @param int $id
     * @param array $data
     * @return Client|null
     */
    public function update(int $id, array $data): ?Client
    {
        $client = $this->findById($id);

        if (!$client) {
            return null;
        }

        $client->update($data);

        return $client->fresh();
    }

    /**
     * Delete a client.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $client = $this->findById($id);

        if (!$client) {
            return false;
        }

        return $client->delete();
    }
}

