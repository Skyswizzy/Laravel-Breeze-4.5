<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Order {{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-900">
                <dl class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div><dt class="text-gray-500">Customer</dt><dd class="font-medium">{{ $order->customer->name }}</dd></div>
                    <div><dt class="text-gray-500">Staff</dt><dd class="font-medium">{{ $order->user->name }}</dd></div>
                    <div><dt class="text-gray-500">Order date</dt><dd class="font-medium">{{ $order->order_date }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium">{{ ucfirst($order->status) }}</dd></div>
                    <div><dt class="text-gray-500">Total</dt><dd class="font-medium">{{ number_format($order->total_amount, 2) }}</dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-900 overflow-x-auto">
                <h3 class="font-semibold mb-3">Order items</h3>
                <table class="min-w-full text-left">
                    <thead class="border-b text-xs uppercase text-gray-500">
                        <tr>
                            <th class="py-2 pr-4">Product</th>
                            <th class="py-2 pr-4 text-right">Qty</th>
                            <th class="py-2 pr-4 text-right">Unit price</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="py-2 pr-4">{{ $item->product->name }}</td>
                                <td class="py-2 pr-4 text-right">{{ $item->quantity }}</td>
                                <td class="py-2 pr-4 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-2 text-right">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (session('status'))
                <div class="rounded bg-green-50 text-green-700 p-3 text-sm">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-900 overflow-x-auto">
                <h3 class="font-semibold mb-3">Payments</h3>
                @php
                    $paid = $order->payments->sum('amount');
                    $remaining = $order->total_amount - $paid;
                @endphp
                <p class="mb-3 text-gray-600">
                    Paid {{ number_format($paid, 2) }} of {{ number_format($order->total_amount, 2) }}
                    &middot; Remaining {{ number_format($remaining, 2) }}
                </p>
                @if ($order->payments->isEmpty())
                    <p class="text-gray-500">No payment recorded yet.</p>
                @else
                    <table class="min-w-full text-left">
                        <thead class="border-b text-xs uppercase text-gray-500">
                            <tr>
                                <th class="py-2 pr-4">Reference</th>
                                <th class="py-2 pr-4">Method</th>
                                <th class="py-2 pr-4 text-right">Amount</th>
                                <th class="py-2">Paid at</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($order->payments as $payment)
                                <tr>
                                    <td class="py-2 pr-4">{{ $payment->payment_reference }}</td>
                                    <td class="py-2 pr-4">{{ $payment->payment_method }}</td>
                                    <td class="py-2 pr-4 text-right">{{ number_format($payment->amount, 2) }}</td>
                                    <td class="py-2">{{ $payment->paid_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                @if ($order->status !== 'cancelled' && $remaining > 0)
                    <form method="POST" action="{{ route('payments.store', $order) }}" class="mt-5 flex flex-wrap items-end gap-3">
                        @csrf
                        <div>
                            <label for="amount" class="block text-gray-600 mb-1">Amount</label>
                            <input id="amount" type="number" step="0.01" min="0.01" name="amount"
                                   value="{{ old('amount') }}" required class="border-gray-300 rounded-md w-36">
                        </div>
                        <div>
                            <label for="payment_method" class="block text-gray-600 mb-1">Method</label>
                            <select id="payment_method" name="payment_method" class="border-gray-300 rounded-md">
                                @foreach (['cash' => 'Cash', 'card' => 'Card', 'gcash' => 'GCash', 'bank_transfer' => 'Bank transfer'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Add payment
                        </button>
                    </form>
                    @error('amount')
                        <p class="mt-2 text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Back to orders</a>
        </div>
    </div>
</x-app-layout>
