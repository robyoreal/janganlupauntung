<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Sale / Sell</h1>
        <p class="text-gray-600">Record new sale - decreases inventory and adds revenue</p>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Product *</label>
                    <select wire:model.live="product_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Customer</label>
                    <select wire:model="customer_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Select Customer (Optional)</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Sales Person</label>
                    <select wire:model="sales_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Select Sales (Optional)</option>
                        @foreach($salesforce as $sales)
                            <option value="{{ $sales->id }}">{{ $sales->name }}</option>
                        @endforeach
                    </select>
                    @error('sales_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Quantity *</label>
                    <input type="number" wire:model="quantity" min="1" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Price per Unit *</label>
                    <input type="number" step="0.01" wire:model="price" min="0" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Transaction Date *</label>
                    <input type="date" wire:model="transaction_date" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                    @error('transaction_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Payment Status *</label>
                    <select wire:model="payment_status" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="paid">Paid</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                    @error('payment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-2">Notes</label>
                    <textarea wire:model="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($quantity && $price)
                    <div class="md:col-span-2 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-lg font-semibold text-green-800">
                            Total: Rp {{ number_format($quantity * $price, 0, ',', '.') }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold text-lg">
                    Record Sale
                </button>
            </div>
        </form>
    </div>
</div>
