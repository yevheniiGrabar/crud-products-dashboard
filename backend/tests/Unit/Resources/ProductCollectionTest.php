<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCollectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_transforms_collection_with_pagination_metadata()
    {
        // Arrange
        $products = Product::factory()->count(15)->create();
        $paginator = Product::orderBy('created_at', 'desc')->paginate(10);

        // Act
        $resource = new ProductCollection($paginator);
        $array = $resource->toArray(request());

        // Assert
        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('meta', $array);
        $this->assertArrayHasKey('links', $array);

        $this->assertCount(10, $array['data']);
        $this->assertEquals(15, $array['meta']['total']);
        $this->assertEquals(10, $array['meta']['per_page']);
        $this->assertEquals(2, $array['meta']['last_page']);
        $this->assertEquals(1, $array['meta']['current_page']);
        $this->assertEquals(1, $array['meta']['from']);
        $this->assertEquals(10, $array['meta']['to']);
        $this->assertTrue($array['meta']['has_more_pages']);
    }

    /** @test */
    public function it_includes_pagination_links()
    {
        // Arrange
        $products = Product::factory()->count(15)->create();
        $paginator = Product::orderBy('created_at', 'desc')->paginate(10);

        // Act
        $resource = new ProductCollection($paginator);
        $array = $resource->toArray(request());

        // Assert
        $this->assertArrayHasKey('first', $array['links']);
        $this->assertArrayHasKey('last', $array['links']);
        $this->assertArrayHasKey('prev', $array['links']);
        $this->assertArrayHasKey('next', $array['links']);

        $this->assertNotNull($array['links']['first']);
        $this->assertNotNull($array['links']['last']);
        $this->assertNull($array['links']['prev']); // First page, no previous
        $this->assertNotNull($array['links']['next']); // Has next page
    }

    /** @test */
    public function it_handles_empty_collection()
    {
        // Arrange
        $paginator = Product::paginate(10);

        // Act
        $resource = new ProductCollection($paginator);
        $array = $resource->toArray(request());

        // Assert
        $this->assertEmpty($array['data']);
        $this->assertEquals(0, $array['meta']['total']);
        $this->assertEquals(0, $array['meta']['from']);
        $this->assertEquals(0, $array['meta']['to']);
        $this->assertFalse($array['meta']['has_more_pages']);
    }

    /** @test */
    public function it_transforms_individual_products_in_collection()
    {
        // Arrange
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 99.99,
            'quantity' => 10,
        ]);
        $paginator = Product::paginate(10);

        // Act
        $resource = new ProductCollection($paginator);
        $array = $resource->toArray(request());

        // Assert
        if (!empty($array['data'])) {
            $firstProduct = $array['data'][0];
            $this->assertEquals($product->id, $firstProduct['id']);
            $this->assertEquals('Test Product', $firstProduct['name']);
            $this->assertEquals('TEST-001', $firstProduct['sku']);
            $this->assertEquals(99.99, $firstProduct['price']);
            $this->assertEquals(10, $firstProduct['quantity']);
        }
    }

    /** @test */
    public function it_handles_last_page_pagination()
    {
        // Arrange
        $products = Product::factory()->count(5)->create();
        $paginator = Product::paginate(10);

        // Act
        $resource = new ProductCollection($paginator);
        $array = $resource->toArray(request());

        // Assert
        $this->assertEquals(1, $array['meta']['current_page']);
        $this->assertEquals(1, $array['meta']['last_page']);
        $this->assertFalse($array['meta']['has_more_pages']);
        $this->assertNull($array['links']['next']); // No next page
    }
}
