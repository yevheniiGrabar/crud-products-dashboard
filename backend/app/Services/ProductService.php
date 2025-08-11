<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductService
{
    protected const DEFAULT_PER_PAGE = 10;
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get all products with pagination
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllProducts($perPage)
    {
        return $this->productRepository->getAllPaginated($perPage ?? self::DEFAULT_PER_PAGE);
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
     * @throws ValidationException
     */
    public function createProduct(array $data)
    {
        // Filter out empty values and null values
        $createData = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        // Validation of data
        $validator = Validator::make($createData, Product::$rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Image processing
        if (isset($createData['image']) && $createData['image'] instanceof UploadedFile) {
            $createData['image'] = $this->uploadImage($createData['image']);
        }

        // Creating a product
        return $this->productRepository->create($createData);
    }

    /**
     * Update product
     *
     * @param int $id
     * @param array $data
     * @return Product|null
     * @throws ValidationException
     */
    public function updateProduct($id, array $data)
    {
        // Check if product exists
        if (!$this->productRepository->exists($id)) {
            return null;
        }

        // Filter out empty values and null values, but keep 0 values
        $updateData = array_filter($data, function($value, $key) {
            // Keep numeric values (including 0) and non-empty strings
            if (is_numeric($value)) {
                return true;
            }
            if (is_string($value)) {
                return trim($value) !== '';
            }
            return $value !== null;
        }, ARRAY_FILTER_USE_BOTH);

        // Validation of data for updating
        $validator = Validator::make($updateData, Product::getUpdateRules($id));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Image processing
        if (isset($updateData['image']) && $updateData['image'] instanceof UploadedFile) {
            // Get current product to delete old image
            $currentProduct = $this->productRepository->findById($id);
            if ($currentProduct && $currentProduct->image) {
                $this->deleteImage($currentProduct->image);
            }
            $updateData['image'] = $this->uploadImage($updateData['image']);
        }

        // Updating a product
        return $this->productRepository->update($id, $updateData);
    }

    /**
     * Delete product
     *
     * @param int $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        // Get product to delete image
        $product = $this->productRepository->findById($id);

        if (!$product) {
            return false;
        }

        // Delete image
        if ($product->image) {
            $this->deleteImage($product->image);
        }

        return $this->productRepository->delete($id);
    }

    /**
     * Upload image
     *
     * @param UploadedFile $file
     * @return string
     */
    private function uploadImage(UploadedFile $file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('products', $fileName, 'public');

        return $path;
    }

    /**
     * Delete image
     *
     * @param string $path
     * @return bool
     */
    private function deleteImage($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }

    /**
     * Get product statistics
     *
     * @return array
     */
    public function getProductStats()
    {
        return $this->productRepository->getStats();
    }
}
