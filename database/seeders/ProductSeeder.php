<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Category, Product};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Books and literature'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'description' => 'Home improvement and garden supplies'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports equipment and accessories'],
        ];

        // Get or create categories and store their IDs
        $categoryIds = [];
        foreach ($categories as $category) {
            $cat = Category::updateOrCreate(['slug' => $category['slug']], $category);
            $categoryIds[$category['slug']] = $cat->id;
        }

        // Create products with placeholder images
        $products = [
            ['name' => 'Wireless Headphones', 'category_slug' => 'electronics', 'price' => 79.99, 'stock' => 50, 'description' => 'High-quality wireless headphones with noise cancellation'],
            ['name' => 'Smartphone', 'category_slug' => 'electronics', 'price' => 699.99, 'stock' => 30, 'description' => 'Latest smartphone with advanced features'],
            ['name' => 'Laptop', 'category_slug' => 'electronics', 'price' => 1299.99, 'stock' => 20, 'description' => 'Powerful laptop for work and gaming'],
            ['name' => 'T-Shirt', 'category_slug' => 'clothing', 'price' => 19.99, 'stock' => 100, 'description' => 'Comfortable cotton t-shirt'],
            ['name' => 'Jeans', 'category_slug' => 'clothing', 'price' => 49.99, 'stock' => 75, 'description' => 'Classic fit denim jeans'],
            ['name' => 'Novel Book', 'category_slug' => 'books', 'price' => 14.99, 'stock' => 200, 'description' => 'Bestselling fiction novel'],
            ['name' => 'Cookbook', 'category_slug' => 'books', 'price' => 24.99, 'stock' => 60, 'description' => 'Delicious recipes for home cooking'],
            ['name' => 'Garden Tools Set', 'category_slug' => 'home-garden', 'price' => 39.99, 'stock' => 40, 'description' => 'Complete set of garden tools'],
            ['name' => 'Basketball', 'category_slug' => 'sports', 'price' => 29.99, 'stock' => 80, 'description' => 'Official size basketball'],
            ['name' => 'Yoga Mat', 'category_slug' => 'sports', 'price' => 19.99, 'stock' => 120, 'description' => 'Non-slip yoga mat for exercise'],
        ];

        foreach ($products as $index => $product) {
            $productData = [
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'category_id' => $categoryIds[$product['category_slug']],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'description' => $product['description'],
                'is_active' => true,
            ];

            // Create a simple colored placeholder image for each product
            $imagePath = $this->createPlaceholderImage($product['name'], $index);
            if ($imagePath) {
                $productData['image'] = $imagePath;
            }

            Product::updateOrCreate(
                ['slug' => Str::slug($product['name'])],
                $productData
            );
        }
    }

    private function createPlaceholderImage($productName, $index)
    {
        // Create a simple SVG placeholder and save it
        $colors = [
            ['667eea', '764ba2'], // Purple
            ['f093fb', 'f5576c'], // Pink
            ['4facfe', '00f2fe'], // Blue
            ['43e97b', '38f9d7'], // Green
            ['fa709a', 'fee140'], // Orange
            ['30cfd0', '330867'], // Dark blue
            ['a8edea', 'fed6e3'], // Light
            ['ff9a9e', 'fecfef'], // Rose
            ['ff6e7f', 'bfe9ff'], // Coral
            ['c3cfe2', 'f5f7fa'], // Gray
        ];

        $colorPair = $colors[$index % count($colors)];
        $svg = <<<SVG
<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#{$colorPair[0]};stop-opacity:1" />
            <stop offset="100%" style="stop-color:#{$colorPair[1]};stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="400" height="300" fill="url(#grad)"/>
    <text x="200" y="150" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">{$productName}</text>
</svg>
SVG;

        $filename = 'product-' . ($index + 1) . '.svg';
        $path = 'products/' . $filename;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
