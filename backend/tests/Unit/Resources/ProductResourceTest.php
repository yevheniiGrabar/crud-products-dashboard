<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_transforms_product_to_array()
    {
        // Arrange
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 10,
            'image' => 'products/test.jpg',
        ]);

        // Act
        $resource = new ProductResource($product);
        $array = $resource->toArray(request());

        // Assert
        $this->assertEquals($product->id, $array['id']);
        $this->assertEquals('Test Product', $array['name']);
        $this->assertEquals('TEST-001', $array['sku']);
        $this->assertEquals(99.99, $array['price']);
        $this->assertEquals(10, $array['quantity']);
        $this->assertEquals(asset('storage/products/test.jpg'), $array['image']);
        $this->assertEquals($product->created_at->toISOString(), $array['created_at']);
        $this->assertEquals($product->updated_at->toISOString(), $array['updated_at']);
    }

    /** @test */
    public function it_handles_null_image()
    {
        // Arrange
        $product = Product::factory()->create([
            'image' => null,
        ]);

        // Act
        $resource = new ProductResource($product);
        $array = $resource->toArray(request());

        // Assert
        $this->assertNull($array['image']);
    }

    /** @test */
    public function it_casts_price_to_float()
    {
        // Arrange
        $product = Product::factory()->create([
            'price' => '99.99',
        ]);

        // Act
        $resource = new ProductResource($product);
        $array = $resource->toArray(request());

        // Assert
        $this->assertIsFloat($array['price']);
        $this->assertEquals(99.99, $array['price']);
    }

    /** @test */
    public function it_formats_dates_as_iso_string()
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $resource = new ProductResource($product);
        $array = $resource->toArray(request());

        // Assert
        $this->assertIsString($array['created_at']);
        $this->assertIsString($array['updated_at']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/', $array['created_at']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/', $array['updated_at']);
    }
}
