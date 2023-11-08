<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Managers\ProductManager;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    protected $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $page = $request->input('page');
            $itemsPerPage = $request->input('itemsPerPage', 10);

            $products = $this->productManager->getAllProducts($page, $itemsPerPage);
            
            return response()->json([
                'products' => $products->items(),
                'totalItems' => $products->total(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Display total stock on hand.
     */

    public function getTotalStockOnHand()
    {
        try {
            $totalStock = $this->productManager->getTotalStockOnHand();
            return response()->json([
                'totalStockOnHand' => $totalStock,
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
    public function store(ProductRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $product = $this->productManager->createProduct($validatedData);

            return response()->json(['message' => 'Product created successfully', 'product' => $product]);
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
        try {
            $product = $this->productManager->getProductByIdWithRelations($id);

            return response()->json(['product' => $product]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
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
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $this->productManager->updateProduct($product, $request->validated());

            return response()->json(['message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);

            $this->productManager->deleteProduct($product);

            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    public function getCategories()
    {
        try {
            $categories = Category::all();

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch categories'], 500);
        }
    }

    public function getCriticalStock(Request $request)
    {
        try {
            $page = $request->input('page');

            $criticalStocks = $this->productManager->getCriticalStock($page);
            return response()->json([
                'criticalStocks' => $criticalStocks->items(),
                'totalItems' => $criticalStocks->total(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    public function getCriticalStockCount()
    {
        try {
            $criticalStockCount = $this->productManager->getCriticalStockCount();
            return response()->json([
                'criticalStockCount' => $criticalStockCount,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            return response()->json(['error' => $errorMessage], 500);
        }
    }
}
