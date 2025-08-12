<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $mockRepository;
    protected $productService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->mockRepository = Mockery::mock(ProductRepositoryInterface::class);
        $this->productService = new ProductService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_all_products_with_pagination()
    {
        $products = Product::factory()->count(5)->create();
        $paginator = Product::orderBy('created_at', 'desc')->paginate(10);

        $this->mockRepository->shouldReceive('getAllPaginated')
            ->with(10)
            ->once()
            ->andReturn($paginator);

        $result = $this->productService->getAllProducts(10);

        $this->assertEquals($paginator, $result);
    }

    /** @test */
    public function it_can_get_product_by_id()
    {
        $product = Product::factory()->create();

        $this->mockRepository->shouldReceive('findById')
            ->with($product->id)
            ->once()
            ->andReturn($product);

        $result = $this->productService->getProductById($product->id);

        $this->assertEquals($product, $result);
    }

    /** @test */
    public function it_can_get_latest_products()
    {
        $products = Product::factory()->count(3)->create();

        $this->mockRepository->shouldReceive('getLatest')
            ->with(3)
            ->once()
            ->andReturn($products);

        $result = $this->productService->getLatestProducts(3);

        $this->assertEquals($products, $result);
    }

    /** @test */
    public function it_can_create_product_with_valid_data()
    {
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

        $result = $this->productService->createProduct($productData);

        $this->assertEquals($product, $result);
    }

    /** @test */
    public function it_can_update_product_with_valid_data()
    {
        $product = Product::factory()->create();
        $updateData = [
            'name' => 'Updated Product',
            'price' => 149.99,
        ];

        $updatedProduct = Product::factory()->make([
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 149.99,
        ]);

        $this->mockRepository->shouldReceive('update')
            ->with($product->id, $updateData)
            ->once()
            ->andReturn($updatedProduct);

        $result = $this->productService->updateProduct($product->id, $updateData);

        $this->assertEquals($updatedProduct, $result);
    }

    /** @test */
    public function it_returns_null_when_updating_nonexistent_product()
    {
        $updateData = ['name' => 'Updated Product'];

        $this->mockRepository->shouldReceive('update')
            ->with(999, $updateData)
            ->once()
            ->andReturn(null);

        $result = $this->productService->updateProduct(999, $updateData);

        $this->assertNull($result);
    }

    /** @test */
    public function it_can_delete_product()
    {
        $product = Product::factory()->create();

        $this->mockRepository->shouldReceive('findById')
            ->with($product->id)
            ->once()
            ->andReturn($product);

        $this->mockRepository->shouldReceive('delete')
            ->with($product->id)
            ->once()
            ->andReturn(true);

        $result = $this->productService->deleteProduct($product->id);

        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_when_deleting_nonexistent_product()
    {
        $this->mockRepository->shouldReceive('findById')
            ->with(999)
            ->once()
            ->andReturn(null);

        $result = $this->productService->deleteProduct(999);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_get_product_statistics()
    {
        $stats = [
            'total' => 5,
            'total_value' => 1000.00,
            'average_price' => 200.00,
            'low_stock_count' => 2,
        ];

        $this->mockRepository->shouldReceive('getStats')
            ->once()
            ->andReturn($stats);

        $result = $this->productService->getProductStats();

        $this->assertEquals($stats, $result);
    }

    /** @test */
    public function it_can_handle_image_upload_during_creation()
    {
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
                       is_string($data['image']);
            }))
            ->once()
            ->andReturn($product);

        $result = $this->productService->createProduct($productData);

        $this->assertEquals($product, $result);
    }

    /** @test */
    public function it_can_handle_image_upload_during_update()
    {
        $product = Product::factory()->create(['image' => 'old-image.jpg']);
        $file = UploadedFile::fake()->image('new-product.jpg');

        $updateData = [
            'name' => 'Updated Product',
            'image' => $file,
        ];

        $updatedProduct = Product::factory()->make([
            'id' => $product->id,
            'name' => 'Updated Product',
            'image' => 'products/new-image.jpg',
        ]);

        $this->mockRepository->shouldReceive('findById')
            ->with($product->id)
            ->once()
            ->andReturn($product);

        $this->mockRepository->shouldReceive('update')
            ->with($product->id, Mockery::on(function ($data) {
                return $data['name'] === 'Updated Product' &&
                       is_string($data['image']);
            }))
            ->once()
            ->andReturn($updatedProduct);

        $result = $this->productService->updateProduct($product->id, $updateData);

        $this->assertEquals($updatedProduct, $result);
    }
}
