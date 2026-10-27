<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.table', [
            'title' => 'Categories',
            'items' => Category::orderBy('id')->paginate(15),
            'columns' => [
                'ID' => fn ($m) => $m->id,
                'Name' => fn ($m) => $m->name,
                'Description' => fn ($m) => $m->description,
                'Created' => fn ($m) => $m->created_at,
                'Updated' => fn ($m) => $m->updated_at,
            ],
        ]);
    }
}
