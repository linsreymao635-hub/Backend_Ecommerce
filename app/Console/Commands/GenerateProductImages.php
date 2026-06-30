<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Signature('app:generate-product-images')]
#[Description('Generate placeholder images for products that don\'t have images')]
class GenerateProductImages extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating product images...');

        $products = Product::all();
        $count = 0;

        foreach ($products as $product) {
            $imagePath = $product->image;

            // Check if image already exists in storage
            if (Storage::disk('public')->exists($imagePath)) {
                $this->line("✓ Image already exists for: {$product->name}");
                continue;
            }

            // Create a placeholder image
            $this->createPlaceholderImage($product, $imagePath);
            $this->info("✓ Generated image for: {$product->name}");
            $count++;
        }

        $this->info("Successfully generated {$count} product images.");
        return Command::SUCCESS;
    }

    private function createPlaceholderImage($product, $imagePath)
    {
        // Create a simple colored placeholder SVG
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

        $colorPair = $colors[($product->id - 1) % count($colors)];
        $productName = $product->name;

        $svg = <<<SVG
<svg width="400" height="300" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#{$colorPair[0]};stop-opacity:1" />
            <stop offset="100%" style="stop-color:#{$colorPair[1]};stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="400" height="300" fill="url(#grad)"/>
    <text x="200" y="150" font-family="Arial, sans-serif" font-size="20" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">{$productName}</text>
    <text x="200" y="180" font-family="Arial, sans-serif" font-size="14" fill="white" text-anchor="middle" dominant-baseline="middle">\${$product->price}</text>
</svg>
SVG;

        Storage::disk('public')->put($imagePath, $svg);
    }
}
