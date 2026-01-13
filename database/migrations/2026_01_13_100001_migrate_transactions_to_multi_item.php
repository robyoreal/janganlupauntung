<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Migrate existing transactions to transaction_items
        $transactions = DB::table('transactions')->get();

        foreach ($transactions as $transaction) {
            DB::table('transaction_items')->insert([
                'transaction_id' => $transaction->id,
                'product_id' => $transaction->product_id,
                'quantity' => $transaction->quantity,
                'price' => $transaction->price,
                'subtotal' => $transaction->quantity * $transaction->price,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
            ]);
        }

        // Step 2: Modify transactions table - remove product-specific columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['product_id', 'quantity', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the columns
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->decimal('price', 12, 2)->nullable();
        });

        // Migrate data back (only for single-item transactions)
        $transactions = DB::table('transactions')->get();

        foreach ($transactions as $transaction) {
            $firstItem = DB::table('transaction_items')
                ->where('transaction_id', $transaction->id)
                ->first();

            if ($firstItem) {
                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'product_id' => $firstItem->product_id,
                        'quantity' => $firstItem->quantity,
                        'price' => $firstItem->price,
                    ]);
            }
        }
    }
};
