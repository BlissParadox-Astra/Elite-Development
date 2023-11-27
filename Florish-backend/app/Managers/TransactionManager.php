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
        $transactionDate = Carbon::now()->format("Y-m-d H:i:s");

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
        $timestamp = Carbon::now()->format('YmdHis');
        $randomNumber = mt_rand(10, 99);
        return "{$timestamp}{$randomNumber}";
    }

    public function getAllTransactions($page, $itemsPerPage, $fromDate = null, $toDate = null, $filterType = null, $searchQuery = null)
    {
        $query = Transaction::with(['transactedProduct.category', 'user']);

        if (auth()->user()->userType->user_type === 'Cashier') {
            $query->where('user_id', auth()->id());
        }

        if ($filterType) {
            switch ($filterType) {
                case 'Day':
                    $query->whereDate('transactions.transaction_date', now()->toDateString());
                    break;
                case 'Week':
                    $query->whereBetween('transactions.transaction_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'Month':
                    $query->whereYear('transactions.transaction_date', now()->year)
                        ->whereMonth('transactions.transaction_date', now()->month);
                    break;
                case 'Year':
                    $query->whereYear('transactions.transaction_date', now()->year);
                    break;
                case 'Customize':
                    $query->whereBetween('transactions.transaction_date', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);
                    break;
                default:
                    $query->whereNull('deleted_at');
                    break;
            }
        } else {
            $query->whereNull('deleted_at');
        }

        if ($searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                $query->where('transaction_number', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhereHas('transactedProduct', function ($query) use ($searchQuery) {
                        $query->where('barcode', 'LIKE', '%' . $searchQuery . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchQuery . '%');

                        $query->orWhereHas('brand', function ($query) use ($searchQuery) {
                            $query->where('brand_name', 'LIKE', '%' . $searchQuery . '%');
                        })
                            ->orWhereHas('category', function ($query) use ($searchQuery) {
                                $query->where('category_name', 'LIKE', '%' . $searchQuery . '%');
                            });
                    })
                    ->orWhereHas('user', function ($query) use ($searchQuery) {
                        $query->where('first_name', 'LIKE', '%' . $searchQuery . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $searchQuery . '%');
                    });
            });
        }

        // if ($sortBy) {
        //     switch ($sortBy) {
        //         case 'Category':
        //             $query->join('products', 'transactions.product_id', '=', 'products.id')
        //                 ->join('categories', 'products.category_id', '=', 'categories.id')
        //                 ->orderBy('categories.category_name', 'asc');
        //             break;
        //         case 'Total':
        //             $query->orderBy('total', 'desc');
        //             break;
        //         case 'Alphabetically':
        //             $query->join('products', 'transactions.product_id', '=', 'products.id')
        //                 ->orderBy('products.description', 'asc');
        //             break;
        //         default:
        //             $query->orderBy('total', 'asc');
        //             break;
        //     }
        // }

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
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
