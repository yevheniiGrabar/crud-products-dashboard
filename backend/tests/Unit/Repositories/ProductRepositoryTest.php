<?php

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ProductRepository();
    }

    /** @test */
    public function it_can_get_all_products_with_pagination()
    {
        // Arrange
        Product::factory()->count(15)->create();

        // Act
        $result = $this->repository->getAllPaginated(10);

        // Assert
        $this->assertEquals(15, $result->total());
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(2, $result->lastPage());
        $this->assertCount(10, $result->items());
    }

    /** @test */
    public function it_can_find_product_by_id()
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $result = $this->repository->findById($product->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($product->id, $result->id);
        $this->assertEquals($product->name, $result->name);
    }

    /** @test */
    public function it_returns_null_for_nonexistent_product()
    {
        // Act
        $result = $this->repository->findById(999);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_get_latest_products()
    {
        // Arrange
        $oldProduct = Product::factory()->create(['created_at' => now()->subDays(5)]);
        $newProduct = Product::factory()->create(['created_at' => now()]);

        // Act
        $result = $this->repository->getLatest(2);

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals($newProduct->id, $result->first()->id);
        $this->assertEquals($oldProduct->id, $result->last()->id);
    }

    /** @test */
    public function it_can_create_product()
    {
        // Arrange
        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 10,
        ];

        // Act
        $result = $this->repository->create($productData);

        // Assert
        $this->assertInstanceOf(Product::class, $result);
        $this->assertEquals('Test Product', $result->name);
        $this->assertEquals('TEST-001', $result->sku);
        $this->assertEquals(99.99, $result->price);
        $this->assertEquals(10, $result->quantity);
        $this->assertDatabaseHas('products', $productData);
    }

    /** @test */
    public function it_can_update_product()
    {
        // Arrange
        $product = Product::factory()->create();
        $updateData = [
            'name' => 'Updated Product',
            'price' => 149.99,
        ];

        // Act
        $result = $this->repository->update($product->id, $updateData);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals('Updated Product', $result->name);
        $this->assertEquals(149.99, $result->price);
        $this->assertEquals($product->sku, $result->sku); // Should remain unchanged
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 149.99,
        ]);
    }

    /** @test */
    public function it_returns_null_when_updating_nonexistent_product()
    {
        // Arrange
        $updateData = ['name' => 'Updated Product'];

        // Act
        $result = $this->repository->update(999, $updateData);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_delete_product()
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $result = $this->repository->delete($product->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_product()
    {
        // Act
        $result = $this->repository->delete(999);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_get_product_statistics()
    {
        // Arrange
        Product::factory()->create([
            'price' => 100,
            'quantity' => 5,
        ]);
        Product::factory()->create([
            'price' => 200,
            'quantity' => 10,
        ]);
        Product::factory()->create([
            'price' => 300,
            'quantity' => 3,
        ]);

        // Act
        $result = $this->repository->getStats();

        // Assert
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('total_value', $result);
        $this->assertArrayHasKey('average_price', $result);
        $this->assertArrayHasKey('low_stock_count', $result);
        $this->assertIsInt($result['total']);
        $this->assertIsNumeric($result['total_value']);
        $this->assertIsNumeric($result['average_price']);
        $this->assertIsInt($result['low_stock_count']);
    }

    /** @test */
    public function it_can_check_if_product_exists()
    {
        // Arrange
        $product = Product::factory()->create();

        // Act & Assert
        $this->assertTrue($this->repository->exists($product->id));
        $this->assertFalse($this->repository->exists(999));
    }
}
