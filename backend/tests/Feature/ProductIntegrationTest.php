<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_perform_full_crud_operations()
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

        // Create
        $createResponse = $this->postJson('/api/products', $productData);
        $createResponse->assertStatus(201);
        $productId = $createResponse->json('data.id');

        // Read
        $readResponse = $this->getJson("/api/products/{$productId}");
        $readResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Test Product',
                    'sku' => 'TEST-001',
                    'price' => 99.99,
                    'quantity' => 10,
                ]
            ]);

        // Update
        $updateData = [
            'name' => 'Updated Product',
            'price' => 149.99,
        ];
        $updateResponse = $this->putJson("/api/products/{$productId}", $updateData);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Product',
                    'price' => 149.99,
                ]
            ]);

        // Delete
        $deleteResponse = $this->deleteJson("/api/products/{$productId}");
        $deleteResponse->assertStatus(200);

        // Verify deletion
        $this->getJson("/api/products/{$productId}")->assertStatus(404);
    }

    /** @test */
    public function it_can_handle_pagination_correctly()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Product::factory()->count(25)->create();

        // Act
        $response = $this->getJson('/api/products?per_page=10&page=2');

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
            ])
            ->assertJson([
                'meta' => [
                    'current_page' => 2,
                    'per_page' => 10,
                    'total' => 25,
                    'last_page' => 3,
                ]
            ]);
    }

    /** @test */
    public function it_can_get_latest_products()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $oldProduct = Product::factory()->create(['created_at' => now()->subDays(5)]);
        $newProduct = Product::factory()->create(['created_at' => now()]);

        // Act
        $response = $this->getJson('/api/products/latest');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'sku', 'price', 'quantity', 'created_at', 'updated_at']
                ]
            ]);

        $products = $response->json('data');
        $this->assertCount(2, $products);
        $this->assertEquals($newProduct->id, $products[0]['id']);
        $this->assertEquals($oldProduct->id, $products[1]['id']);
    }

    /** @test */
    public function it_can_get_product_statistics()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

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
        $response = $this->getJson('/api/products/stats');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['total', 'total_value', 'average_price', 'low_stock_count']
            ])
            ->assertJson([
                'data' => [
                    'total' => 3,
                    'low_stock_count' => 2, // Products with quantity <= 5
                ]
            ]);
    }

    /** @test */
    public function it_validates_product_creation_data()
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
            ->assertJsonStructure(['message', 'errors']);
    }

    /** @test */
    public function it_handles_image_upload_during_creation()
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

        // Act
        $response = $this->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(201)
            ->assertJson(['message' => 'Product successfully created']);

        $productId = $response->json('data.id');
        $product = Product::find($productId);
        $this->assertNotNull($product->image);
        $this->assertStringContainsString('products/', $product->image);
    }

    /** @test */
    public function it_handles_partial_updates()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $product = Product::factory()->create([
            'name' => 'Original Name',
            'sku' => 'ORIG-001',
            'price' => 100.00,
            'quantity' => 5,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            // Only updating name, other fields should remain unchanged
        ];

        // Act
        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Name',
                    'sku' => 'ORIG-001', // Should remain unchanged
                    'price' => 100.00, // Should remain unchanged
                    'quantity' => 5, // Should remain unchanged
                ]
            ]);
    }

    /** @test */
    public function it_prevents_duplicate_sku()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Product::factory()->create(['sku' => 'DUPLICATE-001']);

        $productData = [
            'name' => 'Test Product',
            'sku' => 'DUPLICATE-001', // Same SKU
            'price' => 99.99,
            'quantity' => 10,
        ];

        // Act
        $response = $this->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure(['message', 'errors']);
    }

    /** @test */
    public function it_requires_authentication_for_all_routes()
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
}
