<?php

namespace App\Managers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductManager
{
    public function createProduct(array $productData)
    {
        $productData['stock_on_hand'] = 0;

        $productData['product_type_id'] = 1;
        
        $categoryId = $productData['category_id'];
        $productData['product_code'] = $this->generateProductCode($categoryId);
        return Product::create($productData);
    }

    public function getAllProducts($page = 1, $itemsPerPage = 10)
    {
        return Product::with(['productType', 'category', 'brand'])->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function getProductByIdWithRelations($id)
    {
        return Product::with(['productType', 'category', 'brand'])->findOrFail($id);
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->update($data);
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
    }

    public function generateProductCode($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $categoryCode = $category->category_code;

        $latestProduct = Product::where('category_id', $categoryId)->latest('id')->first();
        $newCodeNumber = ($latestProduct) ? intval(substr($latestProduct->product_code, -4)) + 1 : 1;
        $newProductCode = $categoryCode . '-' . str_pad($newCodeNumber, 4, '0', STR_PAD_LEFT);

        return $newProductCode;
    }

    public function getCriticalStock($page = 1, $itemsPerPage = 10)
    {
        return Product::with(['category', 'brand'])->where('stock_on_hand', '<=', DB::raw('reorder_level'))->paginate($itemsPerPage, ['*'], 'page', $page);
    }
}
