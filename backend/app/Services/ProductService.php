<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductService
{
    /**
     * Get all products with pagination
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllProducts($perPage = 10)
    {
        return Product::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get product by ID
     *
     * @param int $id
     * @return Product|null
     */
    public function getProductById($id)
    {
        return Product::find($id);
    }

    /**
     * Get latest products
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatestProducts($limit = 3)
    {
        return Product::orderBy('created_at', 'desc')->limit($limit)->get();
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
        // Validation of data
        $validator = Validator::make($data, Product::$rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Image processing
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        // Creating a product
        return Product::create($data);
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
        $product = Product::find($id);

        if (!$product) {
            return null;
        }

        // Validation of data for updating
        $validator = Validator::make($data, Product::getUpdateRules($id));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Image processing
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            // Delete old image
            if ($product->image) {
                $this->deleteImage($product->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        // Updating a product
        $product->update($data);

        return $product->fresh();
    }

    /**
    * Delete product
     *
     * @param int $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return false;
        }

        // Delete image
        if ($product->image) {
            $this->deleteImage($product->image);
        }

        return $product->delete();
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
        return [
            'total' => Product::count(),
            'total_value' => Product::sum(DB::raw('price * quantity')),
            'average_price' => Product::avg('price'),
            'low_stock' => Product::where('quantity', '<', 10)->count(),
        ];
    }
}
