<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class Buy extends Component
{
    public $supplier_id, $payment_status = 'paid', $transaction_date, $notes;
    public $items = [];

    protected $rules = [
        'supplier_id' => 'nullable|exists:suppliers,id',
        'payment_status' => 'required|in:paid,unpaid',
        'transaction_date' => 'required|date',
        'notes' => 'nullable|string',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'items.required' => 'Please add at least one item.',
        'items.min' => 'Please add at least one item.',
    ];

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'price' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            if (isset($item['quantity']) && isset($item['price'])) {
                $total += $item['quantity'] * $item['price'];
            }
        }
        return $total;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $total = $this->getTotal();

            // Create transaction
            $transaction = Transaction::create([
                'type' => 'buy',
                'supplier_id' => $this->supplier_id,
                'total' => $total,
                'payment_status' => $this->payment_status,
                'transaction_date' => $this->transaction_date,
                'notes' => $this->notes,
            ]);

            // Create transaction items and update stock
            foreach ($this->items as $item) {
                $subtotal = $item['quantity'] * $item['price'];

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // Update product stock
                $product = Product::find($item['product_id']);
                $product->stock += $item['quantity'];
                $product->save();

                // If paid, update cost price
                if ($this->payment_status === 'paid') {
                    $product->cost_price = $item['price'];
                    $product->save();
                }
            }
        });

        session()->flash('message', 'Purchase recorded successfully. Stock updated.');
        $this->reset(['supplier_id', 'notes', 'items']);
        $this->payment_status = 'paid';
        $this->transaction_date = now()->format('Y-m-d');
        $this->addItem();
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
