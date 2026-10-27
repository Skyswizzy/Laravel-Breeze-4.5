<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.table', [
            'title' => 'Products',
            'items' => Product::with('category:id,name')->orderBy('id')->paginate(15),
            'columns' => [
                'ID' => fn ($m) => $m->id,
                'Category' => fn ($m) => $m->category->name,
                'Name' => fn ($m) => $m->name,
                'Description' => fn ($m) => $m->description,
                'Price' => fn ($m) => number_format($m->price, 2),
                'Stock' => fn ($m) => $m->stock_quantity,
                'Available' => fn ($m) => $m->is_available ? 'Yes' : 'No',
                'Created' => fn ($m) => $m->created_at,
                'Updated' => fn ($m) => $m->updated_at,
            ],
        ]);
    }
}
