<div>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">Products</h1>
            @if(!$showForm)
                <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    + Add Product
                </button>
            @endif
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if($showForm)
        <!-- Product Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">{{ $editingId ? 'Edit Product' : 'New Product' }}</h2>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Product Name *</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">SKU</label>
                        <input type="text" wire:model="sku" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('sku') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Cost Price *</label>
                        <input type="number" step="0.01" wire:model="cost_price" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('cost_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Selling Price *</label>
                        <input type="number" step="0.01" wire:model="selling_price" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('selling_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Initial Stock *</label>
                        <input type="number" wire:model="stock" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                        Save
                    </button>
                    <button type="button" wire:click="cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Products List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b">
            <input type="text" wire:model.live="search" placeholder="Search products..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Product</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Cost</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Selling</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Stock</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <p class="font-semibold">{{ $product->name }}</p>
                                @if($product->sku)<p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>@endif
                            </td>
                            <td class="py-3 px-4 text-right">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : ($product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button wire:click="edit({{ $product->id }})" class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                                <button wire:click="delete({{ $product->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-500">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
