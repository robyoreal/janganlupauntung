<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class Buy extends Component
{
    public $product_id, $supplier_id, $quantity, $price, $payment_status = 'paid', $transaction_date, $notes;

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
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

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $total = $this->quantity * $this->price;

            // Create transaction
            Transaction::create([
                'type' => 'buy',
                'product_id' => $this->product_id,
                'supplier_id' => $this->supplier_id,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'total' => $total,
                'payment_status' => $this->payment_status,
                'transaction_date' => $this->transaction_date,
                'notes' => $this->notes,
            ]);

            // Update product stock
            $product = Product::find($this->product_id);
            $product->stock += $this->quantity;
            $product->save();

            // If paid, update cost price
            if ($this->payment_status === 'paid') {
                $product->cost_price = $this->price;
                $product->save();
            }
        });

        session()->flash('message', 'Purchase recorded successfully. Stock updated.');
        $this->reset(['product_id', 'supplier_id', 'quantity', 'price', 'notes']);
        $this->payment_status = 'paid';
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('livewire.transactions.buy', [
            'products' => $products,
            'suppliers' => $suppliers,
        ])->layout('layouts.app');
    }
}
