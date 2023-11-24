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
        $stockAdjustmentData['adjustment_date'] = $stockAdjustmentData['adjustment_date'] ?? now();

        $product = Product::findOrFail($stockAdjustmentData['product_id']);
        if ($stockAdjustmentData['action'] === 'Add to Inventory') {
            $product->stock_on_hand += $stockAdjustmentData['quantity'];
        } elseif ($stockAdjustmentData['action'] === 'Remove From Inventory') {
            if ($product->stock_on_hand < $stockAdjustmentData['quantity']) {
                throw new \Exception('Cannot remove more products than available in stock.');
            }
            $product->stock_on_hand -= $stockAdjustmentData['quantity'];
        }

        StockAdjustment::create($stockAdjustmentData);
        $product->save();
    }

    public function getAllStockAdjustment($page, $itemsPerPage,  $fromDate = null, $toDate = null, $filterType = null)
    {
        $query = StockAdjustment::with(['stockAdjustmentByUser', 'adjustedProduct']);

        if ($filterType) {
            switch ($filterType) {
                case 'Day':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'Week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'Month':
                    $query->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month);
                    break;
                case 'Year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'Customize':
                    $query->whereBetween('created_at', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);
                    break;
                default:
                    $query->whereYear('created_at', now()->year);
                    break;
            } // end switch
        } else {
            $query->whereYear('created_at', now()->year);
        }

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function generateReferenceNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $randomNumber = mt_rand(1000, 9999);
        return "{$timestamp}{$randomNumber}";
    }
}
