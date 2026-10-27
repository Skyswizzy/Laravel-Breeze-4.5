<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Orders') }}
            </h2>
            <a href="{{ route('orders.create') }}"
               class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">New order</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b text-xs uppercase text-gray-500">
                            <tr>
                                <th class="py-3 pr-4">Order #</th>
                                <th class="py-3 pr-4">Customer</th>
                                <th class="py-3 pr-4">Staff</th>
                                <th class="py-3 pr-4 text-right">Total</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($orders as $order)
                                @php
                                    $badge = match ($order->status) {
                                        'completed' => 'bg-green-100 text-green-800',
                                        'paid' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                @endphp
                                <tr>
                                    <td class="py-3 pr-4 font-medium">
                                        <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:underline">{{ $order->order_number }}</a>
                                    </td>
                                    <td class="py-3 pr-4">{{ $order->customer->name }}</td>
                                    <td class="py-3 pr-4">{{ $order->user->name }}</td>
                                    <td class="py-3 pr-4 text-right">{{ number_format($order->total_amount, 2) }}</td>
                                    <td class="py-3">
                                        <span class="inline-block rounded-full px-3 py-0.5 text-xs font-semibold {{ $badge }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $orders->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
