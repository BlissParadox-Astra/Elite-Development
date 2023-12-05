<?php

namespace App\Managers;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockAdjustmentManager
{
    public function createStockAdjustment(array $stockAdjustmentData)
    {
        $stockAdjustmentData['user'] = Auth::id();
        $stockAdjustmentData['adjustment_date'] = $stockAdjustmentData['adjustment_date'] ?? now();

        $product = Product::findOrFail($stockAdjustmentData['product_id']);
        if ($stockAdjustmentData['action'] === 'Add to inventory') {
            $product->stock_on_hand += $stockAdjustmentData['quantity'];
        } elseif ($stockAdjustmentData['action'] === 'Remove from inventory') {
            if ($product->stock_on_hand < $stockAdjustmentData['quantity']) {
                throw new \Exception('Cannot remove more products than available in stock.');
            }
            $product->stock_on_hand -= $stockAdjustmentData['quantity'];
        }

        StockAdjustment::create($stockAdjustmentData);
        $product->save();
    }

    public function getAllStockAdjustment($page, $itemsPerPage,  $fromDate = null, $toDate = null, $filterType = null, $selectedDate = null)
    {
        $query = StockAdjustment::with(['stockAdjustmentByUser', 'adjustedProduct']);

        if ($filterType) {
            switch ($filterType) {
                case 'Day':
                    $dateToFilter = $selectedDate ?? now()->toDateString();
                    $query->whereDate('stock_adjustments.adjustment_date', $dateToFilter);
                    break;

                case 'Week':
                    $startOfWeek = Carbon::parse($selectedDate)->startOfWeek();
                    $endOfWeek = Carbon::parse($selectedDate)->endOfWeek();
                    $query->whereBetween('stock_adjustments.adjustment_date', [$startOfWeek, $endOfWeek]);
                    break;

                case 'Month':
                    $startOfMonth = Carbon::parse($selectedDate)->startOfMonth();
                    $endOfMonth = Carbon::parse($selectedDate)->endOfMonth();
                    $query->whereBetween('stock_adjustments.adjustment_date', [$startOfMonth, $endOfMonth]);
                    break;

                case 'Year':
                    $startOfYear = Carbon::parse($selectedDate)->startOfYear();
                    $endOfYear = Carbon::parse($selectedDate)->endOfYear();
                    $query->whereBetween('stock_adjustments.adjustment_date', [$startOfYear, $endOfYear]);
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
