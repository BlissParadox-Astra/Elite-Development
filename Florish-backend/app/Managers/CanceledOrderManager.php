<?php

namespace App\Managers;

use App\Models\CanceledOrder;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CanceledOrderManager
{
    public function cancelOrder(array $canceledOrderData)
    {
        $canceledOrderData['user_id'] = Auth::id();
        $canceledOrderData['canceled_date'] = $canceledOrderData['canceled_date'] ?? now();

        $transaction = Transaction::findOrFail($canceledOrderData['transaction_id']);
        $product = $transaction->transactedProduct;
        if ($transaction['quantity'] < $canceledOrderData['quantity']) {
            throw new \Exception('Cannot cancel more products than purchased.');
        } elseif ($transaction['quantity'] == $canceledOrderData['quantity']) {
            $canceledOrderData['total'] = $transaction['total'];

            if ($canceledOrderData['action_taken'] === 'Yes') {
                $product->incrementStockOnHand($canceledOrderData['quantity']);
            }

            CanceledOrder::create($canceledOrderData);
            $transaction->delete();
        } elseif ($transaction['quantity'] > $canceledOrderData['quantity']) {
            $productPrice = $transaction->transactedProduct->price;
            $canceledTotal = $productPrice * $canceledOrderData['quantity'];

            $canceledOrderData['total'] = $canceledTotal;
            $transaction['quantity'] -= $canceledOrderData['quantity'];

            if ($canceledOrderData['action_taken'] === 'Yes') {
                $product->incrementStockOnHand($canceledOrderData['quantity']);
            }

            $transaction['total'] -= $canceledTotal;
            CanceledOrder::create($canceledOrderData);
            $transaction->save();
        } else {
            throw new \Exception('Something went wrong.');
        }
    }

    public function getAllCanceledOrders($page, $itemsPerPage, $fromDate = null, $toDate = null, $filterType = null, $selectedDate = null)
    {
        $query = CanceledOrder::with(['canceledTransaction.transactedProduct', 'user']);

        if ($filterType) {
            switch ($filterType) {
                case 'Day':
                    $dateToFilter = $selectedDate ?? now()->toDateString();
                    $query->whereDate('canceled_orders.canceled_date', $dateToFilter)
                    ->orderBy('canceled_orders.canceled_date', 'desc');
                    break;

                case 'Week':
                    $startOfWeek = Carbon::parse($selectedDate)->startOfWeek();
                    $endOfWeek = Carbon::parse($selectedDate)->endOfWeek();
                    $query->whereBetween('canceled_orders.canceled_date', [$startOfWeek, $endOfWeek])
                    ->orderBy('canceled_orders.canceled_date', 'desc');
                    break;

                case 'Month':
                    $startOfMonth = Carbon::parse($selectedDate)->startOfMonth();
                    $endOfMonth = Carbon::parse($selectedDate)->endOfMonth();
                    $query->whereBetween('canceled_orders.canceled_date', [$startOfMonth, $endOfMonth])
                    ->orderBy('canceled_orders.canceled_date', 'desc');
                    break;

                case 'Year':
                    $startOfYear = Carbon::parse($selectedDate)->startOfYear();
                    $endOfYear = Carbon::parse($selectedDate)->endOfYear();
                    $query->whereBetween('canceled_orders.canceled_date', [$startOfYear, $endOfYear])
                    ->orderBy('canceled_orders.canceled_date', 'desc');
                    break;

                case 'Customize':
                    $query->whereBetween('canceled_orders.canceled_date', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"])
                    ->orderBy('canceled_orders.canceled_date', 'asc');
                    break;
                default:
                    $query->whereNull('deleted_at')->orderBy('canceled_orders.canceled_date', 'desc');
                    break;
            }
        } else {
            $query->whereNull('deleted_at')->orderBy('canceled_orders.canceled_date', 'desc');
        }

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }
}
