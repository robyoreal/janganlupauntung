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
            <!-- Transaction Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                    @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Items Section -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Items</h2>
                @error('items') <div class="text-red-500 text-sm mb-2">{{ $message }}</div> @enderror

                <div class="space-y-4">
                    @foreach($items as $index => $item)
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold mb-2">Product *</label>
                                    <select wire:model.live="items.{{ $index }}.product_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->stock }})</option>
                                        @endforeach
                                    </select>
                                    @error('items.' . $index . '.product_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Quantity *</label>
                                    <input type="number" wire:model="items.{{ $index }}.quantity" min="1" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                                    @error('items.' . $index . '.quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2">Price per Unit *</label>
                                    <input type="number" step="0.01" wire:model="items.{{ $index }}.price" min="0" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                                    @error('items.' . $index . '.price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-3 flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    @if(isset($item['quantity']) && isset($item['price']) && $item['quantity'] > 0 && $item['price'] > 0)
                                        Subtotal: Rp {{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}
                                    @endif
                                </div>
                                @if(count($items) > 1)
                                    <button type="button" wire:click="removeItem({{ $index }})" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold min-h-[44px]">
                                        Remove Item
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" wire:click="addItem" class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold min-h-[44px] min-w-[120px]">
                    + Add Item
                </button>
            </div>

            <!-- Total Section -->
            @if(count($items) > 0)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <p class="text-xl font-bold text-green-800">
                        Total: Rp {{ number_format($this->getTotal(), 0, ',', '.') }}
                    </p>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="flex gap-2">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold text-lg min-h-[44px]">
                    Record Sale
                </button>
            </div>
        </form>
    </div>
</div>
