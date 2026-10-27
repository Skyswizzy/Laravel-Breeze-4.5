<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::where('email', 'staff@example.com')->firstOrFail();
        $customers = Customer::orderBy('id')->get();
        $products = Product::pluck('id', 'name');
        $prices = Product::pluck('price', 'name');

        // Each order: customer index, status, days ago, [product name => quantity], payment method (or null = unpaid)
        $orders = [
            [
                'customer' => 0,
                'status' => 'completed',
                'days_ago' => 5,
                'items' => ['Iced Coffee' => 2, 'Butter Croissant' => 3],
                'payment' => 'cash',
            ],
            [
                'customer' => 1,
                'status' => 'paid',
                'days_ago' => 2,
                'items' => ['Potato Chips' => 4, 'Chocolate Cookies' => 2, 'Green Tea' => 1],
                'payment' => 'gcash',
            ],
            [
                'customer' => 2,
                'status' => 'pending',
                'days_ago' => 0,
                'items' => ['Banana Bread' => 1, 'Iced Coffee' => 2],
                'payment' => null, // not paid yet
            ],
        ];

        DB::transaction(function () use ($orders, $staff, $customers, $products, $prices) {
            foreach ($orders as $i => $data) {
                $number = sprintf('ORD-2026-%04d', $i + 1);
                $orderDate = now()->subDays($data['days_ago']);

                $order = Order::updateOrCreate(
                    ['order_number' => $number],
                    [
                        'customer_id' => $customers[$data['customer']]->id,
                        'user_id' => $staff->id,
                        'order_date' => $orderDate,
                        'status' => $data['status'],
                        'total_amount' => 0,
                    ]
                );

                // Rebuild items so re-running the seeder doesn't duplicate lines.
                $order->items()->delete();

                $total = 0;
                foreach ($data['items'] as $productName => $qty) {
                    $unitPrice = $prices[$productName];
                    $subtotal = $unitPrice * $qty;
                    $total += $subtotal;

                    $order->items()->create([
                        'product_id' => $products[$productName],
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);
                }

                $order->update(['total_amount' => $total]);

                if ($data['payment']) {
                    Payment::updateOrCreate(
                        ['payment_reference' => sprintf('PAY-2026-%04d', $i + 1)],
                        [
                            'order_id' => $order->id,
                            'payment_method' => $data['payment'],
                            'amount' => $total,
                            'paid_at' => $orderDate->copy()->addMinutes(10),
                        ]
                    );
                }
            }
        });
    }
}
