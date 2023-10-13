<?php

namespace App\Managers;

use App\Models\Product;
use App\Models\StockIn;
use Illuminate\Support\Facades\Auth;

class StockInManager
{
    public function createStockIn(array $stockInRequest)
    {
        // $referenceNumber = $this->generateReferenceNumber();
        $stockInBy = Auth::id();
        // $stockInDate = now();

        StockIn::create([
            'reference_number' => $stockInRequest['reference_number'],
            'stock_in_by' => $stockInBy,
            'stock_in_date' => $stockInRequest['stock_in_date'],
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

    public function getAllStockIns()
    {
        return StockIn::with(['adjustedProduct', 'stockInByUser'])->get();
    }
}
