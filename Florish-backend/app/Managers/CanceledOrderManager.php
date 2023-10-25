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
        if ($transaction['quantity'] < $canceledOrderData['quantity']) {
            throw new \Exception('Cannot cancel more products than purchased.');
        } elseif ($transaction['quantity'] == $canceledOrderData['quantity']) {
            // Delete related canceled_orders records
            $transaction->canceledOrders()->delete();

            // Check if all quantities have been canceled
            if ($transaction['quantity'] == 0) {
                // If all quantities have been canceled, delete the transaction
                $transaction->delete();
            }
        } elseif ($transaction['quantity'] > $canceledOrderData['quantity']) {
            $transaction['quantity'] -= $canceledOrderData['quantity'];

            // Fetch the product's price
            $productPrice = $transaction->transactedProduct->price;

            // Calculate the total for the canceled quantity
            $canceledTotal = $productPrice * $canceledOrderData['quantity'];

            $transaction['total'] -= $canceledTotal;
            $transaction->save();
        } else {
            throw new \Exception('Something went wrong.');
        }

        CanceledOrder::create($canceledOrderData);
    }

    public function getAllCanceledOrders()
    {
        return CanceledOrder::with(['canceledTransaction', 'user'])->get();
    }
}
