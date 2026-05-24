<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest\StoreBlogRequest;
use App\Http\Requests\BlogRequest\UpdateBlogRequest;
use App\Http\Resources\BlogResource\BlogResource;
use App\Models\Blog;
use App\Interfaces\BlogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * @var BlogRepositoryInterface
     */
    protected BlogRepositoryInterface $blogRepository;

    /**
     * BlogController constructor.
     *
     * @param BlogRepositoryInterface $blogRepository
     */
    public function __construct(BlogRepositoryInterface $blogRepository)
    {
        $this->blogRepository = $blogRepository;
    }

    /**
     * Get all blogs without pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $blogs = $this->blogRepository->all();

        return response()->json([
            'success' => true,
            'data' => [
                'blogs' => BlogResource::collection($blogs),
            ],
        ], 200);
    }

    /**
     * Display a listing of blogs (paginated).
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

        // Apply filters if provided
        $query = Blog::query();

        if ($request->category) {
            $query->where('fk_category', $request->category);
        }
        
        if ($request->search) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw("LOWER(title) LIKE ?", ["%$search%"])
                  ->orWhereRaw("LOWER(short_desc) LIKE ?", ["%$search%"])
                  ->orWhereRaw("LOWER(slug) LIKE ?", ["%$search%"]);
            });
        }

        if ($request->status) {
            $query->where('status', $request->status === 'published' ? 1 : 0);
        }

        // Validate sortBy to prevent SQL injection
        $allowedSortColumns = ['id', 'title', 'slug', 'created_at', 'updated_at'];
        $sortBy = $sortBy && in_array($sortBy, $allowedSortColumns) ? $sortBy : 'created_at';
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
        
        $query->orderBy($sortBy, $sortDirection);
        
        $blogs = $query->paginate((int) $perPage, ['*'], 'page', (int) $page);

        return response()->json([
            'success' => true,
            'data' => [
                'blogs' => BlogResource::collection($blogs->items()),
                'pagination' => [
                    'current_page' => $blogs->currentPage(),
                    'per_page' => $blogs->perPage(),
                    'total' => $blogs->total(),
                    'last_page' => $blogs->lastPage(),
                    'from' => $blogs->firstItem(),
                    'to' => $blogs->lastItem(),
                ],
                'sort' => [
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                ],
            ],
        ], 200);
    }

    /**
     * Get active blogs.
     *
     * @return JsonResponse
     */
    public function getActive(): JsonResponse
    {
        $blogs = $this->blogRepository->getActive();

        return response()->json([
            'success' => true,
            'data' => [
                'blogs' => BlogResource::collection($blogs),
            ],
        ], 200);
    }

    /**
     * Get hot news blogs.
     *
     * @return JsonResponse
     */
    public function getHotNews(): JsonResponse
    {
        $blogs = $this->blogRepository->getHotNews();

        return response()->json([
            'success' => true,
            'data' => [
                'blogs' => BlogResource::collection($blogs),
            ],
        ], 200);
    }

    /**
     * Store a newly created blog.
     *
     * @param StoreBlogRequest $request
     * @return JsonResponse
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $coverPath = null;

        // Handle file upload
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = 'blog-cover-' . time() . '.' . $file->getClientOriginalExtension();
            $coverPath = Storage::disk('public')->putFileAs('blog-covers', $file, $filename);
        }

        $blog = $this->blogRepository->create([
            'cover' => $coverPath,
            'title' => $request->title,
            'short_desc' => $request->short_desc,
            'long_desc' => $request->long_desc,
            'fk_category' => $request->fk_category,
            'slug' => $request->slug,
            'status' => $request->status ?? true,
            'hot_news' => $request->hot_news ?? false,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully',
            'data' => [
                'blog' => new BlogResource($blog),
            ],
        ], 201);
    }

    /**
     * Display the specified blog.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $blog = $this->blogRepository->findById($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'blog' => new BlogResource($blog),
            ],
        ], 200);
    }

    /**
     * Display the specified blog by slug.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $blog = $this->blogRepository->findBySlug($slug);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'blog' => new BlogResource($blog),
            ],
        ], 200);
    }

    /**
     * Get blogs with filters, sorting, and pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function filter(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search'),
            'category' => $request->get('category'),
            'status' => $request->get('status'),
            'hot_news' => $request->get('hot_news'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'order' => strtolower($request->get('order', 'desc')),
            'per_page' => intval($request->get('per_page', 10)),
            'page' => intval($request->get('page', 1)),
        ];

        $blogs = $this->blogRepository->getWithFilters($filters);

        return response()->json([
            'success' => true,
            'data' => [
                'blogs' => BlogResource::collection($blogs->items()),
                'pagination' => [
                    'total' => $blogs->total(),
                    'per_page' => $blogs->perPage(),
                    'current_page' => $blogs->currentPage(),
                    'last_page' => $blogs->lastPage(),
                    'from' => $blogs->firstItem(),
                    'to' => $blogs->lastItem(),
                    'has_more' => $blogs->hasMorePages(),
                ],
            ],
        ], 200);
    }

    /**
     * Update the specified blog.
     *
     * @param UpdateBlogRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $blog = $this->blogRepository->findById($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        $data = [];

        // Handle file upload and delete old file
        if ($request->hasFile('cover')) {
            // Delete old cover if exists
            if ($blog->cover && Storage::disk('public')->exists($blog->cover)) {
                Storage::disk('public')->delete($blog->cover);
            }
            // Upload new cover
            $file = $request->file('cover');
            $filename = 'blog-cover-' . time() . '.' . $file->getClientOriginalExtension();
            $coverPath = Storage::disk('public')->putFileAs('blog-covers', $file, $filename);
            $data['cover'] = $coverPath;
        }

        if ($request->has('title')) {
            $data['title'] = $request->title;
        }
        if ($request->has('short_desc')) {
            $data['short_desc'] = $request->short_desc;
        }
        if ($request->has('long_desc')) {
            $data['long_desc'] = $request->long_desc;
        }
        if ($request->has('fk_category')) {
            $data['fk_category'] = $request->fk_category;
        }
        if ($request->has('slug')) {
            $data['slug'] = $request->slug;
        }
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }
        if ($request->has('hot_news')) {
            $data['hot_news'] = $request->hot_news;
        }
        if ($request->has('meta_title')) {
            $data['meta_title'] = $request->meta_title;
        }
        if ($request->has('meta_description')) {
            $data['meta_description'] = $request->meta_description;
        }

        $updatedBlog = $this->blogRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully',
            'data' => [
                'blog' => new BlogResource($updatedBlog),
            ],
        ], 200);
    }

    /**
     * Remove the specified blog.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $blog = $this->blogRepository->findById($id);

        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found',
            ], 404);
        }

        $this->blogRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully',
        ], 200);
    }
}
