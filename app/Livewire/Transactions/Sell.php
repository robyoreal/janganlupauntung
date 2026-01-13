<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;

class Sell extends Component
{
    public $customer_id, $sales_id, $payment_status = 'paid', $transaction_date, $notes;
    public $items = [];

    protected $rules = [
        'customer_id' => 'nullable|exists:customers,id',
        'sales_id' => 'nullable|exists:sales,id',
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

    public function updatedItems($value, $key)
    {
        // Auto-populate price when product is selected
        if (str_contains($key, '.product_id')) {
            $index = explode('.', $key)[0];
            $productId = $this->items[$index]['product_id'] ?? null;

            if ($productId) {
                $product = Product::find($productId);
                if ($product) {
                    $this->items[$index]['price'] = $product->selling_price;
                }
            }
        }
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

        // Check stock availability for all items
        foreach ($this->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                session()->flash('error', "Insufficient stock for {$product->name}! Available: {$product->stock}");
                return;
            }
        }

        DB::transaction(function () {
            $total = $this->getTotal();

            // Create transaction
            $transaction = Transaction::create([
                'type' => 'sell',
                'customer_id' => $this->customer_id,
                'sales_id' => $this->sales_id,
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

                // Update product stock (decrease)
                $product = Product::find($item['product_id']);
                $product->stock -= $item['quantity'];
                $product->save();
            }
        });

        session()->flash('message', 'Sale recorded successfully. Stock updated.');
        $this->reset(['customer_id', 'sales_id', 'notes', 'items']);
        $this->payment_status = 'paid';
        $this->transaction_date = now()->format('Y-m-d');
        $this->addItem();
    }

    public function render()
    {
        $products = Product::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $salesforce = Sales::orderBy('name')->get();

        return view('livewire.transactions.sell', [
            'products' => $products,
            'customers' => $customers,
            'salesforce' => $salesforce,
        ])->layout('layouts.app');
    }
}
