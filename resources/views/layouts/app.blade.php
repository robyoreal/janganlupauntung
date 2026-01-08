<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Jangan Lupa Untung') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-xl font-bold">Jangan Lupa Untung</a>
                </div>
                <div class="flex space-x-2 overflow-x-auto">
                    <a href="/" class="px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm">Dashboard</a>
                    <a href="/products" class="px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm">Products</a>
                    <a href="/buy" class="px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm">Buy</a>
                    <a href="/sell" class="px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm">Sell</a>
                    <a href="/suppliers" class="px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm">Suppliers</a>
                    <a href="/customers" class="px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm">Customers</a>
                    <a href="/salesforce" class="px-3 py-2 rounded hover:bg-blue-700 whitespace-nowrap text-sm">Sales</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
