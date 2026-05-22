<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronice', 'slug' => 'electronice', 'description' => 'Produse electronice și gadgeturi'],
            ['name' => 'Îmbrăcăminte', 'slug' => 'imbracaminte', 'description' => 'Haine și accesorii'],
            ['name' => 'Cărți', 'slug' => 'carti', 'description' => 'Cărți și materiale didactice'],
            ['name' => 'Sport', 'slug' => 'sport', 'description' => 'Articole sportive'],
            ['name' => 'Casă și Grădină', 'slug' => 'casa-gradina', 'description' => 'Produse pentru casă și grădină'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}