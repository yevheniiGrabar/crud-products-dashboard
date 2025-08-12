<?php

namespace App\Services;

use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * Get all products with pagination
     */
    public function getAllProducts(int $perPage = 10)
    {
        return $this->productRepository->getAllPaginated($perPage);
    }

    /**
     * Get product by ID
     *
     * @param int $id
     * @return Product|null
     */
    public function getProductById($id)
    {
        return $this->productRepository->findById($id);
    }

    /**
     * Get latest products
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatestProducts($limit = 3)
    {
        return $this->productRepository->getLatest($limit);
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data)
    {
        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->productRepository->create($data);
    }

    /**
     * Update product
     *
     * @param int $id
     * @param array $data
     * @return Product|null
     */
    public function updateProduct(int $id, array $data)
    {
        // Handle image upload if present
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image if exists
            $existingProduct = $this->productRepository->findById($id);
            if ($existingProduct && $existingProduct->image) {
                $this->deleteImage($existingProduct->image);
            }

            $data['image'] = $this->uploadImage($data['image']);
        }

        // Filter out null values but keep numeric 0 values
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        return $this->productRepository->update($id, $data);
    }

    /**
     * Delete product
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return false;
        }

        // Delete image if exists
        if ($product->image) {
            $this->deleteImage($product->image);
        }

        return $this->productRepository->delete($id);
    }

    /**
     * Get product statistics
     */
    public function getProductStats(): array
    {
        return $this->productRepository->getStats();
    }

    /**
     * Upload image to storage
     */
    private function uploadImage(UploadedFile $file): string
    {
        $path = $file->store('products', 'public');
        return $path;
    }

    /**
     * Delete image from storage
     */
    private function deleteImage(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
