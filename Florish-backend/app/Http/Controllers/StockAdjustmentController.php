<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Managers\StockAdjustmentManager;
use App\Models\StockAdjustment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    protected $stockAdjustmentManager;

    public function __construct(StockAdjustmentManager $stockAdjustmentManager)
    {
        $this->stockAdjustmentManager = $stockAdjustmentManager;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $stockAdjustment = $this->stockAdjustmentManager->getAllStockAdjustment(); // Replace with your manager method

            return response()->json(['stock_adjustments' => $stockAdjustment]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockAdjustmentRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $this->stockAdjustmentManager->createStockAdjustment($validatedData);

            return response()->json(['message' => 'Stock adjustment created successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAdjustment $stockAdjustment)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockAdjustment $stockAdjustment)
    {
        //
    }
}
