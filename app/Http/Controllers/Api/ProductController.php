<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products
     *
     * Supports optional query params:
     *   ?search=nama   – filter by product name
     *   ?per_page=15   – items per page (default 15)
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->when($request->search, fn ($q, $search) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * GET /api/products/{id}
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new ProductResource($product),
        ]);
    }
}
