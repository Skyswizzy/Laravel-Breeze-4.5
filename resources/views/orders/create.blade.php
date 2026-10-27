<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New order</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('orders.store') }}"
                  class="bg-white shadow-sm sm:rounded-lg p-6 text-sm text-gray-900 space-y-6">
                @csrf

                @if ($errors->any())
                    <div class="rounded bg-red-50 text-red-700 p-3">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="customer_id" class="block font-medium mb-1">Customer</label>
                    <select id="customer_id" name="customer_id" required class="border-gray-300 rounded-md w-full md:w-80">
                        <option value="">Select a customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <p class="font-medium mb-2">Products <span class="text-gray-500 font-normal">(enter a quantity; leave 0 to skip. At least one product is required.)</span></p>
                    <table class="min-w-full text-left">
                        <thead class="border-b text-xs uppercase text-gray-500">
                            <tr>
                                <th class="py-2 pr-4">Product</th>
                                <th class="py-2 pr-4 text-right">Price</th>
                                <th class="py-2 pr-4 text-right">In stock</th>
                                <th class="py-2 text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($products as $product)
                                <tr>
                                    <td class="py-2 pr-4">{{ $product->name }}</td>
                                    <td class="py-2 pr-4 text-right">{{ number_format($product->price, 2) }}</td>
                                    <td class="py-2 pr-4 text-right">{{ $product->stock_quantity }}</td>
                                    <td class="py-2 text-right">
                                        <input type="number" min="0" max="10000"
                                               name="items[{{ $product->id }}]"
                                               value="{{ old('items.'.$product->id, 0) }}"
                                               class="border-gray-300 rounded-md w-24 text-right">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-gray-500 mt-2">Prices and totals are calculated on the server from the product prices.</p>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Create order
                    </button>
                    <a href="{{ route('orders.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
