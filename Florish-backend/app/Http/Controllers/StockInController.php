<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockInRequest;
use App\Managers\StockInManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StockInController extends Controller
{
    protected $stockInManager;

    public function __construct(StockInManager $stockInManager)
    {
        $this->stockInManager = $stockInManager;
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

            $stockIns = $this->stockInManager->getAllStockIns($page, $itemsPerPage, $fromDate, $toDate, $filterType);

            return response()->json([
                'stockIns' => $stockIns->items(),
                'totalItems' => $stockIns->total(),
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
    public function store(StockInRequest $request)
    {
        try {
            $validatedData = $request->validated();

            foreach ($validatedData['stock_in_requests'] as $stockInRequest) {
                $this->stockInManager->createStockIn($stockInRequest);
            }

            return response()->json(['message' => 'Stock-In records created successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StockInRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate reference number.
     */
    public function generateReferenceNumber()
    {
        $referenceNumber = $this->stockInManager->generateReferenceNumber();

        return response()->json(['reference_number' => $referenceNumber]);
    }
}
