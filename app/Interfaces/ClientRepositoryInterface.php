<?php

namespace App\Interfaces;

use App\Models\Client;

interface ClientRepositoryInterface
{
    /**
     * Create a new client.
     *
     * @param array $data
     * @return Client
     */
    public function create(array $data): Client;

    /**
     * Find a client by ID.
     *
     * @param int $id
     * @return Client|null
     */
    public function findById(int $id): ?Client;

    /**
     * Find a client by ID_Client.
     *
     * @param string $idClient
     * @return Client|null
     */
    public function findByIdClient(string $idClient): ?Client;

    /**
     * Get all clients.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Get paginated clients.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc');

    /**
     * Update a client.
     *
     * @param int $id
     * @param array $data
     * @return Client|null
     */
    public function update(int $id, array $data): ?Client;

    /**
     * Delete a client.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}

