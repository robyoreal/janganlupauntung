<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Sales;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Products
        $products = [
            ['name' => 'Laptop HP', 'sku' => 'HP-001', 'description' => 'HP Laptop 14 inch', 'cost_price' => 5000000, 'selling_price' => 6500000, 'stock' => 10],
            ['name' => 'Mouse Wireless', 'sku' => 'MSE-001', 'description' => 'Logitech Wireless Mouse', 'cost_price' => 150000, 'selling_price' => 200000, 'stock' => 50],
            ['name' => 'Keyboard Mechanical', 'sku' => 'KBD-001', 'description' => 'RGB Mechanical Keyboard', 'cost_price' => 500000, 'selling_price' => 700000, 'stock' => 30],
            ['name' => 'Monitor LED 24"', 'sku' => 'MON-001', 'description' => 'Samsung 24" LED Monitor', 'cost_price' => 1500000, 'selling_price' => 2000000, 'stock' => 15],
            ['name' => 'Headset Gaming', 'sku' => 'HDS-001', 'description' => 'Gaming Headset with Mic', 'cost_price' => 300000, 'selling_price' => 450000, 'stock' => 25],
        ];
        
        foreach ($products as $product) {
            Product::create($product);
        }

        // Create Suppliers
        $suppliers = [
            ['name' => 'PT Teknologi Maju', 'contact' => 'Budi Santoso', 'phone' => '081234567890', 'address' => 'Jakarta'],
            ['name' => 'CV Elektronik Jaya', 'contact' => 'Siti Aminah', 'phone' => '081234567891', 'address' => 'Bandung'],
        ];
        
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Create Customers
        $customers = [
            ['name' => 'Ahmad Rizki', 'phone' => '081234567892', 'address' => 'Jakarta Selatan'],
            ['name' => 'Dewi Lestari', 'phone' => '081234567893', 'address' => 'Jakarta Utara'],
            ['name' => 'Eko Prasetyo', 'phone' => '081234567894', 'address' => 'Depok'],
        ];
        
        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        // Create Sales
        $salesforce = [
            ['name' => 'Rudi Hartono', 'phone' => '081234567895', 'email' => 'rudi@example.com'],
            ['name' => 'Sarah Wijaya', 'phone' => '081234567896', 'email' => 'sarah@example.com'],
        ];
        
        foreach ($salesforce as $sales) {
            Sales::create($sales);
        }

        // Create some transactions (buy and sell)
        // Buy transactions
        Transaction::create([
            'type' => 'buy',
            'product_id' => 1,
            'supplier_id' => 1,
            'quantity' => 5,
            'price' => 5000000,
            'total' => 25000000,
            'payment_status' => 'paid',
            'transaction_date' => now()->startOfMonth()->addDays(2),
        ]);

        Transaction::create([
            'type' => 'buy',
            'product_id' => 2,
            'supplier_id' => 2,
            'quantity' => 20,
            'price' => 150000,
            'total' => 3000000,
            'payment_status' => 'unpaid',
            'transaction_date' => now()->startOfMonth()->addDays(3),
        ]);

        // Sell transactions
        Transaction::create([
            'type' => 'sell',
            'product_id' => 1,
            'customer_id' => 1,
            'sales_id' => 1,
            'quantity' => 2,
            'price' => 6500000,
            'total' => 13000000,
            'payment_status' => 'paid',
            'transaction_date' => now()->startOfMonth()->addDays(5),
        ]);

        Transaction::create([
            'type' => 'sell',
            'product_id' => 2,
            'customer_id' => 2,
            'sales_id' => 2,
            'quantity' => 10,
            'price' => 200000,
            'total' => 2000000,
            'payment_status' => 'paid',
            'transaction_date' => now()->startOfMonth()->addDays(7),
        ]);

        Transaction::create([
            'type' => 'sell',
            'product_id' => 3,
            'customer_id' => 3,
            'sales_id' => 1,
            'quantity' => 5,
            'price' => 700000,
            'total' => 3500000,
            'payment_status' => 'paid',
            'transaction_date' => now()->startOfMonth()->addDays(10),
        ]);
    }
}
