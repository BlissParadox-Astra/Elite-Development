<?php

namespace App\Managers;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionManager
{
    public function createTransaction(array $transactionRequest)
    {
        $transactionBy = Auth::id();
        $transactionDate = now()->format("Y-m-d H:i:s");

        $product = Product::findOrFail($transactionRequest['product_id']);
        $total = $product->price * $transactionRequest['quantity'];

        $transactionData = [
            'transaction_number' => $transactionRequest['transaction_number'],
            'user_id' => $transactionBy,
            'transaction_date' => $transactionDate,
            'product_id' => $transactionRequest['product_id'],
            'price' => $product->price,
            'quantity' => $transactionRequest['quantity'],
            'total' => $total,
        ];

        $product = Product::findOrFail($transactionRequest['product_id']);
        if ($transactionRequest['quantity'] > $product->stock_on_hand) {
            throw new \Exception('Cannot sell more products than available in stock.');
        } else {
            $product->decrementStockOnHand($transactionRequest['quantity']);
            Transaction::create($transactionData);
            $product->save();
        }
    }

    public function generateInvoiceNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $randomNumber = mt_rand(10, 99);
        return "{$timestamp}{$randomNumber}";
    }

    // public function generateInvoiceNumber(): string
    // {
    //     $timestamp = now()->format('YmdHis');
    //     $lastSequentialNumber = $this->getLastSequentialNumberFromDatabase();
    //     $nextSequentialNumber = $lastSequentialNumber + 1;
    //     $paddedSequentialNumber = str_pad($nextSequentialNumber, 2, '0', STR_PAD_LEFT);
    //     $this->saveSequentialNumberToDatabase($nextSequentialNumber);
    //     $invoiceNumber = "{$timestamp}{$paddedSequentialNumber}";
    //     return $invoiceNumber;
    // }

    // public function getLastSequentialNumberFromDatabase()
    // {
    //     $lastRecord = DB::connection('mysql')
    //         ->table('transactions')
    //         ->orderBy('id', 'desc')
    //         ->first();

    //     if ($lastRecord) {
    //         return $lastRecord->id;
    //     }

    //     return 0;
    // }

    // public function saveSequentialNumberToDatabase($nextSequentialNumber)
    // {
    //     DB::connection('mysql')
    //         ->table('transactions')
    //         ->insert(['id' => $nextSequentialNumber]);
    // }

    public function getAllTransactions($page, $itemsPerPage)
    {
        return Transaction::with(['transactedProduct.category', 'user'])->paginate($itemsPerPage, ['*'], 'page', $page);
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
