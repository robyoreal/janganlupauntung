<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sales;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Faktur extends Component
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
    public $customer_id;
    public $sales_id;
    public $quantity;
    public $price;
    public $payment_status;
    public $transaction_date;
    public $notes;

    protected function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'nullable|exists:customers,id',
            'sales_id' => 'nullable|exists:sales,id',
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
        $this->customer_id = $transaction->customer_id;
        $this->sales_id = $transaction->sales_id;
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

            // Update transaction
            $transaction->update([
                'product_id' => $this->product_id,
                'customer_id' => $this->customer_id,
                'sales_id' => $this->sales_id,
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
                $stockDifference = $oldQuantity - $this->quantity; // For sell, subtract from stock
                $product->increment('stock', $stockDifference);
            }
        });

        session()->flash('message', 'Faktur berhasil diperbarui.');
        $this->cancelEdit();
    }

    public function savePayment()
    {
        $this->validate([
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        $transaction = Transaction::findOrFail($this->editingId);
        $transaction->update([
            'payment_status' => $this->payment_status,
        ]);

        session()->flash('message', 'Status pembayaran berhasil diperbarui.');
        $this->cancelPayment();
    }

    public function cancelEdit()
    {
        $this->showEditModal = false;
        $this->reset(['editingId', 'product_id', 'customer_id', 'sales_id', 'quantity', 'price', 'payment_status', 'transaction_date', 'notes']);
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
            $product->increment('stock', $transaction->quantity);

            $transaction->delete();
        });

        session()->flash('message', 'Faktur berhasil dihapus.');
    }

    public function render()
    {
        $query = Transaction::with(['product', 'customer', 'sales'])
            ->where('type', 'sell');

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('product', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('customer', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('sales', function($q) {
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
        $customers = Customer::orderBy('name')->get();
        $salesForce = Sales::orderBy('name')->get();

        return view('livewire.transactions.faktur', [
            'transactions' => $transactions,
            'products' => $products,
            'customers' => $customers,
            'salesForce' => $salesForce,
        ]);
    }
}
