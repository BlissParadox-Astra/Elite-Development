<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Managers\StockAdjustmentManager;
use App\Models\StockAdjustment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
    public function index(Request $request)
    {
        try {
            $page = $request->input('page');
            $itemsPerPage = $request->input('itemsPerPage', 10);
            $fromDate = $request->input('fromDate');
            $toDate = $request->input('toDate');
            $filterType = $request->input('filterType');
            $selectedDate = $request->input('selectedDate');

            $stockAdjustments = $this->stockAdjustmentManager->getAllStockAdjustment($page, $itemsPerPage, $fromDate, $toDate, $filterType, $selectedDate);
            return response()->json([
                'stockAdjustments' => $stockAdjustments->items(),
                'totalItems' => $stockAdjustments->total(),
            ], Response::HTTP_OK);
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

    public function generateReferenceNumber()
    {
        $referenceNumber = $this->stockAdjustmentManager->generateReferenceNumber();

        return response()->json(['reference_number' => $referenceNumber]);
    }
}
