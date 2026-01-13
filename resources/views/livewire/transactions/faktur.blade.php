<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Faktur (Penjualan)</h1>
        <p class="text-gray-600">Kelola daftar faktur penjualan barang</p>
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
                <input type="text" wire:model.live="search" placeholder="Produk, SKU, Customer, Sales..."
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" wire:model.live="filterDateFrom"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" wire:model.live="filterDateTo"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Bayar</label>
                <select wire:model.live="filterPaymentStatus"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="paid">Lunas</option>
                    <option value="unpaid">Belum Lunas</option>
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button wire:click="clearFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg min-h-[44px]">
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
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Items</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Customer</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Sales</th>
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
                                <div class="font-medium">{{ $transaction->items->count() }} item(s)</div>
                                <div class="text-sm text-gray-500">
                                    @foreach($transaction->items->take(2) as $item)
                                        {{ $item->product->name }}@if(!$loop->last), @endif
                                    @endforeach
                                    @if($transaction->items->count() > 2)
                                        ...
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">{{ $transaction->customer?->name ?? '-' }}</td>
                            <td class="py-3 px-4">{{ $transaction->sales?->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-2 justify-end">
                                    <button wire:click="view({{ $transaction->id }})"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm min-h-[44px]">
                                        View
                                    </button>
                                    <button wire:click="editPayment({{ $transaction->id }})"
                                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm min-h-[44px]">
                                        💳 Bayar
                                    </button>
                                    <button wire:click="delete({{ $transaction->id }})"
                                            onclick="return confirm('Yakin ingin menghapus faktur ini? Stok akan dikembalikan.')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm min-h-[44px]">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @if($transaction->notes)
                            <tr class="border-b bg-gray-50">
                                <td colspan="7" class="py-2 px-4 text-sm text-gray-600">
                                    <strong>Catatan:</strong> {{ $transaction->notes }}
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                Tidak ada data faktur.
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

    <!-- View Modal -->
    @if($showViewModal && $viewingTransaction)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeView">
            <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Detail Faktur</h3>
                    <button wire:click="closeView" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Transaction Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Transaksi</p>
                        <p class="font-medium">{{ $viewingTransaction->transaction_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Customer</p>
                        <p class="font-medium">{{ $viewingTransaction->customer?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Sales Person</p>
                        <p class="font-medium">{{ $viewingTransaction->sales?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Pembayaran</p>
                        <p class="font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $viewingTransaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $viewingTransaction->payment_status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="font-bold text-lg">Rp {{ number_format($viewingTransaction->total, 0, ',', '.') }}</p>
                    </div>
                    @if($viewingTransaction->notes)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600">Catatan</p>
                            <p class="font-medium">{{ $viewingTransaction->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Items -->
                <h4 class="text-lg font-bold mb-3">Items</h4>
                <div class="overflow-x-auto mb-6">
                    <table class="w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left py-2 px-3 border-b">Produk</th>
                                <th class="text-right py-2 px-3 border-b">Qty</th>
                                <th class="text-right py-2 px-3 border-b">Harga</th>
                                <th class="text-right py-2 px-3 border-b">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($viewingTransaction->items as $item)
                                <tr class="border-b">
                                    <td class="py-2 px-3">
                                        <div class="font-medium">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->product->sku }}</div>
                                    </td>
                                    <td class="py-2 px-3 text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex gap-2">
                    <button wire:click="closeView"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium min-h-[44px]">
                        Tutup
                    </button>
                </div>
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
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="paid">Lunas</option>
                            <option value="unpaid">Belum Lunas</option>
                        </select>
                        @error('payment_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium min-h-[44px]">
                            Simpan
                        </button>
                        <button type="button" wire:click="cancelPayment"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg font-medium min-h-[44px]">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
