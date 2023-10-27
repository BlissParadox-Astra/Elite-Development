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
                // Add the canceled quantity back to the product's stock on hand
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
                // Add the canceled quantity back to the product's stock on hand
                $product->incrementStockOnHand($canceledOrderData['quantity']);
            }

            $transaction['total'] -= $canceledTotal;
            $transaction->save();
            CanceledOrder::create($canceledOrderData);
        } else {
            throw new \Exception('Something went wrong.');
        }
    }

    public function getAllCanceledOrders()
    {
        return CanceledOrder::with(['canceledTransaction', 'user'])->get();
    }
}
