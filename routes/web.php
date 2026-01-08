<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Products\Index as ProductsIndex;
use App\Livewire\Suppliers\Index as SuppliersIndex;
use App\Livewire\Customers\Index as CustomersIndex;
use App\Livewire\SalesForce\Index as SalesForceIndex;
use App\Livewire\Transactions\Buy;
use App\Livewire\Transactions\Sell;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/products', ProductsIndex::class)->name('products.index');
Route::get('/suppliers', SuppliersIndex::class)->name('suppliers.index');
Route::get('/customers', CustomersIndex::class)->name('customers.index');
Route::get('/salesforce', SalesForceIndex::class)->name('salesforce.index');
Route::get('/buy', Buy::class)->name('transactions.buy');
Route::get('/sell', Sell::class)->name('transactions.sell');
