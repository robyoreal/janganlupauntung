<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tagihan (Pembelian)</h1>
        <p class="text-gray-600">Kelola daftar tagihan pembelian barang</p>
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">Filter</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" wire:model.live="search" placeholder="Produk, SKU, Supplier..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" wire:model.live="filterDateFrom"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" wire:model.live="filterDateTo"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Bayar</label>
                <select wire:model.live="filterPaymentStatus"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="paid">Lunas</option>
                    <option value="unpaid">Belum Lunas</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Tanggal</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Produk</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Supplier</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Jumlah</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Harga</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Total</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Status Bayar</th>
                        <th class="text-right py-3 px-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium">{{ $transaction->product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->product->sku }}</div>
                            </td>
                            <td class="py-3 px-4">{{ $transaction->supplier?->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-right">{{ number_format($transaction->quantity, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right">Rp {{ number_format($transaction->price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button wire:click="editPayment({{ $transaction->id }})"
                                        class="text-orange-600 hover:text-orange-800 mr-2">
                                    💳 Bayar
                                </button>
                                <button wire:click="edit({{ $transaction->id }})"
                                        class="text-blue-600 hover:text-blue-800 mr-2">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $transaction->id }})"
                                        onclick="return confirm('Yakin ingin menghapus tagihan ini?')"
                                        class="text-red-600 hover:text-red-800">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @if($transaction->notes)
                            <tr class="border-b bg-gray-50">
                                <td colspan="8" class="py-2 px-4 text-sm text-gray-600">
                                    <strong>Catatan:</strong> {{ $transaction->notes }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500">
                                Tidak ada data tagihan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Edit Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelEdit">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Edit Tagihan</h3>
                    <button wire:click="cancelEdit" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Produk *</label>
                            <select wire:model="product_id" required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih Produk</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                            <select wire:model="supplier_id"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah *</label>
                            <input type="number" wire:model="quantity" required min="1"
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp) *</label>
                            <input type="number" wire:model="price" required min="0" step="0.01"
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Bayar *</label>
                            <select wire:model="payment_status" required
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="paid">Lunas</option>
                                <option value="unpaid">Belum Lunas</option>
                            </select>
                            @error('payment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi *</label>
                            <input type="date" wire:model="transaction_date" required
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('transaction_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea wire:model="notes" rows="3"
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                            Simpan
                        </button>
                        <button type="button" wire:click="cancelEdit"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Payment Status Modal -->
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="cancelPayment">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Update Status Pembayaran</h3>
                    <button wire:click="cancelPayment" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePayment">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Bayar *</label>
                        <select wire:model="payment_status" required
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="paid">Lunas</option>
                            <option value="unpaid">Belum Lunas</option>
                        </select>
                        @error('payment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                            Simpan
                        </button>
                        <button type="button" wire:click="cancelPayment"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
