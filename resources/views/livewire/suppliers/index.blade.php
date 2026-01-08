<div>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Suppliers</h1>
        @if(!$showForm)
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">+ Add Supplier</button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('message') }}</div>
    @endif

    @if($showForm)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">{{ $editingId ? 'Edit Supplier' : 'New Supplier' }}</h2>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-2">Name *</label>
                        <input type="text" wire:model="name" class="w-full px-4 py-2 border rounded-lg">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Contact Person</label>
                        <input type="text" wire:model="contact" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">Phone</label>
                        <input type="text" wire:model="phone" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Address</label>
                        <textarea wire:model="address" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">Save</button>
                    <button type="button" wire:click="cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-semibold">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-3 px-4 font-semibold">Name</th>
                    <th class="text-left py-3 px-4 font-semibold">Contact</th>
                    <th class="text-left py-3 px-4 font-semibold">Phone</th>
                    <th class="text-right py-3 px-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 font-semibold">{{ $supplier->name }}</td>
                        <td class="py-3 px-4">{{ $supplier->contact }}</td>
                        <td class="py-3 px-4">{{ $supplier->phone }}</td>
                        <td class="py-3 px-4 text-right">
                            <button wire:click="edit({{ $supplier->id }})" class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                            <button wire:click="delete({{ $supplier->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center text-gray-500">No suppliers found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $suppliers->links() }}</div>
    </div>
</div>
