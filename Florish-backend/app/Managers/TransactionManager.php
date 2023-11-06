<?php

namespace App\Managers;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionManager
{
    public function createTransaction(array $transactionRequest)
    {
        $transactionBy = Auth::id();
        $transactionDate = now()->format("Y-m-d H:i:s");

        $total = $transactionRequest['price'] * $transactionRequest['quantity'];

        $transactionData = [
            'invoice_number' => $transactionRequest['invoice_number'],
            'user_id' => $transactionBy,
            'transaction_date' => $transactionDate,
            'product_id' => $transactionRequest['product_id'],
            'price' => $transactionRequest['price'],
            'quantity' => $transactionRequest['quantity'],
            'total' => $total,
        ];

        Transaction::create($transactionData);

        $product = Product::findOrFail($transactionRequest['product_id']);
        if ($transactionRequest['quantity'] > $product->stock_on_hand) {
            throw new \Exception('Cannot sell more products than available in stock.');
        } else {
            $product->decrementStockOnHand($transactionRequest['quantity']);
            $product->save();
        }
    }

    public function generateInvoiceNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $randomNumber = mt_rand(100, 999);
        return "{$timestamp}{$randomNumber}";
    }

    public function getAllTransactions($page)
    {
        return Transaction::with(['transactedProduct.category', 'user'])->paginate(3000, ['*'], 'page', $page);
    }

    public function getDailyTransactions()
    {
        $currentDate = Carbon::now();
        $currentDay = $currentDate->toDateString();

        return Transaction::selectRaw('DATE(transaction_date) as date, SUM(total) as total_amount')
            ->whereDate('transaction_date', $currentDay)
            ->groupBy('date')
            ->get();
    }
}
