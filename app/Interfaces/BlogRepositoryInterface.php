<?php

namespace App\Interfaces;

use App\Models\Blog;

interface BlogRepositoryInterface
{
    /**
     * Create a new blog.
     *
     * @param array $data
     * @return Blog
     */
    public function create(array $data): Blog;

    /**
     * Find a blog by ID.
     *
     * @param int $id
     * @return Blog|null
     */
    public function findById(int $id): ?Blog;

    /**
     * Find a blog by slug.
     *
     * @param string $slug
     * @return Blog|null
     */
    public function findBySlug(string $slug): ?Blog;

    /**
     * Check if slug exists.
     *
     * @param string $slug
     * @return bool
     */
    public function slugExists(string $slug): bool;

    /**
     * Get all blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Get paginated blogs.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc');

    /**
     * Get blogs by category.
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategory(int $categoryId);

    /**
     * Get hot news blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHotNews();

    /**
     * Get active blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive();

    /**
     * Update a blog.
     *
     * @param int $id
     * @param array $data
     * @return Blog|null
     */
    public function update(int $id, array $data): ?Blog;

    /**
     * Delete a blog.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get blogs with search, sort, and pagination.
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWithFilters(array $filters);

    /**
     * Search blogs by title or slug.
     *
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchByTitle(string $query);

    /**
     * Get all blogs sorted.
     *
     * @param string $sortBy
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSorted(string $sortBy = 'created_at', string $order = 'desc');
}
