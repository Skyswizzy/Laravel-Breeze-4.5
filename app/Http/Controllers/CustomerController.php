<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('admin.table', [
            'title' => 'Customers',
            'items' => Customer::orderBy('id')->paginate(15),
            'columns' => [
                'ID' => fn ($m) => $m->id,
                'Name' => fn ($m) => $m->name,
                'Email' => fn ($m) => $m->email,
                'Phone' => fn ($m) => $m->phone,
                'Address' => fn ($m) => $m->address,
                'Created' => fn ($m) => $m->created_at,
                'Updated' => fn ($m) => $m->updated_at,
            ],
        ]);
    }
}
