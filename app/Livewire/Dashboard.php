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
        $costOfGoodsSold = Transaction::where('type', 'sell')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->join('products', 'transactions.product_id', '=', 'products.id')
            ->sum(DB::raw('transactions.quantity * products.cost_price'));

        $profit = $revenue - $costOfGoodsSold;

        // Top selling products
        $topProducts = Transaction::where('type', 'sell')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total) as total_sales'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->with('product')
            ->limit(10)
            ->get();

        return view('livewire.dashboard', [
            'revenue' => $revenue,
            'debt' => $debt,
            'profit' => $profit,
            'topProducts' => $topProducts,
        ])->layout('layouts.app');
    }
}
