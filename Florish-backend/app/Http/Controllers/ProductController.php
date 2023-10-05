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
            $searchQuery = $request->input('search');
            $query = Product::query();

            if (!empty($searchQuery)) {
                $query->where('description', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('product_code', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('barcode', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('reorder_level', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('stock_on_hand', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('price', 'LIKE', '%' . $searchQuery . '%');
            }

            $products = $query->with(['productType', 'category', 'brand'])->get();

            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    // public function index()
    // {
    //     try {
    //         $products = $this->productManager->getAllProducts();

    //         return response()->json(['products' => $products]);
    //     } catch (\Exception $e) {
    //         $errorMessage = $e->getMessage();

    //         return response()->json(['error' => $errorMessage], 500);
    //     }
    // }

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

    public function getCriticalStock()
    {
        try {
            $criticalProducts = $this->productManager->getCriticalStock();

            return response()->json($criticalProducts);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    public function Search(Request $request)
    {
        try {
            $searchQuery = $request->input('search');
            $query = Product::query();

            if (!empty($searchQuery)) {
                $query->where('description', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('product_code', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('barcode', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('reorder_level', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('stock_on_hand', 'LIKE', '%' . $searchQuery . '%')
                    ->orWhere('price', 'LIKE', '%' . $searchQuery . '%');
            }

            $products = $query->get();

            return response()->json(['products' => $products]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

}
