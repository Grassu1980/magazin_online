<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::all();

foreach ($products as $product) {
    $product->slug = \Str::slug($product->name);
    $product->save();
}

echo "Slugs updated\n";