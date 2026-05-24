<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductReviewRequest\StoreProductReviewRequest;
use App\Http\Resources\ProductReviewResource\ProductReviewResource;
use App\Interfaces\ProductReviewRepositoryInterface;
use App\Models\OrderItem;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class ProductReviewController extends Controller
{
    protected $reviewRepository;

    public function __construct(ProductReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function index($productId): JsonResponse
    {
        $reviews = $this->reviewRepository->getAllForProduct($productId);

        return response()->json([
            'success' => true,
            'data'    => ProductReviewResource::collection($reviews),
        ]);
    }

    public function store(StoreProductReviewRequest $request, $productId): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validated();

        $orderItem = OrderItem::findOrFail($validated['order_item_id']);
        $productId = intval($productId);

        if ($orderItem->fk_product_id != $productId) {
            return response()->json([
                'success' => false,
                'message' => 'Produk id tidak cocok'
            ]);
        }

        if ($orderItem->order->fk_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bukan pesanan Anda'
            ], 403);
        }

        if ($orderItem->order->status !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan belum selesai'
            ], 403);
        }

        if (ProductReview::where('order_item_id', $orderItem->id)
            ->where('user_id', $user->id)
            ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Item ini sudah Anda ulas'
            ], 422);
        }

        $review = ProductReview::create([
            'fk_product_id'  => $orderItem->fk_product_id,
            'user_id'        => $user->id,
            'rating'         => $validated['rating'],
            'comment'        => $validated['comment'] ?? null,
            'review_date'    => now(),
            'is_approved'    => true,
            'order_item_id'  => $orderItem->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dikirim',
            'data'    => new ProductReviewResource($review),
        ], 201);
    }

    public function update(Request $request, $reviewId): JsonResponse
    {
        $user = Auth::user();

        $review = ProductReview::findOrFail($reviewId);

        if ($review->user_id !== $user->id) {
            return response()->json(['message' => 'Bukan ulasan Anda'], 403);
        }

        if (now()->greaterThan($review->created_at->addHour())) {
            return response()->json([
                'message' => 'Waktu edit sudah lewat (maks 1 jam)'
            ], 403);
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review->update([
            'rating'  => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil diperbarui',
            'created_at' => $review->created_at,
            'data'    => new ProductReviewResource($review->fresh()),
        ]);
    }

    public function myReviews(): JsonResponse
    {
        $reviews = $this->reviewRepository->getAllByUser(Auth::id());

        return response()->json([
            'success' => true,
            'data'    => ProductReviewResource::collection($reviews),
        ]);
    }
}
