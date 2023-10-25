<?php

namespace App\Http\Controllers;

// use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Managers\TransactionManager;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    protected $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $transactions = $this->transactionManager->getAllTransactions();

            return response()->json(['transactions' => $transactions]);
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
    public function store(TransactionRequest $request)
    {
        try {
            $validatedData = $request->validated();

            foreach ($validatedData['transaction_requests'] as $transactionRequest) {
                $this->transactionManager->createTransaction($transactionRequest);
            }

            return response()->json(['message' => 'Transaction records created successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
