<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepository implements ProductRepositoryInterface
{
    /**
     * Get all products with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Product::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get product by ID
     *
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    /**
     * Get latest products
     *
     * @param int $limit
     * @return Collection
     */
    public function getLatest(int $limit = 3): Collection
    {
        return Product::orderBy('created_at', 'desc')->limit($limit)->get();
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update product
     *
     * @param int $id
     * @param array $data
     * @return Product|null
     */
    public function update(int $id, array $data): ?Product
    {
        $product = $this->findById($id);

        if (!$product) {
            return null;
        }

        $product->update($data);
        return $product->fresh();
    }

    /**
     * Delete product
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $product = $this->findById($id);

        if (!$product) {
            return false;
        }

        return $product->delete();
    }

    /**
     * Get product statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        $stats = DB::table('products')
            ->selectRaw('
                COUNT(*) as total,
                SUM(price * quantity) as total_value,
                AVG(price) as average_price,
                SUM(CASE WHEN quantity <= 5 THEN 1 ELSE 0 END) as low_stock_count
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'total_value' => $stats->total_value ?? 0,
            'average_price' => $stats->average_price ?? 0,
            'low_stock_count' => $stats->low_stock_count ?? 0,
        ];
    }

    /**
     * Check if product exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return Product::where('id', $id)->exists();
    }
}
