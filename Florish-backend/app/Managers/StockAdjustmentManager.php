<?php

namespace App\Managers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentManager
{
    public function createStockAdjustment(array $stockAdjustmentData)
    {
        $stockAdjustmentData['user'] = Auth::id();

        $product = Product::findOrFail($stockAdjustmentData['product_id']);
        if ($stockAdjustmentData['action'] === 'Add to Inventory') {
            $product->stock_on_hand += $stockAdjustmentData['quantity'];
        } elseif ($stockAdjustmentData['action'] === 'Remove From Inventory') {
            if ($product->stock_on_hand < $stockAdjustmentData['quantity']) {
                throw new \Exception('Cannot remove more products than available in stock.');
            }
            $product->stock_on_hand -= $stockAdjustmentData['quantity'];
        }

        $product->save();

        StockAdjustment::create($stockAdjustmentData);
    }

    public function getAllStockAdjustment($page, $itemsPerPage)
    {
        return StockAdjustment::with(['stockAdjustmentByUser', 'adjustedProduct'])->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function generateReferenceNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $randomNumber = mt_rand(1000, 9999);
        return "{$timestamp}{$randomNumber}";
    }
}
