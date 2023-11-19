<?php

namespace App\Http\Controllers;

use App\Models\CanceledOrder;
use Illuminate\Http\Request;
use App\Http\Requests\CanceledOrderRequest;
use App\Managers\CanceledOrderManager;
use Illuminate\Http\Response;

class CanceledOrderController extends Controller
{
    protected $canceledOrderManager;

    public function __construct(CanceledOrderManager $canceledOrderManager)
    {
        $this->canceledOrderManager = $canceledOrderManager;
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

            $canceledOrders = $this->canceledOrderManager->getAllCanceledOrders($page, $itemsPerPage, $fromDate, $toDate);

            return response()->json([
                'canceled_orders' => $canceledOrders->items(),
                'totalItems' => $canceledOrders->total(),
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
    public function store(CanceledOrderRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $this->canceledOrderManager->cancelOrder($validatedData);

            return response()->json(['message' => 'Canceled order created successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CanceledOrder $canceledOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CanceledOrder $canceledOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CanceledOrder $canceledOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CanceledOrder $canceledOrder)
    {
        //
    }
}
