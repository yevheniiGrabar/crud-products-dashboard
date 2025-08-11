<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    /**
     * Get all products with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;

    /**
     * Get product by ID
     *
     * @param int $id
     * @return Product|null
     */
    public function findById(int $id): ?Product;

    /**
     * Get latest products
     *
     * @param int $limit
     * @return Collection
     */
    public function getLatest(int $limit = 3): Collection;

    /**
     * Create a new product
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product;

    /**
     * Update product
     *
     * @param int $id
     * @param array $data
     * @return Product|null
     */
    public function update(int $id, array $data): ?Product;

    /**
     * Delete product
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get product statistics
     *
     * @return array
     */
    public function getStats(): array;

    /**
     * Check if product exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool;
}
