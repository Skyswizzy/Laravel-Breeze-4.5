<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Beverages', 'description' => 'Coffee, tea and cold drinks.'],
            ['name' => 'Snacks', 'description' => 'Chips, biscuits and other light bites.'],
            ['name' => 'Bakery', 'description' => 'Freshly baked bread and pastries.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
