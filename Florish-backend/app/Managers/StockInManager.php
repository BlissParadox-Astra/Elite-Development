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
        $datePart = Carbon::now()->format('YmdHis');
        $randomPart = mt_rand(1000, 9999);

        return "{$datePart}{$randomPart}";
    }

    public function getAllStockIns($page, $itemsPerPage, $fromDate = null, $toDate = null, $filterType = null, $selectedDate = null)
    {
        $query = StockIn::with(['adjustedProduct.stockIns', 'stockInByUser']);

        if ($filterType) {
            switch ($filterType) {
                case 'Day':
                    $dateToFilter = $selectedDate ?? now()->toDateString();
                    $query->whereDate('stock_ins.stock_in_date', $dateToFilter);
                    break;

                case 'Week':
                    $startOfWeek = Carbon::parse($selectedDate)->startOfWeek();
                    $endOfWeek = Carbon::parse($selectedDate)->endOfWeek();
                    $query->whereBetween('stock_ins.stock_in_date', [$startOfWeek, $endOfWeek]);
                    break;

                case 'Month':
                    $startOfMonth = Carbon::parse($selectedDate)->startOfMonth();
                    $endOfMonth = Carbon::parse($selectedDate)->endOfMonth();
                    $query->whereBetween('stock_ins.stock_in_date', [$startOfMonth, $endOfMonth]);
                    break;

                case 'Year':
                    $startOfYear = Carbon::parse($selectedDate)->startOfYear();
                    $endOfYear = Carbon::parse($selectedDate)->endOfYear();
                    $query->whereBetween('stock_ins.stock_in_date', [$startOfYear, $endOfYear]);
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
