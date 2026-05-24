<?php

namespace App\Interfaces;

use App\Models\Brand;

interface BrandRepositoryInterface
{
    /**
     * Create a new brand.
     *
     * @param array $data
     * @return Brand
     */
    public function create(array $data): Brand;

    /**
     * Find a brand by ID.
     *
     * @param int $id
     * @return Brand|null
     */
    public function findById(int $id): ?Brand;

    /**
     * Find a brand id by slug.
     *
     * @param array $slug
     * @param bool $onlyActive
     * @return array
     */
    public function findIdsBySlugs(array $slugs, bool $onlyActive = true): array;

    /**
     * Get all brands.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @param string|null $sortBy
     * @param string $sortDirection
     */
    public function all(?string $sortBy = null, string $sortDirection = 'desc');

    /**
     * Get paginated brands.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc');

    /**
     * Update a brand.
     *
     * @param int $id
     * @param array $data
     * @return Brand|null
     */
    public function update(int $id, array $data): ?Brand;

    /**
     * Delete a brand.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
