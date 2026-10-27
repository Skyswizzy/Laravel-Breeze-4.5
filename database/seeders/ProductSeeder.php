<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        $products = [
            ['Beverages', 'Iced Coffee',      'Cold brew over ice.',             95.00,  50],
            ['Beverages', 'Green Tea',        'Hot or iced green tea.',          70.00,  40],
            ['Snacks',    'Potato Chips',     'Salted, 150g bag.',               55.00, 120],
            ['Snacks',    'Chocolate Cookies','Pack of 6 cookies.',              80.00,  60],
            ['Bakery',    'Butter Croissant', 'Baked fresh every morning.',      65.00,  30],
            ['Bakery',    'Banana Bread',     'Whole loaf, sliced.',            150.00,  15],
        ];

        foreach ($products as [$category, $name, $description, $price, $stock]) {
            Product::updateOrCreate(
                ['name' => $name],
                [
                    'category_id' => $categories[$category],
                    'description' => $description,
                    'price' => $price,
                    'stock_quantity' => $stock,
                    'is_available' => true,
                ]
            );
        }
    }
}
