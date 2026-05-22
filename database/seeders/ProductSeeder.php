<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $products = [
            [
                'category_id' => $categories->where('slug', 'electronice')->first()->id,
                'name' => 'Telefon Smartphone XYZ',
                'description' => 'Un smartphone modern cu cameră de 48MP și procesor rapid.',
                'price' => 2500.00,
                'special_price' => 2200.00,
                'stock' => 50,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $categories->where('slug', 'electronice')->first()->id,
                'name' => 'Laptop Gaming ABC',
                'description' => 'Laptop puternic pentru gaming cu placă video dedicată.',
                'price' => 4500.00,
                'stock' => 20,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'category_id' => $categories->where('slug', 'imbracaminte')->first()->id,
                'name' => 'Tricou Polo',
                'description' => 'Tricou elegant din bumbac organic.',
                'price' => 150.00,
                'stock' => 100,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $categories->where('slug', 'carti')->first()->id,
                'name' => 'Programare în PHP',
                'description' => 'Carte completă despre programarea în PHP.',
                'price' => 80.00,
                'stock' => 30,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'category_id' => $categories->where('slug', 'sport')->first()->id,
                'name' => 'Bicicletă Mountain Bike',
                'description' => 'Bicicletă robustă pentru teren accidentat.',
                'price' => 1200.00,
                'special_price' => 1000.00,
                'stock' => 15,
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}