<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest\StoreCategoryBlogRequest;
use App\Http\Requests\BlogRequest\UpdateCategoryBlogRequest;
use App\Http\Resources\BlogResource\CategoryBlogResource;
use App\Interfaces\CategoryBlogRepositoryInterface;
use App\Models\CategoryBlog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryBlogController extends Controller
{
    /**
     * @var CategoryBlogRepositoryInterface
     */
    protected CategoryBlogRepositoryInterface $categoryBlogRepository;

    /**
     * CategoryBlogController constructor.
     *
     * @param CategoryBlogRepositoryInterface $categoryBlogRepository
     */
    public function __construct(CategoryBlogRepositoryInterface $categoryBlogRepository)
    {
        $this->categoryBlogRepository = $categoryBlogRepository;
    }

    /**
     * Display a listing of category blogs.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = CategoryBlog::with('blogs')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => CategoryBlogResource::collection($categories),
            ],
        ], 200);
    }

    /**
     * Get active category blogs.
     *
     * @return JsonResponse
     */
    public function getActive(): JsonResponse
    {
        $categories = $this->categoryBlogRepository->getActive();

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => CategoryBlogResource::collection($categories),
            ],
        ], 200);
    }

    /**
     * Store a newly created category blog.
     *
     * @param StoreCategoryBlogRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryBlogRequest $request): JsonResponse
    {
        $category = $this->categoryBlogRepository->create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => [
                'category' => new CategoryBlogResource($category),
            ],
        ], 201);
    }

    /**
     * Display the specified category blog.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryBlogRepository->findById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryBlogResource($category),
            ],
        ], 200);
    }

    /**
     * Display the specified category blog by slug.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $category = $this->categoryBlogRepository->findBySlug($slug);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'category' => new CategoryBlogResource($category),
            ],
        ], 200);
    }

    /**
     * Update the specified category blog.
     *
     * @param UpdateCategoryBlogRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryBlogRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryBlogRepository->findById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $data = [];
        if ($request->has('name')) {
            $data['name'] = $request->name;
        }
        if ($request->has('slug')) {
            $data['slug'] = $request->slug;
        }
        if ($request->has('description')) {
            $data['description'] = $request->description;
        }
        if ($request->has('status')) {
            $data['status'] = $request->status;
        }

        $updatedCategory = $this->categoryBlogRepository->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => [
                'category' => new CategoryBlogResource($updatedCategory),
            ],
        ], 200);
    }

    /**
     * Remove the specified category blog.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $category = $this->categoryBlogRepository->findById($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $this->categoryBlogRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
