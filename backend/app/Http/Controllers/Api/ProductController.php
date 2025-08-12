<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StatsResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $products = $this->productService->getAllProducts($perPage);

            return response()->json(new ProductCollection($products));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $product = $this->productService->createProduct($validated);

            return response()->json([
                'message' => 'Product successfully created',
                'data' => new ProductResource($product)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();
            $product = $this->productService->updateProduct($id, $validated);

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Product successfully updated',
                'data' => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->productService->deleteProduct($id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Product successfully deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest products
     */
    public function latest(): JsonResponse
    {
        try {
            $products = $this->productService->getLatestProducts();

            return response()->json([
                'data' => ProductResource::collection($products)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading latest products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->productService->getProductStats();

            return response()->json(new StatsResource($stats));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
