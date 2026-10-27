<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        // Eager load customer and staff to avoid N+1 queries.
        $orders = Order::with(['customer:id,name', 'user:id,name'])
            ->latest('order_date')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('orders.create', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products' => Product::where('is_available', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, OrderService $orders): RedirectResponse
    {
        // Note: no prices are accepted from the request. Only product ids and quantities.
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:10000'],
        ]);

        // The staff member is always the logged-in user.
        $order = $orders->create(
            (int) $data['customer_id'],
            (int) $request->user()->id,
            $data['items']
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('status', "Order {$order->order_number} created.");
    }

    /** Order detail: shows its order_items and payments. */
    public function show(Order $order): View
    {
        $order->load(['customer', 'user', 'items.product', 'payments']);

        return view('orders.show', compact('order'));
    }
}
