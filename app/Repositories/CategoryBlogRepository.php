<?php

namespace App\Repositories;

use App\Models\CategoryBlog;
use App\Interfaces\CategoryBlogRepositoryInterface;

class CategoryBlogRepository implements CategoryBlogRepositoryInterface
{
    /**
     * Create a new category blog.
     *
     * @param array $data
     * @return CategoryBlog
     */
    public function create(array $data): CategoryBlog
    {
        return CategoryBlog::create($data);
    }

    /**
     * Find a category blog by ID.
     *
     * @param int $id
     * @return CategoryBlog|null
     */
    public function findById(int $id): ?CategoryBlog
    {
        return CategoryBlog::find($id);
    }

    /**
     * Find a category blog by slug.
     *
     * @param string $slug
     * @return CategoryBlog|null
     */
    public function findBySlug(string $slug): ?CategoryBlog
    {
        return CategoryBlog::where('slug', $slug)->first();
    }

    /**
     * Check if slug exists.
     *
     * @param string $slug
     * @return bool
     */
    public function slugExists(string $slug): bool
    {
        return CategoryBlog::where('slug', $slug)->exists();
    }

    /**
     * Get all category blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return CategoryBlog::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get active category blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return CategoryBlog::where('status', true)
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    /**
     * Update a category blog.
     *
     * @param int $id
     * @param array $data
     * @return CategoryBlog|null
     */
    public function update(int $id, array $data): ?CategoryBlog
    {
        $category = $this->findById($id);

        if (!$category) {
            return null;
        }

        $category->update($data);

        return $category->fresh();
    }

    /**
     * Delete a category blog.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $category = $this->findById($id);

        if (!$category) {
            return false;
        }

        return $category->delete();
    }
}
