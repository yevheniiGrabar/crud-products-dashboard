<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $mockService;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->mockService = Mockery::mock(ProductService::class);
        $this->app->instance(ProductService::class, $this->mockService);
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
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $products = Product::factory()->count(5)->create();
        $paginator = Product::orderBy('created_at', 'desc')->paginate(10);

        $this->mockService->shouldReceive('getAllProducts')
            ->with(10)
            ->once()
            ->andReturn($paginator);

        // Act
        $response = $this->getJson('/api/products?per_page=10');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'sku', 'price', 'quantity', 'created_at', 'updated_at']
                ],
                'meta' => [
                    'current_page', 'last_page', 'per_page', 'total', 'from', 'to', 'has_more_pages'
                ],
                'links' => ['first', 'last', 'prev', 'next']
            ]);
    }

    /** @test */
    public function it_can_get_latest_products()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $products = Product::factory()->count(3)->create();

        $this->mockService->shouldReceive('getLatestProducts')
            ->once()
            ->andReturn($products);

        // Act
        $response = $this->getJson('/api/products/latest');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'sku', 'price', 'quantity', 'created_at', 'updated_at']
                ]
            ]);
    }

    /** @test */
    public function it_can_get_product_statistics()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $stats = [
            'total' => 5,
            'total_value' => 1000.00,
            'average_price' => 200.00,
            'low_stock_count' => 2,
        ];

        $this->mockService->shouldReceive('getProductStats')
            ->once()
            ->andReturn($stats);

        // Act
        $response = $this->getJson('/api/products/stats');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['total', 'total_value', 'average_price', 'low_stock_count']
            ])
            ->assertJson([
                'data' => $stats
            ]);
    }

    /** @test */
    public function it_can_get_product_by_id()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $this->mockService->shouldReceive('getProductById')
            ->with($product->id)
            ->once()
            ->andReturn($product);

        // Act
        $response = $this->getJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'sku', 'price', 'quantity', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_product()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->mockService->shouldReceive('getProductById')
            ->with(999)
            ->once()
            ->andReturn(null);

        // Act
        $response = $this->getJson('/api/products/999');

        // Assert
        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }

    /** @test */
    public function it_can_create_product()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $productData = [
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 10,
        ];

        $product = Product::factory()->make($productData);

        $this->mockService->shouldReceive('createProduct')
            ->with($productData)
            ->once()
            ->andReturn($product);

        // Act
        $response = $this->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'sku', 'price', 'quantity', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'message' => 'Product successfully created',
                'data' => [
                    'name' => 'Test Product',
                    'sku' => 'TEST-001',
                    'price' => 99.99,
                    'quantity' => 10,
                ]
            ]);
    }

    /** @test */
    public function it_can_create_product_with_image()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

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

        $this->mockService->shouldReceive('createProduct')
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'Test Product' &&
                       $data['sku'] === 'TEST-001' &&
                       $data['price'] === 99.99 &&
                       $data['quantity'] === 10 &&
                       $data['image'] instanceof UploadedFile;
            }))
            ->once()
            ->andReturn($product);

        // Act
        $response = $this->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(201)
            ->assertJson(['message' => 'Product successfully created']);
    }

    /** @test */
    public function it_returns_validation_errors_for_invalid_data()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $invalidData = [
            'name' => '', // Required field is empty
            'sku' => 'TEST-001',
            'price' => -10, // Negative price
            'quantity' => 'invalid', // Not integer
        ];

        // Act
        $response = $this->postJson('/api/products', $invalidData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['name', 'price', 'quantity']);
    }

    /** @test */
    public function it_can_update_product()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

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

        $this->mockService->shouldReceive('updateProduct')
            ->with($product->id, $updateData)
            ->once()
            ->andReturn($updatedProduct);

        // Act
        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'name', 'sku', 'price', 'quantity', 'created_at', 'updated_at']
            ])
            ->assertJson([
                'message' => 'Product successfully updated',
                'data' => [
                    'name' => 'Updated Product',
                    'price' => 149.99,
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_when_updating_nonexistent_product()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updateData = ['name' => 'Updated Product'];

        $this->mockService->shouldReceive('updateProduct')
            ->with(999, $updateData)
            ->once()
            ->andReturn(null);

        // Act
        $response = $this->putJson('/api/products/999', $updateData);

        // Assert
        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }

    /** @test */
    public function it_can_delete_product()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create();

        $this->mockService->shouldReceive('deleteProduct')
            ->with($product->id)
            ->once()
            ->andReturn(true);

        // Act
        $response = $this->deleteJson("/api/products/{$product->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => 'Product successfully deleted']);
    }

    /** @test */
    public function it_returns_404_when_deleting_nonexistent_product()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->mockService->shouldReceive('deleteProduct')
            ->with(999)
            ->once()
            ->andReturn(false);

        // Act
        $response = $this->deleteJson('/api/products/999');

        // Assert
        $response->assertStatus(404)
            ->assertJson(['message' => 'Product not found']);
    }

    /** @test */
    public function it_requires_authentication_for_protected_routes()
    {
        // Act & Assert
        $this->getJson('/api/products')->assertStatus(401);
        $this->getJson('/api/products/latest')->assertStatus(401);
        $this->getJson('/api/products/stats')->assertStatus(401);
        $this->getJson('/api/products/1')->assertStatus(401);
        $this->postJson('/api/products')->assertStatus(401);
        $this->putJson('/api/products/1')->assertStatus(401);
        $this->deleteJson('/api/products/1')->assertStatus(401);
    }

    /** @test */
    public function it_validates_per_page_parameter()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $products = Product::factory()->count(5)->create();
        $paginator = Product::orderBy('created_at', 'desc')->paginate(50);

        $this->mockService->shouldReceive('getAllProducts')
            ->with(50)
            ->once()
            ->andReturn($paginator);

        // Act
        $response = $this->getJson('/api/products?per_page=50');

        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function it_handles_service_exceptions()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->mockService->shouldReceive('getAllProducts')
            ->once()
            ->andThrow(new \Exception('Database connection failed'));

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response->assertStatus(500)
            ->assertJson(['message' => 'Error loading products: Database connection failed']);
    }
}
