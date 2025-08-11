<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StatsResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get all products with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {

            $products = $this->productService->getAllProducts($request->get('per_page'));

            return response()->json(new ProductCollection($products));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading products: ' . $e->getMessage()
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

            return response()->json([
                'data' => new StatsResource($stats)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product by ID
     */
    public function show($id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'data' => new ProductResource($product)
        ]);
    }

    /**
     * Create a new product
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->all());

            return response()->json([
                'message' => 'Product successfully created',
                'data' => new ProductResource($product)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($id, $request->all());

            if (!$product) {
                return response()->json([
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Product successfully updated',
                'data' => new ProductResource($product)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete product
     */
    public function destroy($id): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Product successfully deleted'
        ]);
    }
}
