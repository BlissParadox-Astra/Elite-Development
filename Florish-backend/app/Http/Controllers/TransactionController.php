<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Managers\TransactionManager;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

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
            $itemsPerPage = $request->input('itemsPerPage', 10);
            $fromDate = $request->input('fromDate');
            $toDate = $request->input('toDate');
            $filterType = $request->input('filterType');
            $searchQuery = $request->input('search');
            // $sortBy = $request->input('sortBy');

            $transactions = $this->transactionManager->getAllTransactions($page, $itemsPerPage, $fromDate, $toDate, $filterType, $searchQuery);

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
                try {
                    $this->transactionManager->createTransaction($transactionRequest);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 400);
                }
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
            ->whereNull('deleted_at')
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
            ->whereNull('deleted_at')
            ->groupBy('month_name')
            ->get();

        return response()->json(['monthlyEarnings' => $monthlyEarnings], 200);
    }

    public function generateInvoiceNumber()
    {
        $invoiceNumber = $this->transactionManager->generateInvoiceNumber();

        return response()->json(['transaction_number' => $invoiceNumber]);
    }

    public function calculateTotalOfAllTotals(Request $request)
    {
        try {
            $fromDate = $request->input('fromDate');
            $toDate = $request->input('toDate');
            $filterType = $request->input('filterType');
            $searchQuery = $request->input('search');

            $query = Transaction::query();

            if (auth()->user()->userType->user_type === 'Cashier') {
                $query->where('user_id', auth()->id());
            }

            if ($filterType) {
                switch ($filterType) {
                    case 'Day':
                        $query->whereDate('transactions.transaction_date', now()->toDateString());
                        break;
                    case 'Week':
                        $query->whereBetween('transactions.transaction_date', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'Month':
                        $query->whereYear('transactions.transaction_date', now()->year)
                            ->whereMonth('transactions.transaction_date', now()->month);
                        break;
                    case 'Year':
                        $query->whereYear('transactions.transaction_date', now()->year);
                        break;
                    case 'Customize':
                        $query->whereBetween('transactions.transaction_date', ["{$fromDate} 00:00:00", "{$toDate} 23:59:59"]);
                        break;
                    default:
                        $query->whereNull('deleted_at');
                        break;
                }
            } else {
                $query->whereNull('deleted_at');
            }

            if ($searchQuery) {
                $query->where(function ($query) use ($searchQuery) {
                    $query->where('transaction_number', 'LIKE', '%' . $searchQuery . '%')
                        ->orWhereHas('transactedProduct', function ($query) use ($searchQuery) {
                            $query->where('barcode', 'LIKE', '%' . $searchQuery . '%')
                                ->orWhere('description', 'LIKE', '%' . $searchQuery . '%');

                            $query->orWhereHas('brand', function ($query) use ($searchQuery) {
                                $query->where('brand_name', 'LIKE', '%' . $searchQuery . '%');
                            })
                                ->orWhereHas('category', function ($query) use ($searchQuery) {
                                    $query->where('category_name', 'LIKE', '%' . $searchQuery . '%');
                                });
                        })
                        ->orWhereHas('user', function ($query) use ($searchQuery) {
                            $query->where('first_name', 'LIKE', '%' . $searchQuery . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $searchQuery . '%');
                        });
                });
            }

            $total = $query->sum('total');

            return response()->json(['total' => $total], Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }
}
