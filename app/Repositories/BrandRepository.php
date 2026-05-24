<?php

namespace App\Repositories;

use App\Models\Brand;
use App\Interfaces\BrandRepositoryInterface;

class BrandRepository implements BrandRepositoryInterface
{
    /**
     * Create a new brand.
     *
     * @param array $data
     * @return Brand
     */
    public function create(array $data): Brand
    {
        return Brand::create($data);
    }

    /**
     * Find a brand by ID.
     *
     * @param int $id
     * @return Brand|null
     */
    public function findById(int $id): ?Brand
    {
        return Brand::find($id);
    }

    /**
     * Find a brand id by slug.
     *
     * @param array $slug
     * @param bool $onlyActive
     * @return int|null
     */
    public function findIdsBySlugs(array $slugs, bool $onlyActive = true): array
    {
        $query = Brand::whereIn('slug', $slugs);

        if ($onlyActive) {
            $query->active();
        }

        return $query->pluck('id')->toArray();
    }

    /**
     * Get all brands.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(?string $sortBy = null, string $sortDirection = 'desc')
    {
        $query = Brand::query();

        $allowedSortColumns = ['id', 'name', 'status', 'order', 'created_at'];
        $sortBy = $sortBy && in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';

        return $query->orderBy($sortBy, $sortDirection)->get();
    }

    /**
     * Get paginated brands.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc')
    {
        $query = Brand::query();

        // Validate sortBy to prevent SQL injection
        $allowedSortColumns = ['id', 'name', 'status', 'order', 'created_at'];
        $sortBy = $sortBy && in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';

        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * Update a brand.
     *
     * @param int $id
     * @param array $data
     * @return Brand|null
     */
    public function update(int $id, array $data): ?Brand
    {
        $brand = $this->findById($id);

        if (!$brand) {
            return null;
        }

        $brand->update($data);

        return $brand->fresh();
    }

    /**
     * Delete a brand.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $brand = $this->findById($id);

        if (!$brand) {
            return false;
        }

        return $brand->delete();
    }
}
