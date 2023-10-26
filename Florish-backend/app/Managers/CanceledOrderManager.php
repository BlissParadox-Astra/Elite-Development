<?php

namespace App\Managers;

use App\Models\CanceledOrder;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class CanceledOrderManager
{
    public function cancelOrder(array $canceledOrderData)
    {
        $canceledOrderData['user_id'] = Auth::id();

        $transaction = Transaction::findOrFail($canceledOrderData['transaction_id']);
        $product = Product::findOrFail($transaction['product_id']);
        if ($transaction['quantity'] < $canceledOrderData['quantity']) {
            throw new \Exception('Cannot cancel more products than purchased.');
        } elseif ($transaction['quantity'] == $canceledOrderData['quantity']) {
            if ($canceledOrderData['action_taken'] === 'return') {
                $product->incrementStockOnHand($product['quantity']);
                // $product->stock_on_hand += $canceledOrderData['quantity'];
                CanceledOrder::create($canceledOrderData);
                $transaction->delete();
                // $product->save();
            } elseif ($canceledOrderData['action_taken'] === 'removed') {
                // $product->stock_on_hand -= $canceledOrderData['quantity'];
                CanceledOrder::create($canceledOrderData);
                $transaction->delete();
            } else {
                throw new \Exception('Something went wrong.');
            }
            // CanceledOrder::create($canceledOrderData);

            // $transaction->delete();
        } elseif ($transaction['quantity'] > $canceledOrderData['quantity']) {
            if ($canceledOrderData['action_taken' === 'return']) {
                $product->stock_on_hand += $canceledOrderData['quantity'];
                $transaction['quantity'] -= $canceledOrderData['quantity'];
                $productPrice = $transaction->transactedProduct->price;
                $canceledTotal = $productPrice * $canceledOrderData['quantity'];
                $transaction['total'] -= $canceledTotal;
                $transaction->save();
                CanceledOrder::create($canceledOrderData);
            } elseif ($canceledOrderData['action_taken'] === 'removed') {
                // $product->stock_on_hand -= $canceledOrderData['quantity'];
                $transaction['quantity'] -= $canceledOrderData['quantity'];
                $productPrice = $transaction->transactedProduct->price;
                $canceledTotal = $productPrice * $canceledOrderData['quantity'];
                $transaction['total'] -= $canceledTotal;
                $transaction->save();
                CanceledOrder::create($canceledOrderData);
            } else {
                throw new \Exception('Something went wrong.');
            }
            // $transaction['quantity'] -= $canceledOrderData['quantity'];

            // $productPrice = $transaction->transactedProduct->price;

            // $canceledTotal = $productPrice * $canceledOrderData['quantity'];

            // $transaction['total'] -= $canceledTotal;
            // $transaction->save();
            // CanceledOrder::create($canceledOrderData);
        } else {
            throw new \Exception('Something went wrong.');
        }
    }

    public function getAllCanceledOrders()
    {
        return CanceledOrder::with(['canceledTransaction', 'user'])->get();
    }
}
