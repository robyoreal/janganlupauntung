<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;

class Sell extends Component
{
    public $product_id, $customer_id, $sales_id, $quantity, $price, $payment_status = 'paid', $transaction_date, $notes;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'customer_id' => 'nullable|exists:customers,id',
        'sales_id' => 'nullable|exists:sales,id',
        'quantity' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'payment_status' => 'required|in:paid,unpaid',
        'transaction_date' => 'required|date',
        'notes' => 'nullable|string',
    ];

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function updatedProductId($value)
    {
        if ($value) {
            $product = Product::find($value);
            $this->price = $product->selling_price;
        }
    }

    public function save()
    {
        $this->validate();

        // Check stock availability
        $product = Product::find($this->product_id);
        if ($product->stock < $this->quantity) {
            session()->flash('error', 'Insufficient stock! Available: ' . $product->stock);
            return;
        }

        DB::transaction(function () use ($product) {
            $total = $this->quantity * $this->price;

            // Create transaction
            Transaction::create([
                'type' => 'sell',
                'product_id' => $this->product_id,
                'customer_id' => $this->customer_id,
                'sales_id' => $this->sales_id,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'total' => $total,
                'payment_status' => $this->payment_status,
                'transaction_date' => $this->transaction_date,
                'notes' => $this->notes,
            ]);

            // Update product stock (decrease)
            $product->stock -= $this->quantity;
            $product->save();
        });

        session()->flash('message', 'Sale recorded successfully. Stock updated.');
        $this->reset(['product_id', 'customer_id', 'sales_id', 'quantity', 'price', 'notes']);
        $this->payment_status = 'paid';
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $salesforce = Sales::orderBy('name')->get();

        return view('livewire.transactions.sell', [
            'products' => $products,
            'customers' => $customers,
            'salesforce' => $salesforce,
        ])->layout('layouts.app');
    }
}
