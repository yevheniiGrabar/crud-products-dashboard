<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $service;
    protected $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->mockRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->service = new ProductService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_all_products_with_pagination()
    {
        // Arrange
        $products = Product::factory()->count(5)->create();
        $paginator = Product::orderBy('created_at', 'desc')->paginate(10);
        $this->mockRepository->shouldReceive('getAllPaginated')
            ->with(10)
            ->once()
            ->andReturn($paginator);

        // Act
        $result = $this->service->getAllProducts(10);

        // Assert
        $this->assertEquals($paginator, $result);
    }

    /** @test */
    public function it_can_get_product_by_id()
    {
        // Arrange
        $product = Product::factory()->create();
        $this->mockRepository->shouldReceive('findById')
            ->with($product->id)
            ->once()
            ->andReturn($product);

        // Act
        $result = $this->service->getProductById($product->id);

        // Assert
        $this->assertEquals($product, $result);
    }

    /** @test */
    public function it_can_get_latest_products()
    {
        // Arrange
        $products = Product::factory()->count(3)->create();
        $this->mockRepository->shouldReceive('getLatest')
            ->with(3)
            ->once()
            ->andReturn($products);

        // Act
        $result = $this->service->getLatestProducts(3);

        // Assert
        $this->assertEquals($products, $result);
    }

    /** @test */
    public function it_can_create_product_with_valid_data()
    {
        // Arrange
        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 10,
        ];

        $product = Product::factory()->make($productData);
        $this->mockRepository->shouldReceive('create')
            ->with($productData)
            ->once()
            ->andReturn($product);

        // Act
        $result = $this->service->createProduct($productData);

        // Assert
        $this->assertEquals($product, $result);
    }

    /** @test */
    public function it_throws_validation_exception_for_invalid_data()
    {
        // Arrange
        $invalidData = [
            'name' => '', // Required field is empty
            'sku' => 'TEST-001',
            'price' => -10, // Negative price
            'quantity' => 'invalid', // Not integer
        ];

        // Act & Assert
        $this->expectException(ValidationException::class);
        $this->service->createProduct($invalidData);
    }

    /** @test */
    public function it_can_update_product_with_valid_data()
    {
        // Arrange
        $product = Product::factory()->create();
        $updateData = [
            'name' => 'Updated Product',
            'price' => 149.99,
        ];

        $this->mockRepository->shouldReceive('exists')
            ->with($product->id)
            ->once()
            ->andReturn(true);

        $this->mockRepository->shouldReceive('update')
            ->with($product->id, $updateData)
            ->once()
            ->andReturn($product->fresh());

        // Act
        $result = $this->service->updateProduct($product->id, $updateData);

        // Assert
        $this->assertNotNull($result);
    }

    /** @test */
    public function it_returns_null_when_updating_nonexistent_product()
    {
        // Arrange
        $updateData = ['name' => 'Updated Product'];

        $this->mockRepository->shouldReceive('exists')
            ->with(999)
            ->once()
            ->andReturn(false);

        // Act
        $result = $this->service->updateProduct(999, $updateData);

        // Assert
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_delete_product()
    {
        // Arrange
        $product = Product::factory()->create();

        $this->mockRepository->shouldReceive('findById')
            ->with($product->id)
            ->once()
            ->andReturn($product);

        $this->mockRepository->shouldReceive('delete')
            ->with($product->id)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->service->deleteProduct($product->id);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_product()
    {
        // Arrange
        $this->mockRepository->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        // Act
        $result = $this->service->deleteProduct(999);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_get_product_statistics()
    {
        // Arrange
        $stats = [
            'total' => 5,
            'total_value' => 1000,
            'average_price' => 200,
            'low_stock_count' => 2,
        ];

        $this->mockRepository->shouldReceive('getStats')
            ->once()
            ->andReturn($stats);

        // Act
        $result = $this->service->getProductStats();

        // Assert
        $this->assertEquals($stats, $result);
    }

    /** @test */
    public function it_can_handle_image_upload_during_creation()
    {
        // Arrange
        $file = UploadedFile::fake()->image('product.jpg');
        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 10,
            'image' => $file,
        ];

        $product = Product::factory()->make([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 10,
            'image' => 'products/test.jpg',
        ]);

        $this->mockRepository->shouldReceive('create')
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'Test Product' &&
                       $data['sku'] === 'TEST-001' &&
                       $data['price'] === 99.99 &&
                       $data['quantity'] === 10 &&
                       str_contains($data['image'], 'products/');
            }))
            ->once()
            ->andReturn($product);

        // Act
        $result = $this->service->createProduct($productData);

        // Assert
        $this->assertEquals($product, $result);
    }

    /** @test */
    public function it_can_handle_image_upload_during_update()
    {
        // Arrange
        $product = Product::factory()->create(['image' => 'products/old.jpg']);
        $file = UploadedFile::fake()->image('new-product.jpg');
        $updateData = [
            'name' => 'Updated Product',
            'image' => $file,
        ];

        $this->mockRepository->shouldReceive('exists')
            ->with($product->id)
            ->once()
            ->andReturn(true);

        $this->mockRepository->shouldReceive('findById')
            ->with($product->id)
            ->once()
            ->andReturn($product);

        $updatedProduct = Product::factory()->make([
            'id' => $product->id,
            'name' => 'Updated Product',
            'image' => 'products/new.jpg',
        ]);

        $this->mockRepository->shouldReceive('update')
            ->with($product->id, Mockery::on(function ($data) {
                return $data['name'] === 'Updated Product' &&
                       str_contains($data['image'], 'products/');
            }))
            ->once()
            ->andReturn($updatedProduct);

        // Act
        $result = $this->service->updateProduct($product->id, $updateData);

        // Assert
        $this->assertEquals($updatedProduct, $result);
    }
}
