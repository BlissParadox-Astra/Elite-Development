<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Managers\CategoryManager;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    protected $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }
    /**
     * Display a listing of the resource.
     */ public function index(Request $request)
    {
        try {
            $page = $request->input('page', 1); // Get the requested page number from the request
            $itemsPerPage = $request->input('itemsPerPage', 10); // Get the number of items per page from the request

            $categories = $this->categoryManager->getAllCategories($page, $itemsPerPage);

            return response()->json([
                'categories' => $categories->items(),  // Ensure 'categories' property
                'totalItems' => $categories->total(),  // Get the total count
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
    public function store(CategoryRequest $request)
    {
        try {
            $categoryData = $request->all();

            $this->categoryManager->createCategory($categoryData);

            return response()->json(['message' => 'Category created successfully'], Response::HTTP_CREATED);
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
            $category = $this->categoryManager->getCategoryById($id);

            if (!$category) {
                return response()->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['category' => $category], Response::HTTP_OK);
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
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $this->categoryManager->updateCategory($category, $request->validated());

            return response()->json(['message' => 'Category updated successfully']);
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
            $category = Category::findOrFail($id);

            $this->categoryManager->deleteCategory($category);

            return response()->json(['message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return response()->json(['error' => $errorMessage], 500);
        }
    }
}
