<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\StatsResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_transforms_stats_to_array()
    {
        // Arrange
        $stats = [
            'total' => 5,
            'total_value' => 1000.00,
            'average_price' => 200.00,
            'low_stock_count' => 2,
        ];

        // Act
        $resource = new StatsResource($stats);
        $array = $resource->toArray(request());

        // Assert
        $this->assertEquals(5, $array['total']);
        $this->assertEquals(1000.00, $array['total_value']);
        $this->assertEquals(200.00, $array['average_price']);
        $this->assertEquals(2, $array['low_stock_count']);
    }

    /** @test */
    public function it_casts_numeric_values_to_float()
    {
        // Arrange
        $stats = [
            'total' => '5',
            'total_value' => '1000.00',
            'average_price' => '200.00',
            'low_stock_count' => '2',
        ];

        // Act
        $resource = new StatsResource($stats);
        $array = $resource->toArray(request());

        // Assert
        $this->assertIsInt($array['total']);
        $this->assertIsFloat($array['total_value']);
        $this->assertIsFloat($array['average_price']);
        $this->assertIsInt($array['low_stock_count']);
    }

    /** @test */
    public function it_handles_zero_values()
    {
        // Arrange
        $stats = [
            'total' => 0,
            'total_value' => 0.00,
            'average_price' => 0.00,
            'low_stock_count' => 0,
        ];

        // Act
        $resource = new StatsResource($stats);
        $array = $resource->toArray(request());

        // Assert
        $this->assertEquals(0, $array['total']);
        $this->assertEquals(0.0, $array['total_value']);
        $this->assertEquals(0.0, $array['average_price']);
        $this->assertEquals(0, $array['low_stock_count']);
    }

    /** @test */
    public function it_handles_large_numbers()
    {
        // Arrange
        $stats = [
            'total' => 999999,
            'total_value' => 999999.99,
            'average_price' => 999.99,
            'low_stock_count' => 999,
        ];

        // Act
        $resource = new StatsResource($stats);
        $array = $resource->toArray(request());

        // Assert
        $this->assertEquals(999999, $array['total']);
        $this->assertEquals(999999.99, $array['total_value']);
        $this->assertEquals(999.99, $array['average_price']);
        $this->assertEquals(999, $array['low_stock_count']);
    }
}
