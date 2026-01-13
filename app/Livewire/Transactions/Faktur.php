<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use App\Models\TransactionItem;
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
    public $showViewModal = false;
    public $showPaymentModal = false;
    public $viewingId = null;
    public $editingId = null;

    // Form properties
    public $payment_status;

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

    public function view($id)
    {
        $this->viewingId = $id;
        $this->showViewModal = true;
    }

    public function closeView()
    {
        $this->showViewModal = false;
        $this->reset(['viewingId']);
    }

    public function editPayment($id)
    {
        $transaction = Transaction::findOrFail($id);

        $this->editingId = $transaction->id;
        $this->payment_status = $transaction->payment_status;

        $this->showPaymentModal = true;
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

    public function cancelPayment()
    {
        $this->showPaymentModal = false;
        $this->reset(['editingId', 'payment_status']);
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $transaction = Transaction::with('items')->findOrFail($id);

            // Restore product stock for all items
            foreach ($transaction->items as $item) {
                $product = Product::find($item->product_id);
                $product->increment('stock', $item->quantity);
            }

            $transaction->delete();
        });

        session()->flash('message', 'Faktur berhasil dihapus dan stok dikembalikan.');
    }

    public function render()
    {
        $query = Transaction::with(['items.product', 'customer', 'sales'])
            ->where('type', 'sell');

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('items.product', function($q) {
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

        $viewingTransaction = null;
        if ($this->viewingId) {
            $viewingTransaction = Transaction::with(['items.product', 'customer', 'sales'])
                ->findOrFail($this->viewingId);
        }

        return view('livewire.transactions.faktur', [
            'transactions' => $transactions,
            'viewingTransaction' => $viewingTransaction,
        ])->layout('layouts.app');
    }
}
