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
                    $query->whereDate('stock_adjustments.adjustment_date', now()->toDateString());
                    break;
                case 'Week':
                    $query->whereBetween('stock_adjustments.adjustment_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'Month':
                    $query->whereYear('stock_adjustments.adjustment_date', now()->year)
                        ->whereMonth('stock_adjustments.adjustment_date', now()->month);
                    break;
                case 'Year':
                    $query->whereYear('stock_adjustments.adjustment_date', now()->year);
                    break;
                case 'Customize':
                    $query->whereBetween('stock_adjustments.adjustment_date', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);
                    break;
                default:
                    $query->whereNull('deleted_at');
                    break;
            }
        } else {
            $query->whereNull('deleted_at');
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
