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
        $transactionDate = Carbon::parse($transactionRequest['transaction_date'])->format('Y-m-d H:i:s');

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
            'transaction_total' => $transactionRequest['transaction_total'],
            'payment' => $transactionRequest['payment'],
            'change' => $transactionRequest['change'],
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
        $datePart = Carbon::now()->format('YmdHis');
        $randomPart = mt_rand(1000, 9999);

        return "{$datePart}{$randomPart}";
    }

    public function getAllTransactions($page, $itemsPerPage, $fromDate = null, $toDate = null, $filterType = null, $searchQuery = null, $selectedDate = null)
    {
        $query = Transaction::with(['transactedProduct.category', 'user']);

        if (auth()->user()->userType->user_type === 'Cashier') {
            $query->where('user_id', auth()->id());
        }

        if ($filterType) {
            switch ($filterType) {
                case 'Day':
                    $dateToFilter = $selectedDate ?? now()->toDateString();
                    $query->whereDate('transactions.transaction_date', $dateToFilter)
                    ->orderBy('transactions.transaction_date', 'desc');
                    break;

                case 'Week':
                    $startOfWeek = Carbon::parse($selectedDate)->startOfWeek();
                    $endOfWeek = Carbon::parse($selectedDate)->endOfWeek();
                    $query->whereBetween('transactions.transaction_date', [$startOfWeek, $endOfWeek])
                    ->orderBy('transactions.transaction_date', 'desc');
                    break;

                case 'Month':
                    $startOfMonth = Carbon::parse($selectedDate)->startOfMonth();
                    $endOfMonth = Carbon::parse($selectedDate)->endOfMonth();
                    $query->whereBetween('transactions.transaction_date', [$startOfMonth, $endOfMonth])
                    ->orderBy('transactions.transaction_date', 'desc');
                    break;

                case 'Year':
                    $startOfYear = Carbon::parse($selectedDate)->startOfYear();
                    $endOfYear = Carbon::parse($selectedDate)->endOfYear();
                    $query->whereBetween('transactions.transaction_date', [$startOfYear, $endOfYear])
                    ->orderBy('transactions.transaction_date', 'desc');
                    break;

                case 'Customize':
                    $query->whereBetween('transactions.transaction_date', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"])
                    ->orderBy('transactions.transaction_date', 'asc');
                    break;
                default:
                    $query->whereNull('deleted_at')->orderBy('transactions.transaction_date', 'desc');
                    break;
            }
        } else {
            $query->whereNull('deleted_at')->orderBy('transactions.transaction_date', 'desc');
        }

        if ($searchQuery) {
            $query->where(function ($query) use ($searchQuery) {
                $query->where('transaction_number', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhereHas('transactedProduct', function ($query) use ($searchQuery) {
                        $query->where('barcode', 'LIKE', '%' . $searchQuery . '%')
                            ->orWhere('description', 'LIKE', '%' . $searchQuery . '%')
                            ->orWhere('product_code', 'LIKE', '%' . $searchQuery . '%');

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
