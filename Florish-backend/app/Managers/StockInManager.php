<?php

namespace App\Managers;

use App\Models\Product;
use App\Models\StockIn;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StockInManager
{
    public function createStockIn(array $stockInRequest)
    {
        $stockInBy = Auth::id();
        $stockInDate = Carbon::parse($stockInRequest['stock_in_date'])->format('Y-m-d');

        StockIn::create([
            'reference_number' => $stockInRequest['reference_number'],
            'stock_in_by' => $stockInBy,
            'stock_in_date' => $stockInDate,
            'product_id' => $stockInRequest['product_id'],
            'quantity_added' => $stockInRequest['quantity_added'],
        ]);

        $product = Product::findOrFail($stockInRequest['product_id']);
        $product->incrementStockOnHand($stockInRequest['quantity_added']);

        $product->save();
    }

    public function generateReferenceNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $randomNumber = mt_rand(100, 999);
        return "{$timestamp}{$randomNumber}";
    }

    public function getAllStockIns($page, $itemsPerPage, $fromDate = null, $toDate = null, $filterType = null)
    {
        $query = StockIn::with(['adjustedProduct.stockIns', 'stockInByUser']);

        if ($filterType) {
            switch ($filterType) {
                case 'Day':
                    $query->whereDate('stock_ins.stock_in_date', now()->toDateString());
                    break;
                case 'Week':
                    $query->whereBetween('stock_ins.stock_in_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'Month':
                    $query->whereYear('stock_ins.stock_in_date', now()->year)
                        ->whereMonth('stock_ins.stock_in_date', now()->month);
                    break;
                case 'Year':
                    $query->whereYear('stock_ins.stock_in_date', now()->year);
                    break;
                case 'Customize':
                    $query->whereBetween('stock_ins.stock_in_date', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);
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
}
