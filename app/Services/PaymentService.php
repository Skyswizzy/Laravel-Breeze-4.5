<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Record a payment. An order may have several payments, but their sum
     * must never exceed the order total.
     */
    public function record(Order $order, string|float $amount, string $method): Payment
    {
        return DB::transaction(function () use ($order, $amount, $method) {
            // Lock the order row so two simultaneous payments cannot both pass the check.
            $order = Order::lockForUpdate()->findOrFail($order->id);

            if ($order->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'amount' => 'Payments cannot be added to a cancelled order.',
                ]);
            }

            $newCents = Money::toCents($amount);
            $paidCents = Money::toCents($order->payments()->sum('amount'));
            $totalCents = Money::toCents($order->total_amount);

            if ($newCents <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'The payment amount must be greater than zero.',
                ]);
            }

            if ($paidCents + $newCents > $totalCents) {
                $remaining = Money::fromCents($totalCents - $paidCents);
                throw ValidationException::withMessages([
                    'amount' => "Payments cannot exceed the order total. Remaining balance: {$remaining}.",
                ]);
            }

            $payment = $order->payments()->create([
                'payment_reference' => 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'payment_method' => $method,
                'amount' => Money::fromCents($newCents),
                'paid_at' => now(),
            ]);

            // Fully paid: move a pending order to "paid".
            if ($paidCents + $newCents === $totalCents && $order->status === 'pending') {
                $order->update(['status' => 'paid']);
            }

            return $payment;
        });
    }
}
