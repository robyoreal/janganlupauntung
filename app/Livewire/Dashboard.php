<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public function render()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Month to date revenue (from selling)
        $revenue = Transaction::where('type', 'sell')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total');

        // Month to date debt (unpaid buying)
        $debt = Transaction::where('type', 'buy')
            ->where('payment_status', 'unpaid')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('total');

        // Month to date profit (revenue - cost of goods sold)
        $costOfGoodsSold = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.type', 'sell')
            ->whereBetween('transactions.transaction_date', [$startOfMonth, $endOfMonth])
            ->sum(DB::raw('transaction_items.quantity * products.cost_price'));

        $profit = $revenue - $costOfGoodsSold;

        // Top selling products
        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', 'sell')
            ->whereBetween('transactions.transaction_date', [$startOfMonth, $endOfMonth])
            ->select('transaction_items.product_id', DB::raw('SUM(transaction_items.quantity) as total_quantity'), DB::raw('SUM(transaction_items.subtotal) as total_sales'))
            ->groupBy('transaction_items.product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->product = Product::find($item->product_id);
                return $item;
            });

        return view('livewire.dashboard', [
            'revenue' => $revenue,
            'debt' => $debt,
            'profit' => $profit,
            'topProducts' => $topProducts,
        ])->layout('layouts.app');
    }
}
