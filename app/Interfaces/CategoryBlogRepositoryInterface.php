<?php

namespace App\Interfaces;

use App\Models\CategoryBlog;

interface CategoryBlogRepositoryInterface
{
    /**
     * Create a new category blog.
     *
     * @param array $data
     * @return CategoryBlog
     */
    public function create(array $data): CategoryBlog;

    /**
     * Find a category blog by ID.
     *
     * @param int $id
     * @return CategoryBlog|null
     */
    public function findById(int $id): ?CategoryBlog;

    /**
     * Find a category blog by slug.
     *
     * @param string $slug
     * @return CategoryBlog|null
     */
    public function findBySlug(string $slug): ?CategoryBlog;

    /**
     * Check if slug exists.
     *
     * @param string $slug
     * @return bool
     */
    public function slugExists(string $slug): bool;

    /**
     * Get all category blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Get active category blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive();

    /**
     * Update a category blog.
     *
     * @param int $id
     * @param array $data
     * @return CategoryBlog|null
     */
    public function update(int $id, array $data): ?CategoryBlog;

    /**
     * Delete a category blog.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
