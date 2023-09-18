<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Managers\BrandManager;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Response;

class BrandController extends Controller
{
    protected $brandManager;

    public function __construct(BrandManager $brandManager)
    {
        $this->brandManager = $brandManager;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $brands = $this->brandManager->getAllBrands();

            return response()->json(['brands' => $brands], Response::HTTP_OK);
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
    public function store(BrandRequest $request)
    {
        try {
            $brandData = $request->validated();

            $brand = $this->brandManager->createBrand(
                [
                    'brand_name' => $brandData['brand_name'],
                    'category_id' => $brandData['category_id'],
                ]
            );
            if ($brand) {
                return response()->json(['status' => 200, 'message' => 'Brand created successfully'], 200);
            } else {
                return response()->json(['status' => 500, 'message' => 'Brand creation failed'], 500);
            }
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
            $brand = $this->brandManager->getBrandByIdWithRelations($id);

            if (!$brand) {
                return response()->json(['message' => 'Brand not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['brand' => $brand], Response::HTTP_OK);
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
    public function update(BrandRequest $request, Brand $brand)
    {
        try {
            $this->brandManager->updateBrand($brand, $request->validated());

            return response()->json(['message' => 'Brand updated successfully']);
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
            $brand = Brand::findOrFail($id);

            $this->brandManager->deleteBrand($brand);

            return response()->json(['message' => 'Brand deleted successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Show categories from category table.
     */

    public function getCategories()
    {
        try {
            $categories = Category::all();

            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch categories'], 500);
        }
    }
}
