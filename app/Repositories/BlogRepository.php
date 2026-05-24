<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Interfaces\BlogRepositoryInterface;

class BlogRepository implements BlogRepositoryInterface
{
    /**
     * Create a new blog.
     *
     * @param array $data
     * @return Blog
     */
    public function create(array $data): Blog
    {
        return Blog::create($data);
    }

    /**
     * Find a blog by ID.
     *
     * @param int $id
     * @return Blog|null
     */
    public function findById(int $id): ?Blog
    {
        return Blog::find($id);
    }

    /**
     * Find a blog by slug.
     *
     * @param string $slug
     * @return Blog|null
     */
    public function findBySlug(string $slug): ?Blog
    {
        return Blog::where('slug', $slug)->first();
    }

    /**
     * Check if slug exists.
     *
     * @param string $slug
     * @return bool
     */
    public function slugExists(string $slug): bool
    {
        return Blog::where('slug', $slug)->exists();
    }

    /**
     * Get all blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Blog::orderBy('created_at', 'desc')->get();
    }

    /**
     * Get paginated blogs.
     *
     * @param int $perPage
     * @param string|null $sortBy
     * @param string $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, ?string $sortBy = null, string $sortDirection = 'desc')
    {
        $query = Blog::query();
        
        // Validate sortBy to prevent SQL injection
        $allowedSortColumns = ['id', 'title', 'slug', 'created_at', 'updated_at'];
        $sortBy = $sortBy && in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
        
        $query->orderBy($sortBy, $sortDirection);
        
        return $query->paginate($perPage);
    }

    /**
     * Get blogs by category.
     *
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategory(int $categoryId)
    {
        return Blog::where('fk_category', $categoryId)
                  ->orderBy('created_at', 'desc')
                  ->get();
    }

    /**
     * Get hot news blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHotNews()
    {
        return Blog::where('hot_news', true)
                  ->where('status', true)
                  ->orderBy('created_at', 'desc')
                  ->get();
    }

    /**
     * Get active blogs.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return Blog::where('status', true)
                  ->orderBy('created_at', 'desc')
                  ->get();
    }

    /**
     * Update a blog.
     *
     * @param int $id
     * @param array $data
     * @return Blog|null
     */
    public function update(int $id, array $data): ?Blog
    {
        $blog = $this->findById($id);

        if (!$blog) {
            return null;
        }

        $blog->update($data);

        return $blog->fresh();
    }

    /**
     * Delete a blog.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $blog = $this->findById($id);

        if (!$blog) {
            return false;
        }

        return $blog->delete();
    }

    /**
     * Get blogs with search, sort, and pagination.
     *
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWithFilters(array $filters)
    {
        $query = Blog::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('short_desc', 'like', "%{$search}%");
        }

        // Category filter
        if (!empty($filters['category'])) {
            $query->where('fk_category', $filters['category']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Hot news filter
        if (isset($filters['hot_news'])) {
            $query->where('hot_news', $filters['hot_news']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $order = $filters['order'] ?? 'desc';
        $query->orderBy($sortBy, $order);

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Search blogs by title or slug.
     *
     * @param string $query
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchByTitle(string $query)
    {
        $q = strtolower($query);

        return Blog::whereRaw('LOWER(slug) LIKE ?', ["%{$q}%"])
            ->orWhereRaw('LOWER(title) LIKE ?', ["%{$q}%"])
            ->orWhereRaw('LOWER(short_desc) LIKE ?', ["%{$q}%"])
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get all blogs sorted.
     *
     * @param string $sortBy
     * @param string $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSorted(string $sortBy = 'created_at', string $order = 'desc')
    {
        return Blog::orderBy($sortBy, $order)->get();
    }
}
