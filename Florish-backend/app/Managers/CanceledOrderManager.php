<?php

namespace App\Managers;

use App\Models\CanceledOrder;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class CanceledOrderManager
{
    public function cancelOrder(array $canceledOrderData)
    {
        $canceledOrderData['user_id'] = Auth::id();

        $transaction = Transaction::findOrFail($canceledOrderData['transaction_id']);
        $product = $transaction->transactedProduct;
        if ($transaction['quantity'] < $canceledOrderData['quantity']) {
            throw new \Exception('Cannot cancel more products than purchased.');
        } elseif ($transaction['quantity'] == $canceledOrderData['quantity']) {
            $canceledOrderData['total'] = $transaction['total'];

            if ($canceledOrderData['action_taken'] === 'return') {
                $product->incrementStockOnHand($canceledOrderData['quantity']);
            }

            CanceledOrder::create($canceledOrderData);
            $transaction->delete();
        } elseif ($transaction['quantity'] > $canceledOrderData['quantity']) {
            $productPrice = $transaction->transactedProduct->price;
            $canceledTotal = $productPrice * $canceledOrderData['quantity'];

            $canceledOrderData['total'] = $canceledTotal;
            $transaction['quantity'] -= $canceledOrderData['quantity'];

            if ($canceledOrderData['action_taken'] === 'return') {
                $product->incrementStockOnHand($canceledOrderData['quantity']);
            }

            $transaction['total'] -= $canceledTotal;
            CanceledOrder::create($canceledOrderData);
            $transaction->save();
        } else {
            throw new \Exception('Something went wrong.');
        }
    }

    public function getAllCanceledOrders($page, $itemsPerPage)
    {
        return CanceledOrder::with(['canceledTransaction.transactedProduct', 'user'])
            ->paginate($itemsPerPage, ['*'], 'page', $page);
    }
}
