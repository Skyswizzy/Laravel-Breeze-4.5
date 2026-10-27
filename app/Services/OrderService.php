<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order with its items.
     *
     * Business rules enforced here:
     *  - at least one product is required
     *  - prices come from the products table, never from the request
     *  - order_items.unit_price keeps the price at the time of sale
     *  - subtotal = quantity x unit_price
     *  - orders.total_amount = sum of the item subtotals
     *  - order and items are saved in one database transaction
     *
     * @param  array<int|string, int|string>  $quantities  product_id => quantity
     */
    public function create(int $customerId, int $staffId, array $quantities): Order
    {
        // Keep only lines with a positive quantity.
        $quantities = array_filter(
            array_map('intval', $quantities),
            fn (int $qty) => $qty > 0
        );

        if ($quantities === []) {
            throw ValidationException::withMessages([
                'items' => 'An order must contain at least one product.',
            ]);
        }

        return DB::transaction(function () use ($customerId, $staffId, $quantities) {
            // Lock the product rows so prices cannot change mid-calculation.
            $products = Product::whereIn('id', array_keys($quantities))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($quantities)) {
                throw ValidationException::withMessages([
                    'items' => 'One or more selected products do not exist.',
                ]);
            }

            foreach ($products as $product) {
                if (! $product->is_available) {
                    throw ValidationException::withMessages([
                        'items' => "{$product->name} is not available.",
                    ]);
                }
            }

            $order = Order::create([
                'order_number' => $this->newOrderNumber(),
                'customer_id' => $customerId,
                'user_id' => $staffId,
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalCents = 0;

            foreach ($quantities as $productId => $quantity) {
                $unitCents = Money::toCents($products[$productId]->price); // database price
                $subtotalCents = $quantity * $unitCents;
                $totalCents += $subtotalCents;

                $order->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => Money::fromCents($unitCents),
                    'subtotal' => Money::fromCents($subtotalCents),
                ]);
            }

            $order->update(['total_amount' => Money::fromCents($totalCents)]);

            return $order;
        });
    }

    private function newOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
