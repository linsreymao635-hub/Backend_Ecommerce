<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::all();

foreach ($products as $product) {
    $newImage = 'products/product-' . $product->id . '.svg';
    $product->update(['image' => $newImage]);
    echo 'Updated: ' . $product->name . ' -> ' . $newImage . PHP_EOL;
}
