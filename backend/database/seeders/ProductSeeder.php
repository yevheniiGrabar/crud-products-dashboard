<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'sku' => 'IPH15PRO-256',
                'price' => 999.99,
                'quantity' => 25,
                'image' => null,
            ],
            [
                'name' => 'MacBook Air M2',
                'sku' => 'MBA-M2-512',
                'price' => 1199.99,
                'quantity' => 15,
                'image' => null,
            ],
            [
                'name' => 'iPad Pro 12.9"',
                'sku' => 'IPADPRO-128',
                'price' => 1099.99,
                'quantity' => 30,
                'image' => null,
            ],
            [
                'name' => 'AirPods Pro',
                'sku' => 'AIRPODS-PRO',
                'price' => 249.99,
                'quantity' => 50,
                'image' => null,
            ],
            [
                'name' => 'Apple Watch Series 9',
                'sku' => 'AW-S9-45MM',
                'price' => 399.99,
                'quantity' => 20,
                'image' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
