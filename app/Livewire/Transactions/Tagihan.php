<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Tagihan extends Component
{
    use WithPagination;

    // Filter properties
    public $search = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterPaymentStatus = '';

    // Modal properties
    public $showEditModal = false;
    public $showPaymentModal = false;
    public $editingId = null;

    // Form properties
    public $product_id;
    public $supplier_id;
    public $quantity;
    public $price;
    public $payment_status;
    public $transaction_date;
    public $notes;

    protected function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,unpaid',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }

    public function updatingFilterPaymentStatus()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterDateFrom', 'filterDateTo', 'filterPaymentStatus']);
        $this->resetPage();
    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);

        $this->editingId = $transaction->id;
        $this->product_id = $transaction->product_id;
        $this->supplier_id = $transaction->supplier_id;
        $this->quantity = $transaction->quantity;
        $this->price = $transaction->price;
        $this->payment_status = $transaction->payment_status;
        $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        $this->notes = $transaction->notes;

        $this->showEditModal = true;
    }

    public function editPayment($id)
    {
        $transaction = Transaction::findOrFail($id);

        $this->editingId = $transaction->id;
        $this->payment_status = $transaction->payment_status;

        $this->showPaymentModal = true;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $transaction = Transaction::findOrFail($this->editingId);

            // Store old values
            $oldQuantity = $transaction->quantity;
            $oldPaymentStatus = $transaction->payment_status;
            $oldPrice = $transaction->price;

            // Update transaction
            $transaction->update([
                'product_id' => $this->product_id,
                'supplier_id' => $this->supplier_id,
                'quantity' => $this->quantity,
                'price' => $this->price,
                'total' => $this->quantity * $this->price,
                'payment_status' => $this->payment_status,
                'transaction_date' => $this->transaction_date,
                'notes' => $this->notes,
            ]);

            // Update product stock if quantity changed
            if ($oldQuantity != $this->quantity) {
                $product = Product::find($this->product_id);
                $stockDifference = $this->quantity - $oldQuantity;
                $product->increment('stock', $stockDifference);
            }

            // Update cost price if payment status changed to paid or price changed
            if (($oldPaymentStatus === 'unpaid' && $this->payment_status === 'paid') ||
                ($this->payment_status === 'paid' && $oldPrice != $this->price)) {
                $product = Product::find($this->product_id);
                $product->update(['cost_price' => $this->price]);
            }
        });

        session()->flash('message', 'Tagihan berhasil diperbarui.');
        $this->cancelEdit();
    }

    public function savePayment()
    {
        $this->validate([
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        DB::transaction(function () {
            $transaction = Transaction::findOrFail($this->editingId);
            $oldPaymentStatus = $transaction->payment_status;

            $transaction->update([
                'payment_status' => $this->payment_status,
            ]);

            // Update cost price if status changed to paid
            if ($oldPaymentStatus === 'unpaid' && $this->payment_status === 'paid') {
                $product = Product::find($transaction->product_id);
                $product->update(['cost_price' => $transaction->price]);
            }
        });

        session()->flash('message', 'Status pembayaran berhasil diperbarui.');
        $this->cancelPayment();
    }

    public function cancelEdit()
    {
        $this->showEditModal = false;
        $this->reset(['editingId', 'product_id', 'supplier_id', 'quantity', 'price', 'payment_status', 'transaction_date', 'notes']);
    }

    public function cancelPayment()
    {
        $this->showPaymentModal = false;
        $this->reset(['editingId', 'payment_status']);
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);

            // Restore product stock
            $product = Product::find($transaction->product_id);
            $product->decrement('stock', $transaction->quantity);

            $transaction->delete();
        });

        session()->flash('message', 'Tagihan berhasil dihapus.');
    }

    public function render()
    {
        $query = Transaction::with(['product', 'supplier'])
            ->where('type', 'buy');

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('product', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('supplier', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Apply date filter
        if ($this->filterDateFrom) {
            $query->where('transaction_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->where('transaction_date', '<=', $this->filterDateTo);
        }

        // Apply payment status filter
        if ($this->filterPaymentStatus) {
            $query->where('payment_status', $this->filterPaymentStatus);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('livewire.transactions.tagihan', [
            'transactions' => $transactions,
            'products' => $products,
            'suppliers' => $suppliers,
        ])->layout('layouts.app');
    }
}
