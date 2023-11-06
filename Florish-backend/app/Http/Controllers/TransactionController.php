<?php

namespace App\Http\Controllers;

use App\Managers\TransactionManager;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
    public function index(Request $request)
    {
        try {
            $page = $request->input('page');

            $transactions = $this->transactionManager->getAllTransactions($page);

            return response()->json([
                'transactions' => $transactions->items(),
                'totalItems' => $transactions->total(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Display daily transactions.
     */

    public function dailyTransactions()
    {
        $dailyTransactions = $this->transactionManager->getDailyTransactions();

        return response()->json([
            'dailyTransactions' => $dailyTransactions,
        ], Response::HTTP_OK);
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

    /**
     * Show the yearly earning pie graph.
     */
    public function getTransactionYearsAndEarnings()
    {
        $yearsData = Transaction::selectRaw('YEAR(transaction_date) as year')
            ->selectRaw('SUM(total) as earnings')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return response()->json($yearsData, Response::HTTP_OK);
    }

    public function getMonthlyEarningsForYear(Request $request)
    {
        $currentYear = date('Y');

        $monthlyEarnings = DB::table('transactions')
            ->select(DB::raw('DATE_FORMAT(transaction_date, "%M") as month_name'), DB::raw('SUM(total) as earnings'))
            ->whereYear('transaction_date', $currentYear)
            ->groupBy('month_name')
            ->get();

        return response()->json(['monthlyEarnings' => $monthlyEarnings], 200);
    }
}
