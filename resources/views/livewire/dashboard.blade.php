<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Month to Date Summary - {{ now()->format('F Y') }}</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <!-- Revenue Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Revenue</p>
                    <p class="text-3xl font-bold text-green-600">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Debt Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Unpaid Debt</p>
                    <p class="text-3xl font-bold text-red-600">Rp {{ number_format($debt, 0, ',', '.') }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-semibold">Profit</p>
                    <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($profit, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Top Selling Products</h2>
        </div>
        <div class="p-6">
            @if($topProducts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Rank</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Product</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Qty Sold</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $index => $item)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                            {{ $index === 0 ? 'bg-yellow-400 text-white' : ($index === 1 ? 'bg-gray-300 text-gray-800' : ($index === 2 ? 'bg-orange-400 text-white' : 'bg-gray-100 text-gray-600')) }} 
                                            font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $item->product->name }}</p>
                                            @if($item->product->sku)
                                                <p class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold">{{ number_format($item->total_quantity) }}</td>
                                    <td class="py-3 px-4 text-right font-semibold text-green-600">Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <p class="mt-4 text-gray-500">No sales data for this month yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
